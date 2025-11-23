@extends('layouts.admission')

@section('title', 'Declaration - ' . $applicant->app_ref_no . ' - ESSU Admission System')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Applicant Declaration</h1>
            <p class="mt-2 text-sm text-gray-600">Application Reference: {{ $applicant->app_ref_no }}</p>
            <p class="mt-1 text-sm text-gray-600">Name: {{ $applicant->first_name }} {{ $applicant->middle_name ? $applicant->middle_name . ' ' : '' }}{{ $applicant->last_name }}</p>
        </div>
        <a href="{{ route('admission.applicants.show', $applicant) }}" 
           class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Details
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6 space-y-6">
        <!-- Declaration Information (Read-Only) -->
        <div>
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Declaration Information</h2>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Physical Condition Flag</label>
                    <input type="text" 
                           value="{{ $applicant->declaration && $applicant->declaration->physical_condition_flag ? 'Yes' : 'No' }}" 
                           disabled
                           class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Disciplinary Action Flag</label>
                    <input type="text" 
                           value="{{ $applicant->declaration && $applicant->declaration->disciplinary_action_flag ? 'Yes' : 'No' }}" 
                           disabled
                           class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Physical Condition Description</label>
                    <textarea 
                        disabled
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">{{ $applicant->declaration->physical_condition_desc ?? 'N/A' }}</textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Disciplinary Action Description</label>
                    <textarea 
                        disabled
                        rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">{{ $applicant->declaration->disciplinary_action_desc ?? 'N/A' }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Certified Signature Name</label>
                    <input type="text" 
                           value="{{ $applicant->declaration->certified_signature_name ?? 'N/A' }}" 
                           disabled
                           class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Certified Date</label>
                    <input type="text" 
                           value="{{ $applicant->declaration && $applicant->declaration->certified_date ? $applicant->declaration->certified_date->format('F d, Y') : 'N/A' }}" 
                           disabled
                           class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                </div>
            </div>
        </div>

        <!-- Staff Remarks (Editable) -->
        <div class="border-t border-gray-200 pt-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Staff Remarks</h2>
            
            <form action="{{ route('admission.applicants.declaration.remarks', $applicant) }}" method="POST">
                @csrf
                
                <div>
                    <label for="remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
                    <textarea 
                        name="remarks" 
                        id="remarks" 
                        rows="6"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">{{ old('remarks', $applicant->declaration->remarks ?? '') }}</textarea>
                    @error('remarks')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">Add any notes or remarks about this applicant's declaration.</p>
                </div>

                <div class="mt-6 flex items-center justify-end space-x-4">
                    <a href="{{ route('admission.applicants.show', $applicant) }}" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Save Remarks
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

