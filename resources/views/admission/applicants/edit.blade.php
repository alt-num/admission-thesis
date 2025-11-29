@extends('layouts.admission')

@section('title', 'Edit Applicant - ESSU Admission System')

@push('head')
<script src="/js/alpine.js" defer></script>
@endpush

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Edit Applicant</h1>
        <p class="mt-2 text-sm text-gray-600">Application Reference: {{ $applicant->app_ref_no }}</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admission.applicants.update', $applicant) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Editable Fields -->
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Editable Information</h2>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700">First Name *</label>
                        <input type="text" 
                               name="first_name" 
                               id="first_name" 
                               value="{{ old('first_name', $applicant->first_name) }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name</label>
                        <input type="text" 
                               name="middle_name" 
                               id="middle_name" 
                               value="{{ old('middle_name', $applicant->middle_name) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('middle_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name *</label>
                        <input type="text" 
                               name="last_name" 
                               id="last_name" 
                               value="{{ old('last_name', $applicant->last_name) }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 mt-6">
                    <div>
                        <label for="app_ref_no" class="block text-sm font-medium text-gray-700">Applicant Reference Number</label>
                        <input type="text" 
                               id="app_ref_no" 
                               value="{{ $applicant->app_ref_no }}"
                               disabled
                               readonly
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 shadow-sm sm:text-sm font-mono cursor-not-allowed">
                        <p class="mt-1 text-xs text-gray-500">Reference number cannot be changed after creation.</p>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email', $applicant->email) }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_number" class="block text-sm font-medium text-gray-700">Contact Number</label>
                        <input type="text" 
                               name="contact_number" 
                               id="contact_number" 
                               value="{{ old('contact_number', $applicant->contact_number) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        @error('contact_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="text-md font-medium text-gray-900 mb-3">Preferred Courses</h3>
                    <p class="text-sm text-gray-600 mb-3">Type to search for courses.</p>
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                        <x-searchable-course-dropdown 
                            name="preferred_course_1" 
                            label="First Choice" 
                            :courses="$courses" 
                            :selected="old('preferred_course_1', $applicant->preferred_course_1)" 
                            :error="$errors->first('preferred_course_1')" />

                        <x-searchable-course-dropdown 
                            name="preferred_course_2" 
                            label="Second Choice" 
                            :courses="$courses" 
                            :selected="old('preferred_course_2', $applicant->preferred_course_2)" 
                            :error="$errors->first('preferred_course_2')" />

                        <x-searchable-course-dropdown 
                            name="preferred_course_3" 
                            label="Third Choice" 
                            :courses="$courses" 
                            :selected="old('preferred_course_3', $applicant->preferred_course_3)" 
                            :error="$errors->first('preferred_course_3')" />
                    </div>
                </div>
            </div>

            <!-- Read-Only System Fields -->
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">System Information (Read-Only)</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">Campus</label>
                        <p class="mt-1 text-gray-900">{{ $applicant->campus->campus_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">School Year</label>
                        <p class="mt-1 text-gray-900">{{ $applicant->school_year }}</p>
                    </div>
                </div>
            </div>

            <!-- Read-Only Fields -->
            <div class="border-b border-gray-200 pb-4 mb-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Read-Only Information</h2>
                
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Campus</label>
                        <input type="text" 
                               value="{{ $applicant->campus->campus_name ?? 'N/A' }}" 
                               disabled
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                    </div>


                    <div>
                        <label class="block text-sm font-medium text-gray-700">Birth Date</label>
                        <input type="text" 
                               value="{{ $applicant->birth_date ? $applicant->birth_date->format('Y-m-d') : 'N/A' }}" 
                               disabled
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Place of Birth</label>
                        <input type="text" 
                               value="{{ $applicant->place_of_birth ?? 'N/A' }}" 
                               disabled
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sex</label>
                        <input type="text" 
                               value="{{ $applicant->sex ?? 'N/A' }}" 
                               disabled
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Civil Status</label>
                        <input type="text" 
                               value="{{ $applicant->civil_status ?? 'N/A' }}" 
                               disabled
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Last School Attended</label>
                        <input type="text" 
                               value="{{ $applicant->last_school_attended ?? 'N/A' }}" 
                               disabled
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">School Address</label>
                        <input type="text" 
                               value="{{ $applicant->school_address ?? 'N/A' }}" 
                               disabled
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Year Graduated</label>
                        <input type="text" 
                               value="{{ $applicant->year_graduated ?? 'N/A' }}" 
                               disabled
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">General Average</label>
                        <input type="text" 
                               value="{{ $applicant->gen_average ?? 'N/A' }}" 
                               disabled
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Barangay</label>
                        <input type="text" 
                               value="{{ $applicant->barangay ?? 'N/A' }}" 
                               disabled
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Municipality</label>
                        <input type="text" 
                               value="{{ $applicant->municipality ?? 'N/A' }}" 
                               disabled
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Province</label>
                        <input type="text" 
                               value="{{ $applicant->province ?? 'N/A' }}" 
                               disabled
                               class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admission.applicants.show', $applicant) }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                    Update Applicant
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

