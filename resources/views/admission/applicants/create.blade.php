@extends('layouts.admission')

@section('title', 'Register Applicant - ESSU Admission System')

@push('head')
<script src="/js/alpine.js" defer></script>
<script>
    // Campus data for JavaScript
    const campuses = @json($campuses->mapWithKeys(function($campus) {
        return [$campus->campus_id => [
            'city_code' => $campus->city_code,
            'campus_name' => $campus->campus_name
        ]];
    }));
    const currentYear = {{ date('y') }};
</script>
@endpush

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Register New Applicant</h1>
        <p class="mt-2 text-sm text-gray-600">Create a new applicant account</p>
    </div>

    <!-- Info Notice -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Note:</strong> The application reference number will be auto-generated based on campus and school year. Upon registration, a placeholder name will be assigned. The applicant will provide their complete personal information when they log in.
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admission.applicants.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Application Reference Number (Auto-Generated Preview) -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Application Reference Number</label>
                <div class="mt-1">
                    <!-- Display Preview -->
                    <div id="app_ref_preview" class="p-3 bg-blue-50 border border-blue-200 rounded-md">
                        <p class="text-xs text-gray-600 mb-1">Auto-generated:</p>
                        <p class="text-lg font-mono font-semibold text-blue-600" id="preview_text">—</p>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">The reference number will be automatically generated based on your campus and school year selections below.</p>
                </div>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       value="{{ old('email') }}"
                       required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Campus and School Year -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="campus_id" class="block text-sm font-medium text-gray-700">Campus *</label>
                    <select name="campus_id" 
                            id="campus_id" 
                            required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Select Campus</option>
                        @foreach($campuses as $campus)
                            <option value="{{ $campus->campus_id }}" {{ old('campus_id', $defaultCampusId) == $campus->campus_id ? 'selected' : '' }}>
                                {{ $campus->campus_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('campus_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="school_year" class="block text-sm font-medium text-gray-700">School Year *</label>
                    <input type="text" 
                           name="school_year" 
                           id="school_year" 
                           value="{{ old('school_year', $defaultSchoolYear) }}"
                           placeholder="e.g., 2024-2025"
                           required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('school_year')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Exam Schedule -->
            @if($schedules->isNotEmpty())
                <div>
                    <label for="schedule_id" class="block text-sm font-medium text-gray-700">Exam Schedule (optional)</label>
                    <select name="schedule_id" 
                            id="schedule_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">No schedule yet</option>
                        @foreach($schedules as $schedule)
                            <option value="{{ $schedule->schedule_id }}" {{ old('schedule_id') == $schedule->schedule_id ? 'selected' : '' }}>
                                {{ $schedule->schedule_date->format('M d, Y') }} — 
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('g:i A') }} to 
                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('g:i A') }}
                                @if($schedule->location)
                                    — {{ $schedule->location }}
                                @endif
                                @if($schedule->capacity)
                                    ({{ $schedule->capacity }} slots)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-500">
                        Assign the applicant to an exam schedule immediately. You can also assign them later.
                    </p>
                    @error('schedule_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admission.applicants.index') }}" 
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                    Register Applicant
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const campusSelect = document.getElementById('campus_id');
        const schoolYearInput = document.getElementById('school_year');
        const previewText = document.getElementById('preview_text');

        function updatePreview() {
            const campusId = campusSelect.value;
            const schoolYear = schoolYearInput.value.trim();

            if (!campusId || !schoolYear) {
                previewText.textContent = '—';
                return;
            }

            const campus = campuses[campusId];
            if (!campus) {
                previewText.textContent = '—';
                return;
            }

            // Validate school year format (e.g., 2024-2025)
            const yearMatch = schoolYear.match(/(\d{4})-(\d{4})/);
            if (!yearMatch) {
                previewText.textContent = '—';
                return;
            }

            // Extract YY from school year (e.g., 2025-2026 → 25)
            const yearShort = schoolYear.substring(2, 4);
            const cityCode = campus.city_code;

            // Display format: CITYCODE-YY##### (# will be auto-generated by server)
            previewText.textContent = `${cityCode}-${yearShort}##### (server-generated sequence)`;
        }

        campusSelect.addEventListener('change', updatePreview);
        schoolYearInput.addEventListener('input', updatePreview);
        
        // Initial preview update
        updatePreview();
    });
</script>
@endpush
@endsection

