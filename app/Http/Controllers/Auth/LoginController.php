<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // First, attempt to log in as an admission user
        if (Auth::guard('admission')->attempt([
            'username' => $request->username,
            'password' => $request->password,
        ], $request->boolean('remember'))) {
            // Check if employee is active before allowing login
            $admissionUser = Auth::guard('admission')->user();
            if ($admissionUser && $admissionUser->employee) {
                if (strtolower($admissionUser->employee->status) !== 'active') {
                    Auth::guard('admission')->logout();
                    return back()->withErrors([
                        'username' => 'Your account is inactive. Please contact the system administrator.',
                    ])->withInput($request->only('username'));
                }
            }
            
            $request->session()->regenerate();
            return redirect()->intended('/admission/dashboard');
        }

        // If admission login fails, attempt to log in as an applicant
        if (Auth::guard('applicant')->attempt([
            'username' => $request->username,
            'password' => $request->password,
        ], $request->boolean('remember'))) {
            // Check account status before allowing login
            $applicantUser = Auth::guard('applicant')->user();
            if ($applicantUser && strtolower($applicantUser->account_status) !== 'active') {
                Auth::guard('applicant')->logout();
                return back()->withErrors([
                    'username' => 'Your account is disabled. Please contact the system administrator.',
                ])->withInput($request->only('username'));
            }
            
            $request->session()->regenerate();
            
            // Check if profile is complete
            $applicant = $applicantUser->applicant;
            
            // Check required profile fields
            $requiredFields = [
                'first_name', 'last_name', 'birth_date', 'place_of_birth', 'sex',
                'civil_status', 'email', 'contact_number', 'barangay', 'municipality',
                'province', 'last_school_attended', 'school_address', 'year_graduated',
                'gen_average', 'preferred_course_1', 'preferred_course_2', 'preferred_course_3',
            ];
            
            foreach ($requiredFields as $field) {
                if (empty($applicant->$field)) {
                    return redirect()->route('applicant.profile.show')->with('info', 'Please complete your profile to continue.');
                }
            }
            
            // Check if declaration is complete
            $declaration = $applicant->declaration;
            if (!$declaration) {
                return redirect()->route('applicant.declaration.edit')->with('info', 'Please complete your declaration to continue.');
            }
            
            $requiredDeclarationFields = ['physical_condition_flag', 'disciplinary_action_flag', 'certified_signature_name', 'certified_date'];
            foreach ($requiredDeclarationFields as $field) {
                if (!isset($declaration->$field)) {
                    return redirect()->route('applicant.declaration.edit')->with('info', 'Please complete your declaration to continue.');
                }
            }
            
            // Check conditional fields
            if ($declaration->physical_condition_flag && empty($declaration->physical_condition_desc)) {
                return redirect()->route('applicant.declaration.edit')->with('info', 'Please complete your declaration to continue.');
            }
            
            if ($declaration->disciplinary_action_flag && empty($declaration->disciplinary_action_desc)) {
                return redirect()->route('applicant.declaration.edit')->with('info', 'Please complete your declaration to continue.');
            }
            
            // Profile is complete, redirect to dashboard
            return redirect()->intended('/applicant/dashboard');
        }

        // If both attempts fail, redirect back with error
        return back()->withErrors([
            'username' => 'Invalid credentials.',
        ])->withInput($request->only('username'));
    }

    /**
     * Handle a logout request.
     * Only logs out the guard that made the request, preserving the other guard's session.
     */
    public function logout(Request $request)
    {
        // Attempt to revoke HRMS token BEFORE clearing session
        $this->revokeHRMSToken();

        // Determine which guard is active based on the referrer
        $referer = $request->headers->get('referer', '');
        $isAdmission = str_contains($referer, '/admission/');
        $isApplicant = str_contains($referer, '/applicant/');

        // Log out only the active guard
        if ($isAdmission && Auth::guard('admission')->check()) {
            Auth::guard('admission')->logout();
        } elseif ($isApplicant && Auth::guard('applicant')->check()) {
            Auth::guard('applicant')->logout();
        } else {
            // Fallback: log out whichever guard is active
            if (Auth::guard('admission')->check()) {
                Auth::guard('admission')->logout();
            } elseif (Auth::guard('applicant')->check()) {
                Auth::guard('applicant')->logout();
            }
        }

        // Regenerate CSRF token (shared session)
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Attempt to revoke HRMS access token on logout.
     * Errors are logged but do not block logout.
     */
    private function revokeHRMSToken()
    {
        try {
            // Read token from session
            $token = session('hrms_access_token');

            if (empty($token)) {
                // No token to revoke, skip
                return;
            }

            // Attempt to revoke token on HRMS
            $response = Http::post(
                config('services.oauth.provider_url') . '/oauth/revoke',
                [
                    'token' => $token,
                    'client_id' => config('services.oauth.client_id'),
                    'client_secret' => config('services.oauth.client_secret'),
                ]
            );

            // Log the result for debugging
            Log::info('HRMS token revocation', [
                'status' => $response->status(),
                'success' => $response->successful(),
            ]);
        } catch (\Exception $e) {
            // Log but do not block logout - HRMS failure is not critical
            Log::warning('HRMS token revocation failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}

