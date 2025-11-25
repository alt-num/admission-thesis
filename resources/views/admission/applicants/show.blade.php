@extends('layouts.admission')

@section('title', $applicant->app_ref_no . ' - Applicant Details - ESSU Admission System')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Applicant Details</h1>
            <p class="mt-2 text-sm text-gray-600">Application Reference: {{ $applicant->app_ref_no }}</p>
        </div>
        <div class="flex items-center space-x-3">
            @if($applicant->applicantUser)
                <form method="POST" action="{{ route('admission.applicants.send-credentials', $applicant) }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Send Login Credentials
                    </button>
                </form>
            @endif
            @if($applicant->examSchedules->isNotEmpty())
                <form method="POST" action="{{ route('admission.applicants.send-schedule', $applicant) }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Send Exam Schedule
                    </button>
                </form>
            @endif
            <a href="{{ route('admission.applicants.edit', $applicant) }}" 
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Applicant
            </a>
            <a href="{{ route('admission.applicants.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors">
                Back to List
            </a>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Personal Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Personal Information</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        {{ $applicant->first_name }} 
                        {{ $applicant->middle_name ? $applicant->middle_name . ' ' : '' }}
                        {{ $applicant->last_name }}
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Application Reference Number</dt>
                    <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $applicant->app_ref_no }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $applicant->email }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Contact Number</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $applicant->contact_number ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Birth Date</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $applicant->birth_date ? $applicant->birth_date->format('F d, Y') : 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Place of Birth</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $applicant->place_of_birth ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Sex</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $applicant->sex ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Civil Status</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $applicant->civil_status ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Address Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Address Information</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Barangay</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $applicant->barangay ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Municipality</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $applicant->municipality ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Province</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $applicant->province ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Educational Background -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Educational Background</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Last School Attended</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $applicant->last_school_attended ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">School Address</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $applicant->school_address ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Year Graduated</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $applicant->year_graduated ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">General Average</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $applicant->gen_average ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Application Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Application Information</h2>
            <dl class="space-y-3">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Campus</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $applicant->campus->campus_name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">School Year</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $applicant->school_year }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                        @php
                            $statusColors = [
                                'Pending' => 'bg-yellow-100 text-yellow-800',
                                'ExamTaken' => 'bg-purple-100 text-purple-800',
                                'Passed' => 'bg-green-100 text-green-800',
                                'Failed' => 'bg-red-100 text-red-800',
                            ];
                            $color = $statusColors[$applicant->status] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                            {{ $applicant->status }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Preferred Courses</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                        <ol class="list-decimal list-inside space-y-2">
                            @php
                                $preferredCourses = [
                                    1 => $applicant->preferredCourse1,
                                    2 => $applicant->preferredCourse2,
                                    3 => $applicant->preferredCourse3,
                                ];
                            @endphp
                            @foreach($preferredCourses as $index => $course)
                                @if($course)
                                    @php
                                        $courseResult = $applicant->courseResults->firstWhere('course_id', $course->course_id);
                                    @endphp
                                    <li class="flex items-center justify-between">
                                        <span>
                                            {{ $course->course_name }} ({{ $course->course_code }})
                                        </span>
                                        <span class="ml-4">
                                            @if($courseResult)
                                                @if($courseResult->result_status === 'Pass')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Pass ({{ number_format($courseResult->score_value, 2) }}%)
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Fail ({{ number_format($courseResult->score_value, 2) }}%)
                                                    </span>
                                                @endif
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                    Pending evaluation
                                                </span>
                                            @endif
                                        </span>
                                    </li>
                                @else
                                    <li>N/A</li>
                                @endif
                            @endforeach
                        </ol>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Registered By</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $applicant->registeredBy->username ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Registered At</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $applicant->created_at->format('F d, Y h:i A') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Exam Status -->
    @if($applicant->examAttempts->isNotEmpty())
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Exam Status</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Started At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Finished At</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Score</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($applicant->examAttempts as $attempt)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $attempt->exam->title ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $attempt->started_at->format('M d, Y h:i A') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $attempt->finished_at ? $attempt->finished_at->format('M d, Y h:i A') : 'In Progress' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-semibold">{{ $attempt->score_total ?? '0.00' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Course Evaluation Results -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Course Evaluation Results</h2>
        <div class="space-y-4">
            @php
                $preferredCourses = [
                    1 => $applicant->preferredCourse1,
                    2 => $applicant->preferredCourse2,
                    3 => $applicant->preferredCourse3,
                ];
            @endphp
            @foreach($preferredCourses as $index => $course)
                @if($course)
                    @php
                        $courseResult = $applicant->courseResults->firstWhere('course_id', $course->course_id);
                    @endphp
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div>
                            <h3 class="text-sm font-medium text-gray-900">{{ $course->course_name }} ({{ $course->course_code }})</h3>
                            <p class="text-xs text-gray-500 mt-1">Preferred Course {{ $index }}</p>
                        </div>
                        <div class="text-right">
                            @if($courseResult)
                                @if($courseResult->result_status === 'Pass')
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Pass
                                    </span>
                                @else
                                    <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Fail
                                    </span>
                                @endif
                                <p class="text-xs text-gray-500 mt-1">Score: {{ number_format($courseResult->score_value, 2) }}%</p>
                            @else
                                <span class="px-3 py-1 inline-flex text-sm leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    Not evaluated
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
            @if(!$applicant->preferredCourse1 && !$applicant->preferredCourse2 && !$applicant->preferredCourse3)
                <p class="text-sm text-gray-500">No preferred courses selected.</p>
            @endif
        </div>
    </div>

    <!-- Declaration Section -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Declaration</h2>
            <a href="{{ route('admission.applicants.declaration', $applicant) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                View Declaration
            </a>
        </div>
        @if($applicant->declaration)
            <p class="text-sm text-gray-600">Declaration submitted on {{ $applicant->declaration->certified_date ? $applicant->declaration->certified_date->format('F d, Y') : 'N/A' }}</p>
        @else
            <p class="text-sm text-gray-500">No declaration submitted yet.</p>
        @endif
    </div>

    <!-- Exam Eligibility & Course Qualification Panel -->
    <div class="bg-white p-6 rounded-xl shadow-md space-y-6">
        <h2 class="text-lg font-semibold text-gray-900">Exam Eligibility & Course Qualification</h2>

        @if($eligibility['total_score'] !== null)
            <!-- Total Score -->
            <div>
                <h3 class="text-sm font-medium text-gray-700 mb-2">Total Score</h3>
                <p class="text-2xl font-bold text-indigo-600">{{ number_format($eligibility['total_score'], 2) }}</p>
            </div>

            <!-- Section Scores -->
            @if(!empty($eligibility['sections']))
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Section Scores</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($eligibility['sections'] as $section)
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">{{ $section['name'] }}</p>
                                <p class="text-lg font-semibold text-gray-900">{{ number_format($section['score'], 2) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Subsection Scores -->
            @if(!empty($eligibility['subsections']))
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Subsection Scores</h3>
                    <div class="space-y-4">
                        @foreach($eligibility['subsections'] as $sectionName => $subsections)
                            <div class="border-l-4 border-indigo-500 pl-4">
                                <h4 class="text-sm font-semibold text-gray-800 mb-2">{{ $sectionName }}</h4>
                                <div class="space-y-2">
                                    @foreach($subsections as $subsection)
                                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                            <span class="text-sm text-gray-700">{{ $subsection['name'] }}</span>
                                            <span class="text-sm font-medium text-gray-900">{{ number_format($subsection['score'], 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Preferred Courses vs Passing Score -->
            @if(!empty($eligibility['courses']))
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-3">Preferred Courses vs Passing Score</h3>
                    <div class="space-y-3">
                        @foreach($eligibility['courses'] as $courseData)
                            @php
                                $course = $courseData['course'];
                                $passed = $courseData['passed'];
                                $required = $courseData['required'];
                                $priority = $courseData['priority'];
                            @endphp
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $course->course_name }} ({{ $course->course_code }})
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">Preferred Course {{ $priority }}</p>
                                    @if($required !== null)
                                        <p class="text-xs text-gray-500 mt-1">Required: {{ number_format($required, 2) }}</p>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    @if($required === null)
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            NO PASSING SCORE
                                        </span>
                                    @elseif($passed)
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            PASS
                                        </span>
                                    @else
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            FAIL
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Final Recommendation -->
            <div class="pt-4 border-t border-gray-200">
                <h3 class="text-sm font-medium text-gray-700 mb-2">Final Recommendation</h3>
                @if($eligibility['final_recommendation'])
                    <p class="text-lg font-semibold text-green-600">{{ $eligibility['final_recommendation'] }}</p>
                @else
                    <p class="text-lg font-semibold text-red-600">Not Qualified in Any Preferred Course</p>
                @endif
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-sm text-gray-500">No exam attempt found for this applicant.</p>
            </div>
        @endif
    </div>
</div>
@endsection

