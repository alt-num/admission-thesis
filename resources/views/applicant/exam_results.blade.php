@extends('layouts.applicant')

@section('title', 'Exam Results - ESSU Applicant Portal')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Your Examination Result</h1>
        <p class="mt-1 text-sm text-gray-600">Application Reference: <span class="font-semibold">{{ $applicant->app_ref_no }}</span></p>
    </div>

    <!-- Overall Status Card -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 mb-2">Overall Status</h2>
                <p class="text-sm text-gray-600">Exam completed: {{ $attempt->finished_at->format('F d, Y g:i A') }}</p>
            </div>
            <div>
                @if($applicant->status === 'Qualified')
                    <span class="inline-flex items-center px-6 py-3 bg-green-100 text-green-800 text-xl font-bold rounded-lg">
                        <svg class="h-6 w-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        QUALIFIED
                    </span>
                @elseif($applicant->status === 'NotQualified')
                    <span class="inline-flex items-center px-6 py-3 bg-red-100 text-red-800 text-xl font-bold rounded-lg">
                        <svg class="h-6 w-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        NOT QUALIFIED
                    </span>
                @else
                    <span class="inline-flex items-center px-6 py-3 bg-yellow-100 text-yellow-800 text-xl font-bold rounded-lg">
                        <svg class="h-6 w-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        PENDING
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Overall Score Card -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Overall Score</h2>
        <div class="flex items-center justify-center py-8">
            <div class="text-center">
                <p class="text-6xl font-bold text-blue-600">{{ $overallCorrect }}/{{ $overallTotal }}</p>
                <p class="text-2xl font-semibold text-gray-700 mt-2">{{ number_format($overallPercentage, 2) }}%</p>
            </div>
        </div>
    </div>

    <!-- Section and Subsection Scores -->
    @if(isset($sections) && $sections->isNotEmpty())
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Detailed Scores by Section</h2>
        
        <div class="space-y-6">
            @foreach($sections as $section)
                @if(isset($sectionScoresData[$section->section_id]))
                    @php
                        $sectionData = $sectionScoresData[$section->section_id];
                    @endphp
                    <div class="border-l-4 border-blue-500 pl-4 pb-4">
                        <h3 class="text-base font-semibold text-gray-900 mb-2">
                            {{ $section->name }} — {{ $sectionData['correct'] }}/{{ $sectionData['total'] }} ({{ number_format($sectionData['percentage'], 2) }}%)
                        </h3>
                        
                        <div class="ml-4 space-y-2 mt-2">
                            @foreach($section->subsections as $subsection)
                                @if(isset($subsectionScoresData[$subsection->subsection_id]))
                                    @php
                                        $subsectionData = $subsectionScoresData[$subsection->subsection_id];
                                    @endphp
                                    <div class="text-sm text-gray-700">
                                        <span class="font-medium">• {{ $subsection->name }}</span>
                                        <span class="text-gray-600"> — {{ $subsectionData['correct'] }}/{{ $subsectionData['total'] }} ({{ number_format($subsectionData['percentage'], 2) }}%)</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    <!-- Course Evaluation Results -->
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Course Evaluation Results</h2>
        <p class="text-sm text-gray-600 mb-4">Your performance evaluation for your preferred courses:</p>
        
        <div class="space-y-4">
            @foreach($courseResults as $priority => $result)
                <div class="border rounded-lg p-4 {{ $result['status'] === 'Qualified' ? 'border-green-500 bg-green-50' : ($result['status'] === 'Missed' ? 'border-gray-500 bg-gray-50' : 'border-red-500 bg-red-50') }}">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <p class="text-sm text-gray-600 mb-1">
                                @if($priority === 1)
                                    1st Choice
                                @elseif($priority === 2)
                                    2nd Choice
                                @else
                                    3rd Choice
                                @endif
                            </p>
                            <p class="font-semibold text-gray-900">{{ $result['course']->course_code }} - {{ $result['course']->course_name }}</p>
                            @if($result['status'] === 'Missed')
                                <p class="text-xs text-gray-700 mt-1 font-medium">Missed Exam — No qualification results available.</p>
                            @elseif($result['passing_score'] !== null)
                                <p class="text-xs text-gray-600 mt-1">Passing Score: {{ $result['passing_score'] }}</p>
                            @else
                                <p class="text-xs text-gray-600 mt-1">No passing score required (exam completion only)</p>
                            @endif
                        </div>
                        <div class="ml-4">
                            @if($result['status'] === 'Qualified')
                                <span class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-lg font-bold rounded-lg">
                                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    QUALIFIED
                                </span>
                            @elseif($result['status'] === 'Missed')
                                <span class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-lg font-bold rounded-lg">
                                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    MISSED EXAM
                                </span>
                            @else
                                <span class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-lg font-bold rounded-lg">
                                    <svg class="h-5 w-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    NOT QUALIFIED
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Information Notice -->
    <div class="mb-6">
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        <strong>Your entrance exam is now completed.</strong><br>
                        Please remember that your exam score does not guarantee acceptance to any course.<br>
                        Each college has its own enrollment process and requirements.<br>
                        Please follow the instructions of your chosen department for the next steps.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Back to Dashboard -->
    <div class="text-center">
        <a href="{{ route('applicant.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Dashboard
        </a>
    </div>
</div>
@endsection

