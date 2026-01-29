<?php

namespace App\Http\Controllers\Admission;

use App\Http\Controllers\Controller;
use App\Models\AdmissionUser;
use App\Models\Employee;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class HRMSOAuthController extends Controller
{
    /**
     * Redirect to HRMS OAuth provider.
     */
    public function redirectToHR()
    {
        // Clear any stale session data from previous authorization attempts
        Auth::logout();
        Session::invalidate();
        Session::regenerateToken();

        $state = Str::random(40);
        session(['oauth_state' => $state]);

        $query = http_build_query([
            'client_id' => config('services.oauth.client_id'),
            'redirect_uri' => config('services.oauth.redirect_uri'),
            'response_type' => 'code',
            'scope' => 'openid profile email',
            'state' => $state,
        ]);

        return redirect(rtrim(config('services.oauth.provider_url'), '/') . '/oauth/authorize?' . $query);
    }

    /**
     * Handle OAuth callback from HRMS.
     */
    public function handleHRCallback(Request $request)
    {
        // Validate state parameter
        $state = $request->query('state');
        if (!$state || $state !== session('oauth_state')) {
            return redirect('/login')->with('error', 'Invalid OAuth state. Please try again.');
        }

        $code = $request->query('code');
        if (!$code) {
            return redirect('/login')->with('error', 'No authorization code received. Please try again.');
        }

        try {
            // Exchange code for token
            $tokenResponse = Http::post(config('services.oauth.provider_url') . '/oauth/token', [
                'grant_type' => 'authorization_code',
                'client_id' => config('services.oauth.client_id'),
                'client_secret' => config('services.oauth.client_secret'),
                'code' => $code,
                'redirect_uri' => config('services.oauth.redirect_uri'),
            ]);

            if ($tokenResponse->failed()) {
                return redirect('/login')->with('error', 'Failed to exchange authorization code. Please try again.');
            }
            
            #dd($tokenResponse->json(), $tokenResponse->status());

            $token = $tokenResponse->json('access_token');

            // Store token in session for logout revocation
            session(['hrms_access_token' => $token]);

            // Fetch user info
            $userInfoResponse = Http::withToken($token)->get(
                config('services.oauth.provider_url') . '/oauth/userinfo'
            );

            #dd($userInfoResponse->json(), $userInfoResponse->status());

            if ($userInfoResponse->failed()) {
                return redirect('/login')->with('error', 'Failed to fetch user information. Please try again.');
            }

            $userInfo = $userInfoResponse->json();

            // ==================== AUTHORIZATION CHECK ====================
            // Department must contain 'admission', 'admission office', 'admissions', or 'office of admission'
            $department = strtolower(trim($userInfo['unit'] ?? ''));

            $allowedDepartments = [
                'admission services office',
                'admission',
                'admission office',
                'admissions',
                'office of admission',
            ];

            $isAuthorized = false;
            foreach ($allowedDepartments as $allowed) {
                if (strpos($department, $allowed) !== false) {
                    $isAuthorized = true;
                    break;
                }
            }

            if (!$isAuthorized) {
                // Clear session for unauthorized user
                Auth::logout();
                Session::invalidate();
                Session::regenerateToken();
                
                return redirect('/login')->with('error', 'Your department is not authorized to access this system. Contact the administrator.');
            }

            // ==================== EMPLOYEE CREATION / FETCH ====================
            $firstName = $userInfo['first_name'] ?? '';
            $lastName = $userInfo['last_name'] ?? '';

            if (!$firstName || !$lastName) {
                return redirect('/login')->with('error', 'Invalid user information from HRMS. Missing name fields.');
            }

            // Find or create Employee
            $employee = Employee::whereRaw(
                'LOWER(first_name) = ? AND LOWER(last_name) = ?',
                [strtolower($firstName), strtolower($lastName)]
            )->first();

            if (!$employee) {
                // Create new employee
                // Get Admission Office department
                $admissionDept = Department::whereRaw(
                    'LOWER(department_name) LIKE ?',
                    ['%admission%']
                )->first();

                $departmentId = $admissionDept?->department_id ?? 1;

                $employee = Employee::create([
                    'first_name' => $firstName,
                    'middle_name' => $userInfo['middle_name'] ?? null,
                    'last_name' => $lastName,
                    'department_id' => $departmentId,
                    'status' => 'active',
                ]);
            }

            // ==================== ADMISSION USER CREATION / FETCH ====================
            $admissionUser = AdmissionUser::where('employee_id', $employee->employee_id)->first();

            if (!$admissionUser) {
                // Auto-create admission user with username + password
                $username = $this->generateUniqueUsername($firstName, $lastName);
                $plainPassword = Str::random(5);

                $admissionUser = AdmissionUser::create([
                    'employee_id' => $employee->employee_id,
                    'username' => $username,
                    'password' => bcrypt($plainPassword),
                    'plain_password' => $plainPassword,
                    'role' => 'Staff',
                    'account_status' => 'active',
                ]);
            }

            // ==================== LOGIN ====================
            Auth::guard('admission')->login($admissionUser);

            return redirect('/admission/dashboard')->with('success', 'Logged in via HRMS SSO');
        } catch (\Exception $e) {
            \Log::error('HRMS OAuth Error: ' . $e->getMessage());
            return redirect('/login')->with('error', 'An error occurred during login. Please try again.');
        }
    }

    /**
     * Generate unique username in format: lastname.firstname (with numeric suffix if duplicate).
     */
    private function generateUniqueUsername(string $firstName, string $lastName): string
    {
        $baseUsername = strtolower(str_replace(' ', '', $lastName) . '.' . str_replace(' ', '', $firstName));

        // Check if username exists
        if (!AdmissionUser::where('username', $baseUsername)->exists()) {
            return $baseUsername;
        }

        // Append numeric suffix
        $counter = 2;
        while (AdmissionUser::where('username', $baseUsername . $counter)->exists()) {
            $counter++;
        }

        return $baseUsername . $counter;
    }
}
