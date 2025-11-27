@extends('layouts.applicant')

@section('title', 'Taking Exam - ESSU Applicant Portal')

@push('head')
<script src="/js/alpine.js" defer></script>
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
                                        class="w-full text-left px-3 py-2 text-sm rounded transition-colors flex items-center justify-between"
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
                                                class="w-full text-left px-3 py-1 text-xs rounded transition-colors"
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
                                     class="border rounded-lg p-4 cursor-pointer transition-all"
                                     :class="selectedAnswers[currentQuestion.question_id] === choice.choice_id 
                                         ? 'border-green-500 bg-green-50' 
                                         : 'border-gray-300 hover:border-blue-400 hover:bg-blue-50'">
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
                            class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        ← Previous
                    </button>
                    
                    <div class="flex-1 text-center">
                        <p class="text-sm text-gray-600">
                            Answered: <span x-text="Object.keys(selectedAnswers).length"></span> / <span x-text="totalQuestions"></span>
                        </p>
                    </div>
                    
                    <button @click="nextQuestion()"
                            x-show="currentQuestionIndex < totalQuestions - 1"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Next →
                    </button>
                    
                    <button @click="confirmSubmit()"
                            x-show="currentQuestionIndex === totalQuestions - 1"
                            class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold">
                        Submit Exam
                    </button>
                </div>
            </div>
        </main>
    </div>

    <!-- Hidden Form for Submission -->
    <form id="finishExamForm" method="POST" action="{{ route('applicant.exam.finish') }}" style="display: none;">
        @csrf
    </form>
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
            
            // Prevent page reload
            window.addEventListener('beforeunload', (e) => {
                if (this.timeLeft > 0) {
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
            const questionIdx = this.allQuestions.findIndex(q => q.question_id === questionId);
            if (questionIdx !== -1) {
                this.currentQuestionIndex = questionIdx;
                this.updateCurrentSection();
            }
        },
        
        goToSubsection(sectionIdx, subsectionIdx) {
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
            if (this.currentQuestionIndex < this.totalQuestions - 1) {
                this.currentQuestionIndex++;
                this.updateCurrentSection();
            }
        },
        
        previousQuestion() {
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
                }
            } catch (error) {
                console.error('Error saving answer:', error);
            }
        },
        
        confirmSubmit() {
            const unanswered = this.totalQuestions - Object.keys(this.selectedAnswers).length;
            
            if (unanswered > 0) {
                if (!confirm(`You have ${unanswered} unanswered question(s). Are you sure you want to submit?`)) {
                    return;
                }
            }
            
            if (confirm('Are you sure you want to submit your exam? You cannot change your answers after submission.')) {
                this.submitExam();
            }
        },
        
        submitExam() {
            clearInterval(this.timerInterval);
            document.getElementById('finishExamForm').submit();
        },
        
        autoSubmit() {
            clearInterval(this.timerInterval);
            alert('Time is up! Your exam will be submitted automatically.');
            document.getElementById('finishExamForm').submit();
        }
    };
}
</script>
@endsection

