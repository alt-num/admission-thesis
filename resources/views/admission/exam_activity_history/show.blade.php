@extends('layouts.admission')

@section('title', 'Activity Log - ' . $attempt->applicant->first_name . ' ' . $attempt->applicant->last_name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Exam Activity Log</h1>
            <p class="mt-2 text-sm text-gray-600">
                {{ $attempt->applicant->first_name }} {{ $attempt->applicant->last_name }} - {{ $attempt->exam->title ?? 'N/A' }}
            </p>
        </div>
        <a href="{{ route('admission.exam-activity-history.index') }}" 
           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to History
        </a>
    </div>

    <!-- Attempt Summary -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Exam Attempt Summary</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <p class="text-sm text-gray-600 mb-1">Applicant</p>
                <p class="text-sm font-medium text-gray-900">
                    <a href="{{ route('admission.applicants.show', $attempt->applicant_id) }}" class="text-blue-600 hover:text-blue-900">
                        {{ $attempt->applicant->first_name }} {{ $attempt->applicant->last_name }}
                    </a>
                </p>
                <p class="text-xs text-gray-500">{{ $attempt->applicant->app_ref_no }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Exam</p>
                <p class="text-sm font-medium text-gray-900">{{ $attempt->exam->title ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Started</p>
                <p class="text-sm font-medium text-gray-900">
                    {{ $attempt->started_at->format('M d, Y g:i A') }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Finished</p>
                <p class="text-sm font-medium text-gray-900">
                    @if($attempt->finished_at)
                        {{ $attempt->finished_at->format('M d, Y g:i A') }}
                    @else
                        <span class="text-gray-400">In Progress</span>
                    @endif
                </p>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-200 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Events Logged</p>
                <p class="text-lg font-semibold text-gray-900">
                    {{ $eventCount }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Suspicious Events</p>
                <p class="text-lg font-semibold {{ $suspiciousEvents > 0 ? 'text-yellow-600' : 'text-gray-900' }}">
                    {{ $suspiciousEvents }}
                </p>
                <p class="text-xs text-gray-500 mt-1">Tab switches, blur, hidden, refresh</p>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div>
                <p class="text-sm text-gray-600 mb-1">Exam Code Verified</p>
                <p class="text-lg font-semibold {{ $examCodeVerified ? 'text-green-600' : 'text-gray-900' }}">
                    {{ $examCodeVerified ? 'Yes' : 'No' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Activity Log -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Anti-Cheat Activity Log</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Timestamp</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($logs as $log)
                        @php
                            $eventColors = [
                                'focus_violation' => 'bg-yellow-100 text-yellow-800',
                                'focus_violation_warning' => 'bg-orange-100 text-orange-800',
                                'focus_violation_final_warning' => 'bg-red-100 text-red-800',
                                'auto_submit_due_to_focus_violations' => 'bg-red-100 text-red-800',
                                'tab_switch' => 'bg-yellow-100 text-yellow-800',
                                'window_blur' => 'bg-yellow-100 text-yellow-800',
                                'copy_attempt' => 'bg-red-100 text-red-800',
                                'paste_attempt' => 'bg-red-100 text-red-800',
                                'contextmenu_blocked' => 'bg-red-100 text-red-800',
                                'forbidden_hotkey' => 'bg-red-100 text-red-800',
                                'ip_changed' => 'bg-orange-100 text-orange-800',
                                'invalid_exam_code' => 'bg-red-100 text-red-800',
                                'exam_code_verified' => 'bg-green-100 text-green-800',
                            ];
                            $eventColor = $eventColors[$log->event_type] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $log->event_timestamp->format('M d, Y') }}<br>
                                <span class="text-xs">{{ $log->event_timestamp->format('g:i A') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $eventColor }}">
                                    {{ str_replace('_', ' ', ucwords($log->event_type, '_')) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @if($log->event_details)
                                    @php
                                        $details = is_array($log->event_details) ? $log->event_details : json_decode($log->event_details, true);
                                    @endphp
                                    @if(is_array($details))
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach($details as $key => $value)
                                                @if(!in_array($key, ['timestamp']))
                                                    <li><span class="font-medium">{{ ucwords(str_replace('_', ' ', $key)) }}:</span> 
                                                        @if(is_array($value))
                                                            {{ json_encode($value) }}
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @else
                                        {{ $log->event_details }}
                                    @endif
                                @else
                                    <span class="text-gray-400">No details</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500">
                                No activity logs found for this attempt.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

