@extends('layouts.admission')

@section('title', 'Exam Activity History - ESSU Admission System')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Exam Activity History</h1>
            <p class="mt-2 text-sm text-gray-600">Review anti-cheat logs and exam attempt history</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('admission.exam-activity-history.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Exam</label>
                    <select name="exam_id" id="examFilter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Exams</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->exam_id }}" {{ request('exam_id') == $exam->exam_id ? 'selected' : '' }}>
                                {{ $exam->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Schedule</label>
                    <select name="schedule_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Schedules</option>
                        @foreach($schedules as $schedule)
                            <option value="{{ $schedule->schedule_id }}" {{ request('schedule_id') == $schedule->schedule_id ? 'selected' : '' }}>
                                {{ $schedule->schedule_date->format('M d, Y') }} - {{ date('g:i A', strtotime($schedule->start_time)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Applicant</label>
                    <input type="text" name="applicant_search" value="{{ request('applicant_search') }}"
                           placeholder="Search by name or ref no..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Event Type</label>
                    <select name="event_types[]" multiple class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" size="3">
                        <option value="tab_switch" {{ in_array('tab_switch', (array)request('event_types', [])) ? 'selected' : '' }}>Tab Switch</option>
                        <option value="window_blur" {{ in_array('window_blur', (array)request('event_types', [])) ? 'selected' : '' }}>Window Blur</option>
                        <option value="window_hidden" {{ in_array('window_hidden', (array)request('event_types', [])) ? 'selected' : '' }}>Window Hidden</option>
                        <option value="visibility_hidden" {{ in_array('visibility_hidden', (array)request('event_types', [])) ? 'selected' : '' }}>Visibility Hidden</option>
                        <option value="idle" {{ in_array('idle', (array)request('event_types', [])) ? 'selected' : '' }}>Idle</option>
                        <option value="refresh" {{ in_array('refresh', (array)request('event_types', [])) ? 'selected' : '' }}>Refresh</option>
                        <option value="incorrect_exam_code" {{ in_array('incorrect_exam_code', (array)request('event_types', [])) ? 'selected' : '' }}>Incorrect Exam Code</option>
                        <option value="exam_code_verified" {{ in_array('exam_code_verified', (array)request('event_types', [])) ? 'selected' : '' }}>Exam Code Verified</option>
                        <option value="focus_violation" {{ in_array('focus_violation', (array)request('event_types', [])) ? 'selected' : '' }}>Focus Violation</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <select name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="desc" {{ request('sort', 'desc') == 'desc' ? 'selected' : '' }}>Newest First</option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </div>
                <div class="flex items-end space-x-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="suspicious_only" value="1" {{ request('suspicious_only') ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Suspicious Only</span>
                    </label>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Apply Filters
                </button>
                @if(request()->hasAny(['exam_id', 'schedule_id', 'applicant_search', 'date_from', 'date_to', 'event_types', 'suspicious_only', 'sort']))
                    <a href="{{ route('admission.exam-activity-history.index') }}" 
                       class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        Clear All
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Exam Attempts Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Started</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Finished</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Events</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Suspicious</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code Verified</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($attempts as $attempt)
                        @php
                            $eventCount = \App\Models\AntiCheatLog::where('exam_attempt_id', $attempt->attempt_id)->count();
                            $suspiciousCount = \App\Models\AntiCheatLog::where('exam_attempt_id', $attempt->attempt_id)
                                ->whereIn('event_type', ['tab_switch', 'window_blur', 'window_hidden', 'visibility_hidden', 'refresh'])
                                ->count();
                            $examCodeVerified = \App\Models\AntiCheatLog::where('exam_attempt_id', $attempt->attempt_id)
                                ->where('event_type', 'exam_code_verified')
                                ->exists();
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('admission.applicants.show', $attempt->applicant_id) }}" 
                                   class="text-sm font-medium text-blue-600 hover:text-blue-900">
                                    {{ $attempt->applicant->first_name }} {{ $attempt->applicant->last_name }}
                                </a>
                                <div class="text-xs text-gray-500">{{ $attempt->applicant->app_ref_no }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attempt->exam->title ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $attempt->started_at->format('M d, Y') }}<br>
                                <span class="text-xs">{{ $attempt->started_at->format('g:i A') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($attempt->finished_at)
                                    {{ $attempt->finished_at->format('M d, Y') }}<br>
                                    <span class="text-xs">{{ $attempt->finished_at->format('g:i A') }}</span>
                                @else
                                    <span class="text-gray-400">In Progress</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $eventCount }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                @if($suspiciousCount > 0)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        {{ $suspiciousCount }}
                                    </span>
                                @else
                                    <span class="text-gray-400">0</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                @if($examCodeVerified)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Yes</span>
                                @else
                                    <span class="text-gray-400">No</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admission.exam-activity-history.show', $attempt) }}" 
                                   class="text-blue-600 hover:text-blue-900">
                                    View Activity Log
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500">
                                No exam attempts found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($attempts->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                {{ $attempts->links() }}
            </div>
        @endif
    </div>
</div>

<script>
// Update schedule dropdown when exam changes
document.getElementById('examFilter')?.addEventListener('change', function() {
    // Reload page with new exam filter to get updated schedules
    const form = this.closest('form');
    if (form) {
        form.submit();
    }
});
</script>
@endsection

