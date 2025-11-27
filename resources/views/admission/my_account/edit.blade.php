@extends('layouts.admission')

@section('title', 'My Account - ESSU Admission System')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">My Account</h1>
        <p class="mt-2 text-sm text-gray-600">Manage your account settings</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admission.my-account.update') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Account Information Section -->
            <div class="border-b border-gray-200 pb-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h2>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Username *</label>
                        <input type="text" 
                               name="username" 
                               id="username" 
                               value="{{ old('username', $user->username) }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role (Read-only) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Role</label>
                        <div class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-sm text-gray-600">
                            {{ $user->role }}
                        </div>
                    </div>

                    <!-- Account Status (Read-only) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Account Status</label>
                        <div class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 text-sm">
                            @php
                                $status = $user->account_status ?? 'active';
                                $statusColor = strtolower($status) === 'active' 
                                    ? 'text-green-600 font-medium' 
                                    : 'text-red-600 font-medium';
                            @endphp
                            <span class="{{ $statusColor }}">{{ ucfirst($status) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Password Section -->
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Change Password</h2>
                <p class="text-sm text-gray-500 mb-4">Leave password fields blank if you don't want to change your password.</p>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Current Password -->
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                        <input type="password" 
                               name="current_password" 
                               id="current_password" 
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('current_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" 
                               name="new_password" 
                               id="new_password" 
                               minlength="8"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <p class="mt-1 text-xs text-gray-500">Minimum 8 characters</p>
                        @error('new_password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm New Password -->
                    <div class="sm:col-span-2">
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                        <input type="password" 
                               name="new_password_confirmation" 
                               id="new_password_confirmation" 
                               minlength="8"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-6 border-t border-gray-200">
                <button type="submit" 
                        class="inline-flex items-center px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

