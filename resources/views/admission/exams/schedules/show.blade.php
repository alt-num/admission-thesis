@extends('layouts.admission')

@section('title', 'Schedule Details - ' . $exam->title)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admission.exams.schedules.index', $exam) }}" 
               class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $exam->title }}</h1>
                <p class="mt-1 text-sm text-gray-600">
                    Schedule: {{ $schedule->schedule_date->format('M d, Y') }} • 
                    {{ date('g:i A', strtotime($schedule->start_time)) }} – {{ date('g:i A', strtotime($schedule->end_time)) }}
                </p>
            </div>
        </div>
    </div>

    <!-- Schedule Info Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-900">Schedule Information</h2>
            <button onclick="toggleEditForm()" 
                    class="inline-flex items-center px-3 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Schedule
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div>
                <p class="text-sm text-gray-600 mb-1">Date</p>
                <p class="text-lg font-medium text-gray-900">{{ $schedule->schedule_date->format('M d, Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Start Time</p>
                <p class="text-lg font-medium text-gray-900">{{ date('g:i A', strtotime($schedule->start_time)) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">End Time</p>
                <p class="text-lg font-medium text-gray-900">{{ date('g:i A', strtotime($schedule->end_time)) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Capacity</p>
                <p class="text-lg font-medium text-gray-900">
                    {{ $schedule->capacity ?? 'Unlimited' }}
                    @if($schedule->capacity)
                        <span class="text-sm text-gray-600">
                            ({{ $assignedApplicants->count() }} assigned, {{ $schedule->capacity - $assignedApplicants->count() }} remaining)
                        </span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Edit Form (Hidden by default) -->
        <div id="editForm" class="hidden mt-6 pt-6 border-t border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Edit Schedule</h3>
            <form method="POST" action="{{ route('admission.exams.schedules.update', [$exam, $schedule]) }}" class="space-y-4">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" name="schedule_date" required
                               value="{{ $schedule->schedule_date->format('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Start Time</label>
                        <input type="time" name="start_time" required
                               value="{{ date('H:i', strtotime($schedule->start_time)) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                        <input type="time" name="end_time" required
                               value="{{ date('H:i', strtotime($schedule->end_time)) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Capacity</label>
                        <input type="number" name="capacity" min="1" placeholder="Optional"
                               value="{{ $schedule->capacity }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>
                <div class="flex space-x-3">
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Save Changes
                    </button>
                    <button type="button" onclick="toggleEditForm()"
                            class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Assigned Applicants -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Assigned Applicants</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">App Ref No</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campus</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($assignedApplicants as $applicant)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $applicant->app_ref_no }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $applicant->last_name }}, {{ $applicant->first_name }} {{ $applicant->middle_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $applicant->campus->campus_name ?? '—' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $applicant->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $applicant->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <form method="POST" 
                                      action="{{ route('admission.exams.schedules.unassign', [$exam, $schedule, $applicant]) }}"
                                      onsubmit="return confirm('Are you sure you want to unassign this applicant?');"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                        Unassign
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                No applicants assigned to this schedule yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Assign New Applicants (Bulk) -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">Assign Applicants (Bulk)</h2>
                <span class="text-sm text-gray-600">{{ $eligibleApplicants->total() }} eligible</span>
            </div>
        </div>

        @if($eligibleApplicants->count() > 0)
            <!-- Search Form -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <form method="GET" action="{{ route('admission.exams.schedules.show', [$exam, $schedule]) }}" class="flex items-center space-x-3">
                    <div class="flex-1">
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}"
                               placeholder="Search by name or app ref no..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    </div>
                    <button type="submit" 
                            class="px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                        Search
                    </button>
                    @if($search)
                        <a href="{{ route('admission.exams.schedules.show', [$exam, $schedule]) }}" 
                           class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            <!-- Bulk Assignment Form -->
            <form method="POST" action="{{ route('admission.exams.schedules.assign', [$exam, $schedule]) }}" id="bulkAssignForm">
                @csrf
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">
                                    <input type="checkbox" 
                                           id="selectAll"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">App Ref No</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campus</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($eligibleApplicants as $applicant)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" 
                                               name="applicants[]" 
                                               value="{{ $applicant->applicant_id }}"
                                               class="applicant-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $applicant->app_ref_no }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $applicant->last_name }}, {{ $applicant->first_name }} {{ $applicant->middle_name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $applicant->campus->campus_name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {{ $applicant->created_at->format('M d, Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($eligibleApplicants->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        {{ $eligibleApplicants->links() }}
                    </div>
                @endif

                <!-- Submit Button -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600" id="selectedCount">0 selected</span>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                id="assignButton"
                                disabled>
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Assign Selected
                        </button>
                    </div>
                    @error('applicants')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </form>
        @else
            <div class="px-6 py-8 text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-sm font-medium text-gray-900 mb-1">No eligible applicants</h3>
                <p class="text-sm text-gray-600">
                    @if($search)
                        No applicants match your search. <a href="{{ route('admission.exams.schedules.show', [$exam, $schedule]) }}" class="text-blue-600 hover:text-blue-900">Clear search</a>
                    @else
                        All pending applicants have already been assigned to a schedule for this exam.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>

<script>
function toggleEditForm() {
    const form = document.getElementById('editForm');
    form.classList.toggle('hidden');
}

// Bulk assignment checkbox logic
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const applicantCheckboxes = document.querySelectorAll('.applicant-checkbox');
    const selectedCountSpan = document.getElementById('selectedCount');
    const assignButton = document.getElementById('assignButton');

    if (selectAllCheckbox && applicantCheckboxes.length > 0) {
        // Select all functionality
        selectAllCheckbox.addEventListener('change', function() {
            applicantCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });

        // Individual checkbox change
        applicantCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectAllState();
                updateSelectedCount();
            });
        });

        // Update select all state based on individual checkboxes
        function updateSelectAllState() {
            const checkedCount = document.querySelectorAll('.applicant-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === applicantCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < applicantCheckboxes.length;
        }

        // Update selected count and button state
        function updateSelectedCount() {
            const checkedCount = document.querySelectorAll('.applicant-checkbox:checked').length;
            selectedCountSpan.textContent = checkedCount + ' selected';
            assignButton.disabled = checkedCount === 0;
        }

        // Initial state
        updateSelectedCount();
    }
});
</script>
@endsection

