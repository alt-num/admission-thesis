@extends('layouts.applicant')

@section('title', 'Applicant Declaration - ESSU Applicant Portal')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Applicant Declaration</h2>
            <p class="mt-2 text-sm text-gray-600">Please read and complete the declaration carefully. All fields marked with * are required.</p>
        </div>

        <form action="{{ route('applicant.declaration.update') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Physical Condition Declaration -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Health Declaration</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Do you have any physical or health condition that may affect your studies? *
                        </label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="radio" 
                                       name="physical_condition_flag" 
                                       id="physical_condition_yes" 
                                       value="1"
                                       {{ old('physical_condition_flag', $declaration?->physical_condition_flag) == '1' ? 'checked' : '' }}
                                       required
                                       onchange="togglePhysicalConditionDesc(true)"
                                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                <label for="physical_condition_yes" class="ml-3 text-sm text-gray-700">
                                    Yes
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" 
                                       name="physical_condition_flag" 
                                       id="physical_condition_no" 
                                       value="0"
                                       {{ old('physical_condition_flag', $declaration?->physical_condition_flag) == '0' ? 'checked' : '' }}
                                       required
                                       onchange="togglePhysicalConditionDesc(false)"
                                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                <label for="physical_condition_no" class="ml-3 text-sm text-gray-700">
                                    No
                                </label>
                            </div>
                        </div>
                        @error('physical_condition_flag')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="physical_condition_desc_wrapper" style="display: {{ old('physical_condition_flag', $declaration?->physical_condition_flag) == '1' ? 'block' : 'none' }};">
                        <label for="physical_condition_desc" class="block text-sm font-medium text-gray-700">
                            Please provide details *
                        </label>
                        <textarea name="physical_condition_desc" 
                                  id="physical_condition_desc" 
                                  rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">{{ old('physical_condition_desc', $declaration?->physical_condition_desc) }}</textarea>
                        @error('physical_condition_desc')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Disciplinary Action Declaration -->
            <div class="border-b border-gray-200 pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Disciplinary Declaration</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Have you been subjected to any disciplinary action in your previous school? *
                        </label>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <input type="radio" 
                                       name="disciplinary_action_flag" 
                                       id="disciplinary_action_yes" 
                                       value="1"
                                       {{ old('disciplinary_action_flag', $declaration?->disciplinary_action_flag) == '1' ? 'checked' : '' }}
                                       required
                                       onchange="toggleDisciplinaryActionDesc(true)"
                                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                <label for="disciplinary_action_yes" class="ml-3 text-sm text-gray-700">
                                    Yes
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="radio" 
                                       name="disciplinary_action_flag" 
                                       id="disciplinary_action_no" 
                                       value="0"
                                       {{ old('disciplinary_action_flag', $declaration?->disciplinary_action_flag) == '0' ? 'checked' : '' }}
                                       required
                                       onchange="toggleDisciplinaryActionDesc(false)"
                                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
                                <label for="disciplinary_action_no" class="ml-3 text-sm text-gray-700">
                                    No
                                </label>
                            </div>
                        </div>
                        @error('disciplinary_action_flag')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="disciplinary_action_desc_wrapper" style="display: {{ old('disciplinary_action_flag', $declaration?->disciplinary_action_flag) == '1' ? 'block' : 'none' }};">
                        <label for="disciplinary_action_desc" class="block text-sm font-medium text-gray-700">
                            Please provide details *
                        </label>
                        <textarea name="disciplinary_action_desc" 
                                  id="disciplinary_action_desc" 
                                  rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">{{ old('disciplinary_action_desc', $declaration?->disciplinary_action_desc) }}</textarea>
                        @error('disciplinary_action_desc')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Certification -->
            <div class="pb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Certification</h3>
                
                <div class="bg-gray-50 p-4 rounded-md mb-4">
                    <p class="text-sm text-gray-700 leading-relaxed">
                        I hereby certify that all the information provided above is true and correct to the best of my knowledge. 
                        I understand that any false statement or misrepresentation may result in the denial of admission or dismissal 
                        from the university.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Signature Name -->
                    <div>
                        <label for="certified_signature_name" class="block text-sm font-medium text-gray-700">Full Name (Signature) *</label>
                        <input type="text" 
                               name="certified_signature_name" 
                               id="certified_signature_name" 
                               value="{{ old('certified_signature_name', $declaration?->certified_signature_name ?? ($applicant->first_name . ' ' . ($applicant->middle_name ? $applicant->middle_name . ' ' : '') . $applicant->last_name)) }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        @error('certified_signature_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date -->
                    <div>
                        <label for="certified_date" class="block text-sm font-medium text-gray-700">Date *</label>
                        <input type="date" 
                               name="certified_date" 
                               id="certified_date" 
                               value="{{ old('certified_date', $declaration?->certified_date?->format('Y-m-d') ?? date('Y-m-d')) }}"
                               required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm">
                        @error('certified_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-6 border-t border-gray-200">
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                    Submit Declaration
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function togglePhysicalConditionDesc(show) {
    const wrapper = document.getElementById('physical_condition_desc_wrapper');
    const textarea = document.getElementById('physical_condition_desc');
    wrapper.style.display = show ? 'block' : 'none';
    if (!show) {
        textarea.value = '';
    }
}

function toggleDisciplinaryActionDesc(show) {
    const wrapper = document.getElementById('disciplinary_action_desc_wrapper');
    const textarea = document.getElementById('disciplinary_action_desc');
    wrapper.style.display = show ? 'block' : 'none';
    if (!show) {
        textarea.value = '';
    }
}
</script>
@endsection

