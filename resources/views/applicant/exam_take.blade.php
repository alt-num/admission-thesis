@extends('layouts.applicant')

@section('title', 'Taking Exam - ESSU Applicant Portal')

@push('head')
<script src="/js/alpine.js" defer></script>
@if(config('anticheat.enabled', true))
<script src="/js/anticheat-manager.js"></script>
@endif
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div x-data="examInterface()" x-init="init()" x-cloak class="fixed inset-0 bg-gray-100 flex flex-col">
    
    <!-- Fixed Header with Timer -->
    <header class="bg-white shadow-md px-4 py-3 flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-gray-900">{{ $exam->title }}</h1>
            <p class="text-sm text-gray-600">{{ $attempt->applicant->first_name }} {{ $attempt->applicant->last_name }}</p>
        </div>
        
        <!-- Timer -->
        <div class="flex items-center space-x-4">
            <div class="text-right">
                <p class="text-sm text-gray-600">Time Remaining</p>
                <p class="text-2xl font-bold" :class="timeLeft < 300 ? 'text-red-600' : 'text-green-600'" x-text="formatTime(timeLeft)"></p>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <div class="flex flex-1 overflow-hidden">
        
        <!-- Left Sidebar - Sections Navigator -->
        <aside class="w-64 bg-white border-r border-gray-200 overflow-y-auto">
            <div class="p-4">
                <h2 class="text-sm font-semibold text-gray-700 mb-3">Navigation</h2>
                
                @foreach($sections as $sectionIndex => $section)
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-2 px-2 py-1 bg-gray-100 rounded">{{ $section->name }}</h3>
                        
                        @foreach($section->subsections as $subsectionIndex => $subsection)
                            <div class="mb-1">
                                <button @click="toggleSubsection({{ $sectionIndex }}, {{ $subsectionIndex }})"
                                        :disabled="isSubmitting"
                                        class="w-full text-left px-3 py-2 text-sm rounded transition-colors flex items-center justify-between disabled:opacity-50 disabled:cursor-not-allowed"
                                        :class="currentSection === {{ $sectionIndex }} && currentSubsection === {{ $subsectionIndex }} 
                                            ? 'bg-green-100 text-green-800 font-semibold' 
                                            : 'text-gray-800 hover:bg-gray-100 font-medium'">
                                    <span>{{ $subsection->name }}</span>
                                    <svg class="h-4 w-4 transition-transform" 
                                         :class="subsectionExpanded[{{ $sectionIndex }} + '-' + {{ $subsectionIndex }}] ? 'rotate-90' : ''"
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                                <div x-show="subsectionExpanded[{{ $sectionIndex }} + '-' + {{ $subsectionIndex }}]" 
                                     class="ml-4 mt-1 space-y-1">
                                    <template x-for="(q, qIdx) in getSubsectionQuestions({{ $sectionIndex }}, {{ $subsectionIndex }})" :key="q.question_id">
                                        <button @click="goToQuestion(q.question_id)"
                                                :disabled="isSubmitting"
                                                class="w-full text-left px-3 py-1 text-xs rounded transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                                :class="currentQuestion && currentQuestion.question_id === q.question_id
                                                    ? 'bg-blue-100 text-blue-800 font-medium'
                                                    : 'text-gray-600 hover:bg-gray-50'">
                                            Q<span x-text="qIdx + 1"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </aside>

        <!-- Main Question Area -->
        <main class="flex-1 overflow-y-auto p-6">
            <div class="max-w-4xl mx-auto">
                
                <!-- Monitoring Banner -->
                @if(isset($settings) && $settings->monitoring_banner_enabled)
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-3 mb-4 flex items-center gap-2">
                    <svg class="h-5 w-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <p class="text-blue-800 text-sm font-medium">Your behavior is being monitored for security.</p>
                </div>
                @endif
                
                <!-- Question Display -->
                <template x-if="currentQuestion">
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                        <!-- Question Header -->
                        <div class="mb-4 pb-4 border-b">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-sm text-gray-600 mb-2">
                                        Question <span x-text="currentQuestionNumber"></span> of <span x-text="totalQuestions"></span>
                                    </p>
                                    <p class="text-base font-semibold text-gray-900 mb-1" x-text="currentSectionName"></p>
                                    <p class="text-sm font-medium text-blue-700" x-text="currentSubsectionTitle"></p>
                                </div>
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded">
                                    <span x-text="currentQuestion.question_type"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Question Text/Image -->
                        <div class="mb-6">
                            <template x-if="currentQuestion.question_text">
                                <p class="text-lg text-gray-900 leading-relaxed" x-html="currentQuestion.question_text"></p>
                            </template>
                            
                            <template x-if="currentQuestion.question_image_path">
                                <img :src="'/storage/' + currentQuestion.question_image_path" 
                                     alt="Question Image" 
                                     class="max-w-full h-auto rounded border mt-4">
                            </template>
                        </div>

                        <!-- Choices -->
                        <div class="space-y-3">
                            <template x-for="(choice, index) in currentQuestion.choices" :key="choice.choice_id">
                                <div @click="selectChoice(choice.choice_id)"
                                     :class="[
                                         'border rounded-lg p-4 transition-all',
                                         isSubmitting ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
                                         selectedAnswers[currentQuestion.question_id] === choice.choice_id 
                                             ? 'border-green-500 bg-green-50' 
                                             : 'border-gray-300 hover:border-blue-400 hover:bg-blue-50'
                                     ]">
                                    <div class="flex items-start">
                                        <!-- Radio Button -->
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                                                 :class="selectedAnswers[currentQuestion.question_id] === choice.choice_id 
                                                     ? 'border-green-500 bg-green-500' 
                                                     : 'border-gray-400'">
                                                <div x-show="selectedAnswers[currentQuestion.question_id] === choice.choice_id" 
                                                     class="w-2 h-2 rounded-full bg-white"></div>
                                            </div>
                                        </div>
                                        
                                        <!-- Choice Content -->
                                        <div class="ml-4 flex-1">
                                            <template x-if="choice.choice_text">
                                                <p class="text-gray-900" x-text="choice.choice_text"></p>
                                            </template>
                                            
                                            <template x-if="choice.choice_image_path">
                                                <img :src="'/storage/' + choice.choice_image_path" 
                                                     alt="Choice Image" 
                                                     class="max-w-sm h-auto rounded border mt-2">
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>

                <!-- Navigation Buttons -->
                <div class="flex justify-between items-center">
                    <button @click="previousQuestion()"
                            x-show="currentQuestionIndex > 0"
                                                :disabled="isSubmitting"
                            class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        ← Previous
                    </button>
                    
                    <div class="flex-1 text-center">
                        <p class="text-sm text-gray-600">
                            Answered: <span x-text="Object.keys(selectedAnswers).length"></span> / <span x-text="totalQuestions"></span>
                        </p>
                    </div>
                    
                    <button @click="nextQuestion()"
                            x-show="currentQuestionIndex < totalQuestions - 1"
                                                :disabled="isSubmitting"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        Next →
                    </button>
                    
                    <div x-show="currentQuestionIndex === totalQuestions - 1" class="flex flex-col items-end">
                        <p class="text-xs text-gray-600 mb-2 text-right max-w-xs">
                            To submit your exam, double-click the Finish button. Submission is final and cannot be undone.
                        </p>
                        <div class="flex flex-col items-end">
                            <button @click="handleFinishClick()"
                                                :disabled="isSubmitting"
                                    class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold disabled:bg-gray-400 disabled:cursor-not-allowed">
                                Finish Exam
                            </button>
                            <p x-show="showFinishWarning" class="mt-2 text-sm text-orange-600 font-medium">
                                Click again to finish your exam.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Exam Locked Overlay (only for normal submission, not violations) -->
    <div x-show="isSubmitting" 
         x-cloak
         class="fixed inset-0 z-[9999] bg-black bg-opacity-75 flex items-center justify-center"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 p-8 text-center pointer-events-auto">
            <svg class="h-16 w-16 text-blue-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Submitting Exam</h2>
            <p class="text-gray-700 mb-6">Please wait while your exam is being submitted...</p>
            <div class="flex justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>
        </div>
    </div>
</div>

<script>
function examInterface() {
    return {
        // Timer
        timeLeft: {{ (int) $remainingSeconds }},
        timerInterval: null,
        
        // Exam Structure
        sections: @json($sections),
        attemptId: {{ $attempt->attempt_id }},
        
        // Finish button double-click
        showFinishWarning: false,
        
        // Navigation
        currentSection: 0,
        currentSubsection: 0,
        currentQuestionIndex: 0,
        allQuestions: [],
        subsectionExpanded: {},
        
        // Answers
        selectedAnswers: @json($existingAnswers->mapWithKeys(function($answer, $key) {
            return [$answer->question_id => $answer->choice_id];
        })),
        
        // Exam state
        isSubmitting: false,
        
        // Double-click to finish
        finishClickCount: 0,
        finishClickTimer: null,
        
        // Computed
        get currentQuestion() {
            return this.allQuestions[this.currentQuestionIndex] || null;
        },
        
        get totalQuestions() {
            return this.allQuestions.length;
        },
        
        get currentQuestionNumber() {
            return this.currentQuestionIndex + 1;
        },
        
        get currentSectionName() {
            if (!this.currentQuestion) return '';
            return this.sections[this.currentSection]?.name || '';
        },
        
        get currentSubsectionTitle() {
            if (!this.currentQuestion) return '';
            return this.sections[this.currentSection]?.subsections[this.currentSubsection]?.name || '';
        },
        
        init() {
            // Flatten all questions into a single array
            this.flattenQuestions();
            
            // Initialize subsection expanded states
            this.sections.forEach((section, sIdx) => {
                section.subsections.forEach((subsection, ssIdx) => {
                    const key = sIdx + '-' + ssIdx;
                    this.subsectionExpanded[key] = true; // Default to expanded
                });
            });
            
            // Start timer
            this.startTimer();
            
            // Initialize anti-cheat manager (only on exam pages)
            @php
                $antiCheatSettings = \App\Services\AntiCheatSettingsService::getSettingsArray();
                // Check both global setting and per-exam setting
                $antiCheatEnabled = $antiCheatSettings['enabled'] && ($antiCheatEnabled ?? true);
            @endphp
            @if($antiCheatEnabled)
            if (typeof AntiCheatManager !== 'undefined') {
                this.anticheatManager = new AntiCheatManager({
                    enabled: {{ $antiCheatSettings['enabled'] ? 'true' : 'false' }},
                    attemptId: this.attemptId,
                    logEndpoint: '{{ route('applicant.exam.anticheat.log') }}',
                    csrfToken: '{{ csrf_token() }}',
                    features: {
                        tabSwitchDetection: {{ $antiCheatSettings['tab_switch_detection'] ? 'true' : 'false' }},
                        focusLossViolations: {{ $antiCheatSettings['focus_loss_violations'] ? 'true' : 'false' }},
                        copyPasteBlocking: {{ $antiCheatSettings['copy_paste_blocking'] ? 'true' : 'false' }},
                        rightClickBlocking: {{ $antiCheatSettings['right_click_blocking'] ? 'true' : 'false' }},
                        devtoolsHotkeyBlocking: {{ $antiCheatSettings['devtools_hotkey_blocking'] ? 'true' : 'false' }},
                    },
                });
                this.anticheatManager.init();
            }
            @endif
            
            // Prevent page reload during active exam
            window.addEventListener('beforeunload', (e) => {
                if (this.timeLeft > 0 && !this.isSubmitting) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        },
        
        flattenQuestions() {
            this.allQuestions = [];
            this.sections.forEach((section, sIdx) => {
                section.subsections.forEach((subsection, ssIdx) => {
                    subsection.questions.forEach(question => {
                        this.allQuestions.push(Object.assign({}, question, {
                            sectionIndex: sIdx,
                            subsectionIndex: ssIdx
                        }));
                    });
                });
            });
        },
        
        startTimer() {
            this.timerInterval = setInterval(() => {
                if (this.timeLeft > 0) {
                    this.timeLeft--;
                } else {
                    this.autoSubmit();
                }
            }, 1000);
        },
        
        formatTime(seconds) {
            if (seconds < 0) seconds = 0;
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            
            if (hours > 0) {
                return `${hours}:${String(minutes).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
            }
            return `${minutes}:${String(secs).padStart(2, '0')}`;
        },
        
        toggleSubsection(sectionIdx, subsectionIdx) {
            const key = sectionIdx + '-' + subsectionIdx;
            this.subsectionExpanded[key] = !this.subsectionExpanded[key];
        },
        
        getSubsectionQuestions(sectionIdx, subsectionIdx) {
            return this.allQuestions.filter(q => 
                q.sectionIndex === sectionIdx && q.subsectionIndex === subsectionIdx
            );
        },
        
        goToQuestion(questionId) {
            if (this.isSubmitting) {
                return;
            }
            
            const questionIdx = this.allQuestions.findIndex(q => q.question_id === questionId);
            if (questionIdx !== -1) {
                this.currentQuestionIndex = questionIdx;
                this.updateCurrentSection();
            }
        },
        
        goToSubsection(sectionIdx, subsectionIdx) {
            if (this.isSubmitting) {
                return;
            }
            
            this.currentSection = sectionIdx;
            this.currentSubsection = subsectionIdx;
            
            // Find the first question in this subsection
            const questionIdx = this.allQuestions.findIndex(q => 
                q.sectionIndex === sectionIdx && q.subsectionIndex === subsectionIdx
            );
            
            if (questionIdx !== -1) {
                this.currentQuestionIndex = questionIdx;
            }
        },
        
        nextQuestion() {
            if (this.isSubmitting) {
                return;
            }
            
            if (this.currentQuestionIndex < this.totalQuestions - 1) {
                this.currentQuestionIndex++;
                this.updateCurrentSection();
            }
        },
        
        previousQuestion() {
            if (this.isSubmitting) {
                return;
            }
            
            if (this.currentQuestionIndex > 0) {
                this.currentQuestionIndex--;
                this.updateCurrentSection();
            }
        },
        
        updateCurrentSection() {
            const question = this.allQuestions[this.currentQuestionIndex];
            if (question) {
                this.currentSection = question.sectionIndex;
                this.currentSubsection = question.subsectionIndex;
            }
        },
        
        async selectChoice(choiceId) {
            // Prevent answer changes if submitting
            if (this.isSubmitting) {
                return;
            }
            
            const questionId = this.currentQuestion.question_id;
            this.selectedAnswers[questionId] = choiceId;
            
            // Auto-save via fetch
            try {
                const response = await fetch('{{ route('applicant.exam.answer') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        attempt_id: this.attemptId,
                        question_id: questionId,
                        choice_id: choiceId
                    })
                });
                
                const data = await response.json();
                
                if (!data.success) {
                    console.error('Failed to save answer:', data.message);
                    // If exam is finished, prevent further changes
                    if (data.message && data.message.includes('finished')) {
                        this.isSubmitting = true;
                    }
                }
            } catch (error) {
                console.error('Error saving answer:', error);
            }
        },
        
        handleFinishClick() {
            // Prevent if submitting
            if (this.isSubmitting) {
                return;
            }
            
            // Clear any existing timer
            if (this.finishClickTimer) {
                clearTimeout(this.finishClickTimer);
            }
            
            this.finishClickCount++;
            
            if (this.finishClickCount === 1) {
                // First click - show warning message
                this.showFinishWarning = true;
                
                // Reset counter after 1.5 seconds
                this.finishClickTimer = setTimeout(() => {
                    this.finishClickCount = 0;
                    this.showFinishWarning = false;
                }, 1500);
            } else if (this.finishClickCount === 2) {
                // Second click within 1.5 seconds - submit exam
                this.finishClickCount = 0;
                this.showFinishWarning = false;
                clearTimeout(this.finishClickTimer);
                this.submitExam();
            }
        },
        
        async submitExam() {
            if (this.isSubmitting) {
                return;
            }
            
            this.isSubmitting = true;
            clearInterval(this.timerInterval);
            
            try {
                // Submit via POST request directly (no form submission, no navigation)
                const response = await fetch('{{ route('applicant.exam.finish') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                });
                
                if (response.ok) {
                    // Success - redirect to results
                    window.location.href = '{{ route('applicant.exam.results') }}';
                } else {
                    const data = await response.json();
                    console.error('Submit error:', data);
                    // Re-enable on error
                    this.isSubmitting = false;
                }
            } catch (error) {
                console.error('Submit error:', error);
                // Re-enable on error
                this.isSubmitting = false;
            }
        },
        
        autoSubmit() {
            // Time expired - submit automatically
            if (this.isSubmitting) {
                return;
            }
            
            this.submitExam();
        },
    };
}
</script>
@endsection

