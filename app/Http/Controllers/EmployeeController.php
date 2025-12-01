<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\AdmissionUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees.
     */
    public function index(Request $request)
    {
        // Check if user is Admin or Staff
        $user = Auth::guard('admission')->user();
        if (!$user || !in_array($user->role, ['Admin', 'Staff'])) {
            abort(403, 'Unauthorized access.');
        }

        $search = $request->query('search');
        $filter = $request->query('filter');

        $employees = Employee::with(['department', 'admissionUser'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'ilike', "%{$search}%")
                      ->orWhere('last_name', 'ilike', "%{$search}%")
                      ->orWhereRaw("CONCAT(first_name, ' ', last_name) ILIKE ?", ["%{$search}%"])
                      ->orWhereHas('department', function ($deptQuery) use ($search) {
                          $deptQuery->where('department_name', 'ilike', "%{$search}%");
                      });
                });
            })
            ->when($filter === 'has_account', function ($query) {
                $query->whereHas('admissionUser');
            })
            ->when($filter === 'no_account', function ($query) {
                $query->whereDoesntHave('admissionUser');
            })
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(20)
            ->withQueryString();

        return view('admission.employees.index', compact('employees'));
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee)
    {
        // Check if user is Admin or Staff
        $user = Auth::guard('admission')->user();
        if (!$user || !in_array($user->role, ['Admin', 'Staff'])) {
            abort(403, 'Unauthorized access.');
        }

        $employee->load(['department', 'admissionUser']);

        return view('admission.employees.show', compact('employee'));
    }

    /**
     * Create login account for employee.
     */
    public function createAccount(Employee $employee, Request $request)
    {
        // Only Admin can create accounts
        $user = Auth::guard('admission')->user();
        if (!$user || $user->role !== 'Admin') {
            abort(403, 'Only Admin can create login accounts.');
        }

        // Check if employee is active
        if (strtolower($employee->status) !== 'active') {
            return back()->with('error', 'Cannot create an account for an inactive employee.');
        }

        // Check if account already exists
        if ($employee->admissionUser) {
            return back()->with('error', 'Employee already has a login account.');
        }

        // Generate username: lastname.firstname (lowercase, no spaces)
        $lastName = strtolower(preg_replace('/\s+/', '', $employee->last_name));
        $firstName = strtolower(preg_replace('/\s+/', '', $employee->first_name));
        $baseUsername = $lastName . '.' . $firstName;

        // Ensure uniqueness
        $username = $baseUsername;
        $counter = 2;
        while (AdmissionUser::where('username', $username)->exists()) {
            $username = $baseUsername . '-' . $counter;
            $counter++;
        }

        // Generate password using unified generator
        $password = generateRandomPassword();
        $hashedPassword = Hash::make($password);

        // Create admission user
        $admissionUser = AdmissionUser::create([
            'employee_id' => $employee->employee_id,
            'username' => $username,
            'password' => $hashedPassword,
            'plain_password' => $password,
            'role' => 'Staff', // Default role
        ]);

        return back()->with('success', "Login account created successfully.\nUsername: {$username}\nPassword: {$password}");
    }

    /**
     * Reset username for employee.
     */
    public function resetUsername(Employee $employee, Request $request)
    {
        // Only Admin can reset usernames
        $user = Auth::guard('admission')->user();
        if (!$user || $user->role !== 'Admin') {
            abort(403, 'Only Admin can reset usernames.');
        }

        if (!$employee->admissionUser) {
            return back()->with('error', 'Employee does not have a login account.');
        }

        // Generate new username
        $lastName = strtolower(preg_replace('/\s+/', '', $employee->last_name));
        $firstName = strtolower(preg_replace('/\s+/', '', $employee->first_name));
        $baseUsername = $lastName . '.' . $firstName;

        // Ensure uniqueness (exclude current username)
        $username = $baseUsername;
        $counter = 2;
        while (AdmissionUser::where('username', $username)
            ->where('admission_user_id', '!=', $employee->admissionUser->admission_user_id)
            ->exists()) {
            $username = $baseUsername . '-' . $counter;
            $counter++;
        }

        $employee->admissionUser->update(['username' => $username]);

        return back()->with('success', "Username reset successfully.\nNew Username: {$username}");
    }

    /**
     * Reset password for employee.
     */
    public function resetPassword(Employee $employee, Request $request)
    {
        // Only Admin can reset passwords
        $user = Auth::guard('admission')->user();
        if (!$user || $user->role !== 'Admin') {
            abort(403, 'Only Admin can reset passwords.');
        }

        if (!$employee->admissionUser) {
            return back()->with('error', 'Employee does not have a login account.');
        }

        // Generate new password using unified generator
        $password = generateRandomPassword();
        $hashedPassword = Hash::make($password);

        $employee->admissionUser->update([
            'password' => $hashedPassword,
            'plain_password' => $password,
        ]);

        return back()->with('success', "Password reset successfully.\nNew Password: {$password}");
    }

    /**
     * Toggle account status (disable/enable).
     */
    public function toggleAccountStatus(Employee $employee, Request $request)
    {
        // Only Admin can toggle account status
        $user = Auth::guard('admission')->user();
        if (!$user || $user->role !== 'Admin') {
            abort(403, 'Only Admin can disable/enable accounts.');
        }

        if (!$employee->admissionUser) {
            return back()->with('error', 'Employee does not have a login account.');
        }

        // Prevent admin from disabling their own account
        if ($employee->admissionUser->admission_user_id === $user->admission_user_id) {
            return back()->with('error', 'You cannot disable your own account.');
        }

        try {
            // Toggle between active and disabled
            $currentStatus = $employee->admissionUser->account_status ?? 'active';
            $newStatus = $currentStatus === 'active' ? 'disabled' : 'active';

            $employee->admissionUser->update(['account_status' => $newStatus]);

            $statusText = $newStatus === 'active' ? 'enabled' : 'disabled';
            return back()->with('success', "Account {$statusText} successfully.");
        } catch (\Exception $e) {
            // If account_status column doesn't exist, inform user
            if (str_contains($e->getMessage(), 'account_status')) {
                return back()->with('error', 'Account status feature requires account_status column in admission_users table. Please add it manually.');
            }
            return back()->with('error', 'Failed to toggle account status: ' . $e->getMessage());
        }
    }
}
