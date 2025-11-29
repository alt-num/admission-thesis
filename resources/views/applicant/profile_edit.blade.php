@extends('layouts.applicant')

@section('title', 'Edit Profile - ESSU Applicant Portal')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Edit Profile</h2>
            <p class="mt-2 text-sm text-gray-600">Update your account information. Fields marked with * are required.</p>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">
                        <strong>Success!</strong> {{ session('success') }}
                    </p>
                </div>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">
                        <strong>Please correct the following errors:</strong>
                    </p>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <form action="{{ route('applicant.profile.update') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Editable Fields -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Username -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Username *</label>
                        <input type="text" 
                               name="username" 
                               id="username" 
                               value="{{ old('username', $applicantUser->username) }}"
                               required
                               minlength="3"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        @error('username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email Address *</label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email', $applicant->email) }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Mobile Number -->
                    <div>
                        <label for="contact_number" class="block text-sm font-medium text-gray-700">Mobile Number *</label>
                        <input type="text" 
                               name="contact_number" 
                               id="contact_number" 
                               value="{{ old('contact_number', $applicant->contact_number) }}"
                               required
                               maxlength="32"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        @error('contact_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Password Change (Optional) -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Change Password (Optional)</h3>
                <p class="text-sm text-gray-600 mb-4">Leave blank if you don't want to change your password.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               minlength="6"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               minlength="6"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <!-- Read-Only Information -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Personal Information (Read-Only)</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Reference Number -->
                    <div>
                        <label for="app_ref_no" class="block text-sm font-medium text-gray-700">Application Reference Number</label>
                        <input type="text" 
                               id="app_ref_no" 
                               value="{{ $applicant->app_ref_no }}"
                               disabled
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm cursor-not-allowed">
                    </div>

                    <!-- First Name -->
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                        <input type="text" 
                               id="first_name" 
                               value="{{ $applicant->first_name }}"
                               disabled
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm cursor-not-allowed">
                    </div>

                    <!-- Middle Name -->
                    <div>
                        <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
                        <input type="text" 
                               id="middle_name" 
                               value="{{ $applicant->middle_name ?? 'N/A' }}"
                               disabled
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm cursor-not-allowed">
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                        <input type="text" 
                               id="last_name" 
                               value="{{ $applicant->last_name }}"
                               disabled
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm cursor-not-allowed">
                    </div>

                    <!-- Birthdate -->
                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700">Birthdate</label>
                        <input type="text" 
                               id="birth_date" 
                               value="{{ $applicant->birth_date ? $applicant->birth_date->format('F d, Y') : 'N/A' }}"
                               disabled
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm cursor-not-allowed">
                    </div>
                </div>
            </div>

            <!-- Course Preferences (Read-Only) -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Course Preferences (Read-Only)</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Preferred Course 1 -->
                    <div>
                        <label for="preferred_course_1" class="block text-sm font-medium text-gray-700">First Choice</label>
                        <input type="text" 
                               id="preferred_course_1" 
                               value="{{ $applicant->preferredCourse1 ? $applicant->preferredCourse1->course_name : 'Not Set' }}"
                               disabled
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm cursor-not-allowed">
                    </div>

                    <!-- Preferred Course 2 -->
                    <div>
                        <label for="preferred_course_2" class="block text-sm font-medium text-gray-700">Second Choice</label>
                        <input type="text" 
                               id="preferred_course_2" 
                               value="{{ $applicant->preferredCourse2 ? $applicant->preferredCourse2->course_name : 'Not Set' }}"
                               disabled
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm cursor-not-allowed">
                    </div>

                    <!-- Preferred Course 3 -->
                    <div>
                        <label for="preferred_course_3" class="block text-sm font-medium text-gray-700">Third Choice</label>
                        <input type="text" 
                               id="preferred_course_3" 
                               value="{{ $applicant->preferredCourse3 ? $applicant->preferredCourse3->course_name : 'Not Set' }}"
                               disabled
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm cursor-not-allowed">
                    </div>
                </div>
            </div>

            <!-- Exam Status (Read-Only) -->
            <div class="pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Exam Status (Read-Only)</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Exam Status -->
                    <div>
                        <label for="exam_status" class="block text-sm font-medium text-gray-700">Exam Status</label>
                        <input type="text" 
                               id="exam_status" 
                               value="{{ ucfirst($applicant->status ?? 'Pending') }}"
                               disabled
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm cursor-not-allowed">
                    </div>

                    <!-- Account Status -->
                    <div>
                        <label for="account_status" class="block text-sm font-medium text-gray-700">Account Status</label>
                        <input type="text" 
                               id="account_status" 
                               value="{{ ucfirst($applicantUser->account_status ?? 'Active') }}"
                               disabled
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm cursor-not-allowed">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('applicant.dashboard') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

