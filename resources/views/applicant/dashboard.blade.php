@extends('layouts.applicant')

@section('title', 'Dashboard - ESSU Applicant Portal')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Welcome Header -->
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-gray-900">Welcome, {{ $applicant->first_name }}!</h2>
        <p class="mt-1 text-sm text-gray-600">Application Reference: <span class="font-semibold">{{ $applicant->app_ref_no }}</span></p>
    </div>

    <!-- Exam Result Status Notice -->
    @if($examStatus === 'finished' && $applicant->status)
        @if($applicant->status === 'Qualified')
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">
                            <strong>Congratulations!</strong> You have qualified for admission. Please wait for further instructions from the Admission Office.
                        </p>
                    </div>
                </div>
            </div>
        @elseif($applicant->status === 'NotQualified')
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            Unfortunately, you did not meet the qualification requirements. Please contact the Admission Office for guidance on next steps.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- Status Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Profile Status Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Profile Status</h3>
                <svg class="h-8 w-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="space-y-2">
                <p class="text-2xl font-bold text-green-600">Profile Completed ✓</p>
                <p class="text-sm text-gray-600">Your profile and declaration have been successfully submitted.</p>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-200">
                <a href="{{ route('applicant.profile.edit') }}" 
                   class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Profile
                </a>
            </div>
        </div>

        <!-- Schedule Status Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Exam Schedule</h3>
                <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
            <div class="space-y-2">
                @if($assignedSchedule)
                    <p class="text-sm font-medium text-gray-900 mb-2">Schedule Assigned</p>
                    <div class="bg-blue-50 rounded p-3 space-y-1 mb-3">
                        <p class="text-sm text-gray-700"><span class="font-semibold">Date:</span> {{ $assignedSchedule->examSchedule->schedule_date->format('F d, Y') }}</p>
                        <p class="text-sm text-gray-700"><span class="font-semibold">Time:</span> {{ \Carbon\Carbon::parse($assignedSchedule->examSchedule->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($assignedSchedule->examSchedule->end_time)->format('g:i A') }}</p>
                        <p class="text-sm text-gray-700"><span class="font-semibold">Location:</span> {{ $assignedSchedule->examSchedule->location ?? 'TBA' }}</p>
                    </div>
                    <a href="{{ route('applicant.schedule') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                        View Full Schedule
                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                @else
                    <p class="text-sm text-gray-600">No schedule assigned yet.</p>
                    <p class="text-xs text-gray-500 mt-2">Please wait for the admission office to assign you an exam schedule.</p>
                @endif
            </div>
        </div>

        <!-- Exam Status Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Exam Status</h3>
                <svg class="h-8 w-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div class="space-y-2">
                @if($examStatus === 'not_started')
                    <p class="text-sm font-medium text-gray-900">Not Started</p>
                    <p class="text-xs text-gray-500 mb-3">You have not taken the exam yet.</p>
                    @if($assignedSchedule)
                        <a href="{{ route('applicant.schedule') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Go to Schedule Page
                            <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    @endif
                @elseif($examStatus === 'in_progress')
                    <p class="text-sm font-medium text-orange-600">In Progress</p>
                    <p class="text-xs text-gray-500 mb-3">You have an exam in progress.</p>
                    <a href="{{ route('applicant.exam.take') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Continue Exam
                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                @elseif($examStatus === 'finished')
                    <p class="text-sm font-medium text-green-600">Exam Completed ✓</p>
                    <div class="mt-2 bg-green-50 rounded p-3 mb-3">
                        <p class="text-xs text-gray-600">Finished: {{ $examAttempt->finished_at->format('M d, Y g:i A') }}</p>
                        @if($examAttempt->score_total)
                            <p class="text-sm font-semibold text-gray-700 mt-1">Score: {{ number_format($examAttempt->score_total, 2) }}</p>
                        @endif
                    </div>
                    <a href="{{ route('applicant.exam.results') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 font-medium">
                        View Full Results
                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                @endif
            </div>
        </div>

        <!-- Course Evaluation Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Course Evaluation</h3>
                <svg class="h-8 w-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <div class="space-y-2">
                @if($courseResults->isEmpty())
                    <p class="text-sm text-gray-600">No evaluation results yet.</p>
                    <p class="text-xs text-gray-500 mt-2">Results will be available after your exam is evaluated.</p>
                @else
                    <p class="text-sm font-medium text-gray-900 mb-3">Your Results:</p>
                    <div class="space-y-2">
                        @foreach($courseResults as $result)
                            <div class="flex items-center justify-between p-2 rounded {{ $result->result_status === 'Qualified' ? 'bg-green-50' : 'bg-red-50' }}">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ $result->course->course_code }}</p>
                                    <p class="text-xs text-gray-600">{{ $result->course->course_name }}</p>
                                </div>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $result->result_status === 'Qualified' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $result->result_status === 'Qualified' ? 'Qualified' : 'Not Qualified' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- Information Notice -->
    <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    For any questions or concerns, please contact the Admission Office. You will receive email notifications for any updates regarding your application.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

