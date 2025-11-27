<?php

namespace App\Http\Controllers\Admission;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class MyAccountController extends Controller
{
    /**
     * Show the form for editing the authenticated user's account.
     */
    public function edit()
    {
        $user = auth()->guard('admission')->user();

        return view('admission.my_account.edit', compact('user'));
    }

    /**
     * Update the authenticated user's account.
     */
    public function update(Request $request)
    {
        $user = auth()->guard('admission')->user();

        // Validation rules
        $rules = [
            'username' => [
                'required',
                'string',
                Rule::unique('admission_users', 'username')->ignore($user->admission_user_id, 'admission_user_id'),
            ],
            'current_password' => 'sometimes|required_with:new_password',
            'new_password' => 'sometimes|nullable|min:8|confirmed',
        ];

        $validated = $request->validate($rules);

        // If new_password is provided, verify current_password
        if (!empty($validated['new_password'])) {
            if (empty($validated['current_password'])) {
                return back()->withErrors(['current_password' => 'Current password is required to change password.'])->withInput();
            }

            if (!Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
            }
        }

        // Update username
        $user->username = $validated['username'];

        // Update password only if new_password is provided
        if (!empty($validated['new_password'])) {
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return back()->with('success', 'Account updated successfully.');
    }
}

