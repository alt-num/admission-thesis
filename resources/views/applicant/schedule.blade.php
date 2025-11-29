@extends('layouts.applicant')

@section('title', 'Exam Schedule - ESSU Applicant Portal')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="mb-6">
        <h2 class="text-3xl font-bold text-gray-900">Exam Schedule</h2>
        <p class="mt-1 text-sm text-gray-600">View your assigned examination schedule</p>
    </div>

    <!-- Schedule Card -->
    <div class="bg-white rounded-lg shadow-lg p-6 md:p-8">
        @if(!$hasSchedule)
            <!-- No Schedule Assigned -->
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No Schedule Assigned Yet</h3>
                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                    You do not have an exam schedule assigned. Please wait for the Admission Office to assign you a schedule.
                </p>
                
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 max-w-2xl mx-auto text-left">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-blue-700">
                                You will receive an email notification once your examination schedule has been assigned.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Schedule Details -->
            <div class="mb-6">
                <div class="flex items-center mb-4">
                    <svg class="h-8 w-8 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900">Schedule Assigned</h3>
                </div>
                <p class="text-sm text-gray-600">Your examination schedule details:</p>
            </div>

            <!-- Schedule Information Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Exam</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $assignedSchedule->examSchedule->exam->title }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Date</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $assignedSchedule->examSchedule->schedule_date->format('F d, Y') }}</p>
                    <p class="text-sm text-gray-500">{{ $assignedSchedule->examSchedule->schedule_date->format('l') }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Start Time</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $startTimePh->format('g:i A') }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <label class="block text-sm font-medium text-gray-600 mb-1">End Time</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $endTimePh->format('g:i A') }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 md:col-span-2">
                    <label class="block text-sm font-medium text-gray-600 mb-1">Location</label>
                    <p class="text-lg font-semibold text-gray-900">{{ $assignedSchedule->examSchedule->location ?? 'TBA' }}</p>
                </div>
            </div>

            <!-- Exam Status Section -->
            <div class="border-t border-gray-200 pt-6">
                @if($finishedAttempt)
                    <!-- Already Finished -->
                    <div class="text-center py-8">
                        <svg class="mx-auto h-16 w-16 text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">You have already completed the exam.</h3>
                        <p class="text-gray-600 mb-6">Finished at: {{ $finishedAttempt->finished_at->format('F d, Y g:i A') }}</p>
                        
                        <a href="{{ route('applicant.exam.results') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            View Exam Results
                        </a>
                    </div>

                @elseif($examUpcoming)
                    <!-- Exam Upcoming -->
                    <div class="text-center py-8">
                        <svg class="mx-auto h-16 w-16 text-yellow-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Your exam is not yet available.</h3>
                        <p class="text-gray-600 mb-2">The exam will start at:</p>
                        <p class="text-lg font-bold text-blue-600 mb-6">{{ $startTimePh->format('F d, Y g:i A') }}</p>
                        
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 max-w-2xl mx-auto text-left">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700">
                                        Please return to this page at or after the scheduled start time to begin your exam.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($examExpired)
                    <!-- Exam Expired -->
                    <div class="text-center py-8">
                        <svg class="mx-auto h-16 w-16 text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Your exam schedule has expired.</h3>
                        <p class="text-gray-600 mb-6">The exam window closed at {{ $endTimePh->format('F d, Y g:i A') }}.</p>
                        
                        <div class="bg-red-50 border-l-4 border-red-400 p-4 max-w-2xl mx-auto text-left">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">
                                        Please contact the Admission Office immediately if you need to reschedule.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($examAvailable)
                    <!-- Exam Available - Show Start Button -->
                    <div class="text-center py-8">
                        <svg class="mx-auto h-16 w-16 text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Your exam is now available!</h3>
                        <p class="text-gray-600 mb-6">Click the button below to start your examination.</p>

                        <!-- Important Instructions -->
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 max-w-2xl mx-auto text-left mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h4 class="text-sm font-medium text-blue-800 mb-2">Before You Start:</h4>
                                    <ul class="text-sm text-blue-700 space-y-1">
                                        <li>• Please keep the exam window active at all times.</li>
                                        <li>• Switching to other tabs or applications may affect your exam.</li>
                                        <li>• Unintentional page refreshes caused by network issues are allowed.</li>
                                        <li>• The system will notify you if any restricted action occurs.</li>
                                        <li>• Ensure you submit all answers before the scheduled end time.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Exam Info Panel -->
                        <div class="bg-white border-2 border-gray-300 rounded-lg p-6 max-w-md mx-auto mb-6 space-y-3">
                            <div class="text-center border-b border-gray-200 pb-3">
                                <p class="text-sm text-gray-600 mb-1">Date & Time</p>
                                <p class="text-base font-semibold text-gray-900">{{ $assignedSchedule->examSchedule->schedule_date->format('F d, Y') }}</p>
                                <p class="text-sm text-gray-600">{{ $startTimePh->format('g:i A') }} - {{ $endTimePh->format('g:i A') }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-600 mb-1">Location</p>
                                <p class="text-base font-semibold text-gray-900">{{ $assignedSchedule->examSchedule->location ?? 'TBA' }}</p>
                            </div>
                        </div>

                        <!-- Exam Code Input Form -->
                        <form method="POST" action="{{ route('applicant.exam.start') }}" id="examStartForm">
                            @csrf
                            @php
                                $examCodeRequired = \App\Services\AntiCheatSettingsService::getFeature('exam_code_required', true);
                            @endphp
                            @if($examCodeRequired && $assignedSchedule->examSchedule->exam_code)
                            <div class="bg-white border-2 border-gray-300 rounded-lg p-6 max-w-md mx-auto mb-6">
                                <label for="exam_code" class="block text-sm font-medium text-gray-700 mb-2">
                                    Enter the Exam Code provided by the proctor:
                                </label>
                                <input type="text" 
                                       id="exam_code" 
                                       name="exam_code" 
                                       required
                                       minlength="4"
                                       maxlength="5"
                                       pattern="[A-Z0-9]{4,5}"
                                       placeholder="XXXX"
                                       class="w-full px-4 py-3 text-center text-2xl font-bold tracking-widest border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 uppercase"
                                       style="letter-spacing: 0.5em;"
                                       autocomplete="off">
                            </div>
                            @endif
                            <button type="submit" 
                                    class="inline-flex items-center px-8 py-4 bg-green-600 text-white text-lg font-semibold rounded-lg hover:bg-green-700 transition-colors shadow-lg">
                                <svg class="h-6 w-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Start Exam
                            </button>
                        </form>
                        @if($assignedSchedule->examSchedule->exam_code)
                        <script>
                            (function() {
                                const examCodeInput = document.getElementById('exam_code');
                                const examStartForm = document.getElementById('examStartForm');
                                
                                if (!examCodeInput || !examStartForm) return;
                                
                                // Auto-uppercase and limit input
                                examCodeInput.addEventListener('input', function(e) {
                                    this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
                                    // Clear any custom validity when user types
                                    this.setCustomValidity('');
                                });
                                
                                // Validate exam code on form submission
                                examStartForm.addEventListener('submit', async function(e) {
                                    // Let browser handle pattern validation first
                                    if (!examCodeInput.validity.valid) {
                                        // Browser will show its native pattern bubble
                                        return;
                                    }
                                    
                                    // Pattern is valid, now check the actual code
                                    const examCode = examCodeInput.value.trim();
                                    
                                    if (examCode.length >= 4) {
                                        // Prevent default submission
                                        e.preventDefault();
                                        
                                        try {
                                            // Check code via AJAX
                                            const formData = new FormData();
                                            formData.append('exam_code', examCode);
                                            formData.append('_token', '{{ csrf_token() }}');
                                            
                                            const response = await fetch('{{ route('applicant.exam.check-code') }}', {
                                                method: 'POST',
                                                body: formData,
                                                headers: {
                                                    'Accept': 'application/json',
                                                    'X-Requested-With': 'XMLHttpRequest'
                                                }
                                            });
                                            
                                            const data = await response.json();
                                            
                                            if (data.valid === true) {
                                                // Code is valid - clear custom validity and submit form
                                                examCodeInput.setCustomValidity('');
                                                examStartForm.submit();
                                            } else {
                                                // Code is invalid - show browser-native bubble
                                                examCodeInput.setCustomValidity('Incorrect exam code.');
                                                examCodeInput.reportValidity();
                                            }
                                        } catch (error) {
                                            console.error('Error checking exam code:', error);
                                            // On error, allow form to submit normally
                                            examCodeInput.setCustomValidity('');
                                            examStartForm.submit();
                                        }
                                    }
                                });
                            })();
                        </script>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Back to Dashboard Button -->
    <div class="mt-6 text-center">
        <a href="{{ route('applicant.dashboard') }}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Dashboard
        </a>
    </div>
</div>
@endsection

