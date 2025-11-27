@extends('layouts.admission')

@section('title', 'Exam Editor: ' . $exam->title . ' - ESSU Admission System')

@php
use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
                <a href="{{ route('admission.exams.index') }}" class="hover:text-gray-900">← Back to Exams</a>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Exam Editor</h1>
            <div class="mt-2 flex items-center space-x-4">
                <p class="text-sm text-gray-600">{{ $exam->title }}</p>
                @if($exam->is_active)
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Active
                    </span>
                @else
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                        Draft
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Two Column Layout -->
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Left Column - Sections List -->
        <div class="bg-white rounded-lg shadow p-6 lg:w-64 flex-shrink-0">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Sections</h2>
                <button onclick="window.openAddSectionModal()" 
                        class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Section
                </button>
            </div>
            
            <div class="space-y-2">
                @forelse($exam->sections as $section)
                    <a href="{{ route('admission.exams.editor', ['exam' => $exam->exam_id, 'section_id' => $section->section_id]) }}"
                       class="block p-3 rounded-lg border transition-colors {{ $selectedSection && $selectedSection->section_id === $section->section_id ? 'bg-indigo-50 border-indigo-300' : 'border-gray-200 hover:bg-gray-50' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">{{ $section->name }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $section->subsections->count() }} subsection(s)</div>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                #{{ $section->order_no }}
                            </span>
                        </div>
                    </a>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">No sections yet. Create one to get started.</p>
                @endforelse
            </div>
        </div>

        <!-- Right Column - Section Detail -->
        <div class="bg-white rounded-lg shadow p-6 flex-1">
            @if($selectedSection)
                <!-- Section Header -->
                <div class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ $selectedSection->name }}</h2>
                        <p class="text-xs text-gray-500 mt-1">Order: {{ $selectedSection->order_no }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="window.openEditSectionModal({{ $selectedSection->section_id }}, '{{ addslashes($selectedSection->name) }}', {{ $selectedSection->order_no }})"
                                class="inline-flex items-center px-3 py-1.5 bg-gray-600 text-white text-xs font-medium rounded-lg hover:bg-gray-700 transition-colors">
                            Edit
                        </button>
                        <button onclick="window.deleteSection({{ $selectedSection->section_id }})"
                                class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-xs font-medium rounded-lg hover:bg-red-700 transition-colors">
                            Delete
                        </button>
                    </div>
                </div>

                <!-- Add Subsection and Question Buttons -->
                <div class="mb-4 flex items-center space-x-2">
                    <button onclick="window.openAddSubsectionModal({{ $selectedSection->section_id }})"
                            class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Subsection
                    </button>
                    <button onclick="window.openAddQuestionToSectionModal({{ $selectedSection->section_id }})"
                            class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Question
                    </button>
                </div>

                <!-- Subsections List -->
                <div class="space-y-4">
                    @forelse($selectedSection->subsections as $subsection)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-900">{{ $subsection->name }}</h3>
                                    <p class="text-xs text-gray-500 mt-1">Order: {{ $subsection->order_no }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button onclick="window.openEditSubsectionModal({{ $subsection->subsection_id }}, '{{ addslashes($subsection->name) }}', {{ $subsection->order_no }})"
                                            class="inline-flex items-center px-2 py-1 bg-gray-600 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">
                                        Edit
                                    </button>
                                    <button onclick="window.deleteSubsection({{ $subsection->subsection_id }})"
                                            class="inline-flex items-center px-2 py-1 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700 transition-colors">
                                        Delete
                                    </button>
                                </div>
                            </div>

                            <!-- Add Question Button -->
                            <div class="mb-3">
                                <button onclick="window.openAddQuestionModal({{ $subsection->subsection_id }})"
                                        class="inline-flex items-center px-2 py-1 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700 transition-colors">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Question
                                </button>
                            </div>

                            <!-- Questions List -->
                            <div class="space-y-3">
                                @forelse($subsection->questions as $question)
                                    <div class="bg-gray-50 rounded-lg p-3 border-l-4 border-blue-500">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2 mb-1">
                                                    <span class="text-xs font-semibold text-gray-700">Q{{ $question->order_no }}</span>
                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $question->type === 'MCQ' ? 'bg-purple-100 text-purple-800' : 'bg-orange-100 text-orange-800' }}">
                                                        {{ $question->type === 'MCQ' ? 'MCQ' : 'True/False' }}
                                                    </span>
                                                </div>
                                                <p class="text-sm text-gray-900 mt-1">{{ $question->question_text ?? '(No text)' }}</p>
                                                @if($question->question_image)
                                                    <div class="mt-2">
                                                        <img src="{{ Storage::url($question->question_image) }}" alt="Question Image" class="w-24 h-auto rounded border border-gray-300">
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex items-center space-x-1 ml-2">
                                                <button onclick="window.openEditQuestionModal({{ $question->question_id }}, '{{ addslashes($question->question_text ?? '') }}', '{{ $question->type }}', {{ $question->order_no }}, '{{ $question->question_image ? Storage::url($question->question_image) : '' }}', '{{ $question->type === 'TRUE_FALSE' ? ($question->choices->firstWhere('is_correct', true)->choice_text ?? 'True') : '' }}')"
                                                        class="inline-flex items-center px-2 py-1 bg-gray-600 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">
                                                    Edit
                                                </button>
                                                <button onclick="window.deleteQuestion({{ $question->question_id }})"
                                                        class="inline-flex items-center px-2 py-1 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700 transition-colors">
                                                    Delete
                                                </button>
                                            </div>
                                        </div>

                                        @if($question->type === 'MCQ')
                                            <!-- MCQ Choices -->
                                            <div class="mt-3 space-y-2">
                                                @forelse($question->choices as $choice)
                                                    <div class="flex items-center justify-between bg-white rounded p-2 border border-gray-200">
                                                        <div class="flex items-center space-x-2 flex-1">
                                                            <div class="flex-1">
                                                                <span class="text-xs text-gray-700">{{ $choice->choice_text ?? '(No text)' }}</span>
                                                                @if($choice->choice_image)
                                                                    <div class="mt-1">
                                                                        <img src="{{ Storage::url($choice->choice_image) }}" alt="Choice Image" class="w-16 h-auto rounded border border-gray-300">
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            @if($choice->is_correct)
                                                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                                    Correct
                                                                </span>
                                                            @endif
                                                        </div>
                                                        <div class="flex items-center space-x-1">
                                                            <button onclick="window.openEditChoiceModal({{ $choice->choice_id }}, '{{ addslashes($choice->choice_text ?? '') }}', {{ $choice->is_correct ? 'true' : 'false' }}, '{{ $choice->choice_image ? Storage::url($choice->choice_image) : '' }}', '{{ addslashes($question->question_text ?? '') }}', '{{ addslashes($selectedSection->name ?? '') }}', '{{ addslashes($subsection->name ?? '') }}')"
                                                                    class="inline-flex items-center px-2 py-0.5 bg-gray-600 text-white text-xs font-medium rounded hover:bg-gray-700 transition-colors">
                                                                Edit
                                                            </button>
                                                            <button onclick="window.deleteChoice({{ $choice->choice_id }})"
                                                                    class="inline-flex items-center px-2 py-0.5 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700 transition-colors">
                                                                Delete
                                                            </button>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <p class="text-xs text-gray-500">No choices yet.</p>
                                                @endforelse
                                                <button onclick="window.openAddChoiceModal({{ $question->question_id }}, '{{ addslashes($question->question_text ?? '') }}', '{{ addslashes($selectedSection->name ?? '') }}', '{{ addslashes($subsection->name ?? '') }}')"
                                                        class="mt-2 inline-flex items-center px-2 py-1 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700 transition-colors">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                    Add Choice
                                                </button>
                                            </div>
                                        @else
                                            <!-- True/False Display -->
                                            <div class="mt-3 space-y-2">
                                                @php
                                                    $trueChoice = $question->choices->firstWhere('choice_text', 'True');
                                                    $falseChoice = $question->choices->firstWhere('choice_text', 'False');
                                                @endphp
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-2">
                                                        <button type="button" 
                                                                onclick="window.setTrueFalseCorrect({{ $question->question_id }}, 'True', {{ $trueChoice && $trueChoice->is_correct ? 'true' : 'false' }})"
                                                                class="px-3 py-1.5 text-xs font-medium rounded transition-colors {{ $trueChoice && $trueChoice->is_correct ? 'bg-green-100 text-green-800 border-2 border-green-500' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                                            ✓ True
                                                        </button>
                                                        <button type="button"
                                                                onclick="window.setTrueFalseCorrect({{ $question->question_id }}, 'False', {{ $falseChoice && $falseChoice->is_correct ? 'true' : 'false' }})"
                                                                class="px-3 py-1.5 text-xs font-medium rounded transition-colors {{ $falseChoice && $falseChoice->is_correct ? 'bg-green-100 text-green-800 border-2 border-green-500' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                                            ✓ False
                                                        </button>
                                                    </div>
                                                    <span class="text-xs text-gray-500">Click to set correct answer</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <p class="text-xs text-gray-500 text-center py-2">No questions yet. Add one to get started.</p>
                                @endforelse
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No subsections yet. Create one to add questions.</p>
                    @endforelse
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No section selected</h3>
                    <p class="mt-1 text-sm text-gray-500">Select a section from the left or create one to begin.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modals -->
@include('admission.exams.editor-modals')

@endsection

<script>
// Global modal functions - must be defined directly on window
const examId = {{ $exam->exam_id }};
const baseUrl = '/admission/exams/' + examId;

// ===== INLINE DOM UPDATE FUNCTIONS =====
// Reload just the section detail panel without full page refresh
async function reloadSectionDetail() {
    const currentSectionId = {{ $selectedSection ? $selectedSection->section_id : 'null' }};
    if (!currentSectionId) {
        // No section selected, just reload page
        window.location.reload();
        return;
    }
    
    try {
        // Save current scroll position
        const scrollY = window.scrollY;
        
        // Fetch fresh HTML for the current page
        const response = await fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Extract the section detail panel from the response
        const newRightPanel = doc.querySelector('.bg-white.rounded-lg.shadow.p-6.flex-1');
        const currentRightPanel = document.querySelector('.bg-white.rounded-lg.shadow.p-6.flex-1');
        
        if (newRightPanel && currentRightPanel) {
            // Replace the content
            currentRightPanel.innerHTML = newRightPanel.innerHTML;
            
            // Restore scroll position
            window.scrollTo({ top: scrollY, behavior: 'instant' });
        } else {
            // Fallback to full reload
            window.location.reload();
        }
    } catch (error) {
        console.error('Error reloading section:', error);
        window.location.reload();
    }
}

// Reload sections list (left panel) without full page refresh
async function reloadSectionsList() {
    try {
        const scrollY = window.scrollY;
        
        const response = await fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const html = await response.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        
        // Extract the sections list from the response
        const newLeftPanel = doc.querySelector('.bg-white.rounded-lg.shadow.p-6.lg\\:w-64');
        const currentLeftPanel = document.querySelector('.bg-white.rounded-lg.shadow.p-6.lg\\:w-64');
        
        if (newLeftPanel && currentLeftPanel) {
            currentLeftPanel.innerHTML = newLeftPanel.innerHTML;
            window.scrollTo({ top: scrollY, behavior: 'instant' });
        } else {
            window.location.reload();
        }
    } catch (error) {
        console.error('Error reloading sections list:', error);
        window.location.reload();
    }
}

// Section Modals
window.openAddSectionModal = function() {
    document.getElementById('addSectionModal').classList.remove('hidden');
};

window.closeAddSectionModal = function() {
    document.getElementById('addSectionModal').classList.add('hidden');
};

window.openEditSectionModal = function(sectionId, name, orderNo) {
    document.getElementById('editSectionId').value = sectionId;
    document.getElementById('editSectionName').value = name;
    document.getElementById('editSectionOrderNo').value = orderNo;
    document.getElementById('editSectionModal').classList.remove('hidden');
};

window.closeEditSectionModal = function() {
    document.getElementById('editSectionModal').classList.add('hidden');
};

window.deleteSection = async function(sectionId) {
    if (!confirm('Are you sure you want to delete this section? All subsections, questions, and choices will also be deleted.')) {
        return;
    }

    try {
        const response = await fetch(`${baseUrl}/sections/${sectionId}/delete`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });

        const data = await response.json();
        if (data.success) {
            // Navigate to editor without section selection
            window.location.href = `{{ route('admission.exams.editor', ['exam' => $exam->exam_id]) }}`;
        } else {
            alert('Error: ' + (data.message || 'Failed to delete section'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
};

// Subsection Modals
window.openAddSubsectionModal = function(sectionId) {
    // Only set the section ID, do NOT reset the form
    // This preserves user input if they accidentally close and reopen the modal
    document.getElementById('addSubsectionSectionId').value = sectionId;
    document.getElementById('addSubsectionModal').classList.remove('hidden');
};

window.closeAddSubsectionModal = function() {
    document.getElementById('addSubsectionModal').classList.add('hidden');
};

window.openEditSubsectionModal = function(subsectionId, name, orderNo) {
    document.getElementById('editSubsectionId').value = subsectionId;
    document.getElementById('editSubsectionName').value = name;
    document.getElementById('editSubsectionOrderNo').value = orderNo;
    document.getElementById('editSubsectionModal').classList.remove('hidden');
};

window.closeEditSubsectionModal = function() {
    document.getElementById('editSubsectionModal').classList.add('hidden');
};

window.deleteSubsection = async function(subsectionId) {
    if (!confirm('Are you sure you want to delete this subsection? All questions and choices will also be deleted.')) {
        return;
    }

    try {
        const response = await fetch(`${baseUrl}/subsections/${subsectionId}/delete`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });

        const data = await response.json();
        if (data.success) {
            // Reload just the section detail panel
            await reloadSectionDetail();
        } else {
            alert('Error: ' + (data.message || 'Failed to delete subsection'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
};

// Question Modals
window.openAddQuestionModal = function(subsectionId) {
    // Reset the form first
    const form = document.getElementById('addQuestionForm');
    if (form) {
        form.reset();
    }
    
    // Set the required hidden values
    document.getElementById('addQuestionSubsectionId').value = subsectionId;
    document.getElementById('addQuestionSectionId').value = '';
    document.getElementById('addQuestionToSection').value = '0';
    
    // Reset type dropdown to MCQ
    const typeSelect = document.getElementById('addQuestionType');
    if (typeSelect) {
        typeSelect.value = 'MCQ';
    }
    
    // Hide True/False options
    const trueFalseOptions = document.getElementById('addQuestionTrueFalseOptions');
    if (trueFalseOptions) {
        trueFalseOptions.classList.add('hidden');
    }
    
    // Reset correct_answer to False (default)
    const correctAnswerSelect = trueFalseOptions ? trueFalseOptions.querySelector('select[name="correct_answer"]') : null;
    if (correctAnswerSelect) {
        correctAnswerSelect.value = 'False';
    }
    
    // Clear any file inputs
    const questionImageInput = form ? form.querySelector('input[name="question_image"]') : null;
    if (questionImageInput) {
        questionImageInput.value = '';
    }
    
    // Clear textarea
    const questionTextArea = form ? form.querySelector('textarea[name="question_text"]') : null;
    if (questionTextArea) {
        questionTextArea.value = '';
    }
    
    // Clear order number
    const orderNoInput = form ? form.querySelector('input[name="order_no"]') : null;
    if (orderNoInput) {
        orderNoInput.value = '';
    }
    
    document.getElementById('addQuestionModal').classList.remove('hidden');
};

window.openAddQuestionToSectionModal = function(sectionId) {
    // Reset the form first
    const form = document.getElementById('addQuestionForm');
    if (form) {
        form.reset();
    }
    
    // Set the required hidden values
    document.getElementById('addQuestionSubsectionId').value = '';
    document.getElementById('addQuestionSectionId').value = sectionId;
    document.getElementById('addQuestionToSection').value = '1';
    
    // Reset type dropdown to MCQ
    const typeSelect = document.getElementById('addQuestionType');
    if (typeSelect) {
        typeSelect.value = 'MCQ';
    }
    
    // Hide True/False options
    const trueFalseOptions = document.getElementById('addQuestionTrueFalseOptions');
    if (trueFalseOptions) {
        trueFalseOptions.classList.add('hidden');
    }
    
    // Reset correct_answer to False (default)
    const correctAnswerSelect = trueFalseOptions ? trueFalseOptions.querySelector('select[name="correct_answer"]') : null;
    if (correctAnswerSelect) {
        correctAnswerSelect.value = 'False';
    }
    
    // Clear any file inputs
    const questionImageInput = form ? form.querySelector('input[name="question_image"]') : null;
    if (questionImageInput) {
        questionImageInput.value = '';
    }
    
    // Clear textarea
    const questionTextArea = form ? form.querySelector('textarea[name="question_text"]') : null;
    if (questionTextArea) {
        questionTextArea.value = '';
    }
    
    // Clear order number
    const orderNoInput = form ? form.querySelector('input[name="order_no"]') : null;
    if (orderNoInput) {
        orderNoInput.value = '';
    }
    
    document.getElementById('addQuestionModal').classList.remove('hidden');
};

window.closeAddQuestionModal = function() {
    document.getElementById('addQuestionModal').classList.add('hidden');
};

window.openEditQuestionModal = function(questionId, questionText, type, orderNo, imageUrl = '', correctAnswer = '') {
    document.getElementById('editQuestionId').value = questionId;
    document.getElementById('editQuestionText').value = questionText;
    document.getElementById('editQuestionType').value = type;
    document.getElementById('editQuestionOrderNo').value = orderNo;
    
    // Handle image preview
    const imagePreview = document.getElementById('editQuestionImagePreview');
    const imageThumbnail = document.getElementById('editQuestionImageThumbnail');
    if (imageUrl) {
        imageThumbnail.src = imageUrl;
        imagePreview.classList.remove('hidden');
    } else {
        imagePreview.classList.add('hidden');
    }
    
    // Reset the remove checkbox
    const removeCheckbox = document.querySelector('input[name="remove_question_image"]');
    if (removeCheckbox) removeCheckbox.checked = false;
    
    // Handle TRUE_FALSE type
    if (type === 'TRUE_FALSE') {
        document.getElementById('editQuestionTrueFalseOptions').classList.remove('hidden');
        if (correctAnswer) {
            document.getElementById('editQuestionCorrectAnswer').value = correctAnswer;
        }
    } else {
        document.getElementById('editQuestionTrueFalseOptions').classList.add('hidden');
    }
    
    document.getElementById('editQuestionModal').classList.remove('hidden');
};

// Toggle TRUE/FALSE options visibility
window.toggleTrueFalseOptions = function(mode) {
    const typeSelect = document.getElementById(mode + 'QuestionType');
    const options = document.getElementById(mode + 'QuestionTrueFalseOptions');
    
    if (typeSelect.value === 'TRUE_FALSE') {
        options.classList.remove('hidden');
    } else {
        options.classList.add('hidden');
    }
};

// Set TRUE/FALSE correct answer
window.setTrueFalseCorrect = async function(questionId, answer, isCurrentlyCorrect) {
    if (isCurrentlyCorrect) {
        return; // Already correct, no need to change
    }
    
    try {
        const formData = new FormData();
        formData.append('correct_answer', answer);
        
        const response = await fetch(`${baseUrl}/questions/${questionId}/toggle-true-false`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: formData
        });

        const data = await response.json();
        if (data.success) {
            // Reload just the section detail panel
            await reloadSectionDetail();
        } else {
            alert('Error: ' + (data.message || 'Failed to update correct answer'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
};

window.closeEditQuestionModal = function() {
    document.getElementById('editQuestionModal').classList.add('hidden');
};

window.deleteQuestion = async function(questionId) {
    if (!confirm('Are you sure you want to delete this question? All choices will also be deleted.')) {
        return;
    }

    try {
        const response = await fetch(`${baseUrl}/questions/${questionId}/delete`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });

        const data = await response.json();
        if (data.success) {
            // Reload just the section detail panel
            await reloadSectionDetail();
        } else {
            alert('Error: ' + (data.message || 'Failed to delete question'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
};

// Choice Modals
window.openAddChoiceModal = function(questionId, questionText, sectionName, subsectionName) {
    // Reset the form first
    const form = document.getElementById('addChoiceForm');
    if (form) {
        form.reset();
    }
    
    // Set the required hidden value
    document.getElementById('addChoiceQuestionId').value = questionId;
    
    // Populate question preview
    const questionPreviewText = document.getElementById('addChoiceQuestionText');
    const questionPreviewSection = document.getElementById('addChoiceQuestionSection');
    const questionPreviewSubsection = document.getElementById('addChoiceQuestionSubsection');
    
    if (questionPreviewText) {
        questionPreviewText.textContent = questionText || '(No question text)';
    }
    if (questionPreviewSection) {
        questionPreviewSection.textContent = sectionName ? `Section: ${sectionName}` : '';
    }
    if (questionPreviewSubsection) {
        questionPreviewSubsection.textContent = subsectionName ? `Subsection: ${subsectionName}` : '';
    }
    
    // Clear choice text input
    const choiceTextInput = form ? form.querySelector('input[name="choice_text"]') : null;
    if (choiceTextInput) {
        choiceTextInput.value = '';
    }
    
    // Clear file input
    const choiceImageInput = form ? form.querySelector('input[name="choice_image"]') : null;
    if (choiceImageInput) {
        choiceImageInput.value = '';
    }
    
    // Uncheck is_correct checkbox
    const isCorrectCheckbox = form ? form.querySelector('input[name="is_correct"][type="checkbox"]') : null;
    if (isCorrectCheckbox) {
        isCorrectCheckbox.checked = false;
    }
    
    // Also ensure the hidden is_correct field is set to 0
    const hiddenIsCorrect = form ? form.querySelector('input[name="is_correct"][type="hidden"]') : null;
    if (hiddenIsCorrect) {
        hiddenIsCorrect.value = '0';
    }
    
    document.getElementById('addChoiceModal').classList.remove('hidden');
};

window.closeAddChoiceModal = function() {
    document.getElementById('addChoiceModal').classList.add('hidden');
};

window.openEditChoiceModal = function(choiceId, choiceText, isCorrect, imageUrl = '', questionText = '', sectionName = '', subsectionName = '') {
    document.getElementById('editChoiceId').value = choiceId;
    document.getElementById('editChoiceText').value = choiceText;
    document.getElementById('editChoiceIsCorrect').checked = isCorrect;
    
    // Populate question preview
    const questionPreviewText = document.getElementById('editChoiceQuestionText');
    const questionPreviewSection = document.getElementById('editChoiceQuestionSection');
    const questionPreviewSubsection = document.getElementById('editChoiceQuestionSubsection');
    
    if (questionPreviewText) {
        questionPreviewText.textContent = questionText || '(No question text)';
    }
    if (questionPreviewSection) {
        questionPreviewSection.textContent = sectionName ? `Section: ${sectionName}` : '';
    }
    if (questionPreviewSubsection) {
        questionPreviewSubsection.textContent = subsectionName ? `Subsection: ${subsectionName}` : '';
    }
    
    // Handle image preview
    const imagePreview = document.getElementById('editChoiceImagePreview');
    const imageThumbnail = document.getElementById('editChoiceImageThumbnail');
    if (imageUrl) {
        imageThumbnail.src = imageUrl;
        imagePreview.classList.remove('hidden');
    } else {
        imagePreview.classList.add('hidden');
    }
    
    // Reset the remove checkbox
    const removeCheckbox = document.querySelector('input[name="remove_choice_image"]');
    if (removeCheckbox) removeCheckbox.checked = false;
    
    document.getElementById('editChoiceModal').classList.remove('hidden');
};

window.closeEditChoiceModal = function() {
    document.getElementById('editChoiceModal').classList.add('hidden');
};

window.deleteChoice = async function(choiceId) {
    if (!confirm('Are you sure you want to delete this choice?')) {
        return;
    }

    try {
        const response = await fetch(`${baseUrl}/choices/${choiceId}/delete`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        });

        const data = await response.json();
        if (data.success) {
            // Reload just the section detail panel
            await reloadSectionDetail();
        } else {
            alert('Error: ' + (data.message || 'Failed to delete choice'));
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
};

// Form Submission Helper
async function submitForm(formId, url, method = 'POST', keepModalOpen = false) {
    const form = document.getElementById(formId);
    const formData = new FormData(form);

    try {
        const response = await fetch(url, {
            method: method,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                // Note: Do NOT set Content-Type header - browser will set it automatically with boundary for multipart/form-data
            },
            body: formData
        });

        const data = await response.json();
        if (data.success) {
            // Reset subsection form on successful creation (before closing)
            if (formId === 'addSubsectionForm') {
                const form = document.getElementById('addSubsectionForm');
                if (form) {
                    form.reset();
                }
            }
            
            // Close the modal only if not keeping it open
            if (!keepModalOpen) {
                const modalMap = {
                    'addSectionForm': 'closeAddSectionModal',
                    'editSectionForm': 'closeEditSectionModal',
                    'addSubsectionForm': 'closeAddSubsectionModal',
                    'editSubsectionForm': 'closeEditSubsectionModal',
                    'addQuestionForm': 'closeAddQuestionModal',
                    'editQuestionForm': 'closeEditQuestionModal',
                    'addChoiceForm': 'closeAddChoiceModal',
                    'editChoiceForm': 'closeEditChoiceModal',
                };
                
                if (modalMap[formId] && window[modalMap[formId]]) {
                    window[modalMap[formId]]();
                }
            } else {
                // Reset form fields while keeping modal open
                if (formId === 'addQuestionForm') {
                    resetQuestionForm();
                } else if (formId === 'addChoiceForm') {
                    resetChoiceForm();
                }
            }
            
            // Handle different form types
            if (formId === 'addSectionForm') {
                // For new section, navigate to it
                if (data.data && data.data.section_id) {
                    window.location.href = `{{ route('admission.exams.editor', ['exam' => $exam->exam_id]) }}?section_id=${data.data.section_id}`;
                } else {
                    await reloadSectionsList();
                }
            } else if (formId === 'editSectionForm') {
                // For edited section, reload sections list
                await reloadSectionsList();
            } else {
                // For subsection/question/choice operations, reload detail panel
                await reloadSectionDetail();
            }
        } else {
            const errors = data.errors || {};
            let errorMsg = data.message || 'An error occurred';
            if (Object.keys(errors).length > 0) {
                errorMsg += '\n' + Object.values(errors).flat().join('\n');
            }
            alert('Error: ' + errorMsg);
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}

// Reset Question Form (for Create & Add Another)
function resetQuestionForm() {
    const form = document.getElementById('addQuestionForm');
    if (!form) return;
    
    // Preserve hidden values before reset
    const subsectionId = document.getElementById('addQuestionSubsectionId').value;
    const sectionId = document.getElementById('addQuestionSectionId').value;
    const toSection = document.getElementById('addQuestionToSection').value;
    
    // Reset the form
    form.reset();
    
    // Restore hidden values
    document.getElementById('addQuestionSubsectionId').value = subsectionId;
    document.getElementById('addQuestionSectionId').value = sectionId;
    document.getElementById('addQuestionToSection').value = toSection;
    
    // Reset type dropdown to MCQ
    const typeSelect = document.getElementById('addQuestionType');
    if (typeSelect) {
        typeSelect.value = 'MCQ';
    }
    
    // Hide True/False options
    const trueFalseOptions = document.getElementById('addQuestionTrueFalseOptions');
    if (trueFalseOptions) {
        trueFalseOptions.classList.add('hidden');
    }
    
    // Reset correct_answer to False (default)
    const correctAnswerSelect = trueFalseOptions ? trueFalseOptions.querySelector('select[name="correct_answer"]') : null;
    if (correctAnswerSelect) {
        correctAnswerSelect.value = 'False';
    }
    
    // Clear any file inputs
    const questionImageInput = form.querySelector('input[name="question_image"]');
    if (questionImageInput) {
        questionImageInput.value = '';
    }
    
    // Clear textarea
    const questionTextArea = form.querySelector('textarea[name="question_text"]');
    if (questionTextArea) {
        questionTextArea.value = '';
    }
    
    // Clear order number
    const orderNoInput = form.querySelector('input[name="order_no"]');
    if (orderNoInput) {
        orderNoInput.value = '';
    }
}

// Reset Choice Form (for Create & Add Another)
function resetChoiceForm() {
    const form = document.getElementById('addChoiceForm');
    if (!form) return;
    
    // Preserve hidden value before reset
    const questionId = document.getElementById('addChoiceQuestionId').value;
    
    // Reset the form
    form.reset();
    
    // Restore hidden value
    document.getElementById('addChoiceQuestionId').value = questionId;
    
    // Clear choice text input
    const choiceTextInput = form.querySelector('input[name="choice_text"]');
    if (choiceTextInput) {
        choiceTextInput.value = '';
    }
    
    // Clear file input
    const choiceImageInput = form.querySelector('input[name="choice_image"]');
    if (choiceImageInput) {
        choiceImageInput.value = '';
    }
    
    // Uncheck is_correct checkbox
    const isCorrectCheckbox = form.querySelector('input[name="is_correct"][type="checkbox"]');
    if (isCorrectCheckbox) {
        isCorrectCheckbox.checked = false;
    }
    
    // Also ensure the hidden is_correct field is set to 0
    const hiddenIsCorrect = form.querySelector('input[name="is_correct"][type="hidden"]');
    if (hiddenIsCorrect) {
        hiddenIsCorrect.value = '0';
    }
}

// Form event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Section forms
    document.getElementById('addSectionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitForm('addSectionForm', `${baseUrl}/sections/create`);
    });

    document.getElementById('editSectionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const sectionId = document.getElementById('editSectionId').value;
        submitForm('editSectionForm', `${baseUrl}/sections/${sectionId}/update`);
    });

    // Subsection forms
    document.getElementById('addSubsectionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const sectionId = document.getElementById('addSubsectionSectionId').value;
        submitForm('addSubsectionForm', `${baseUrl}/sections/${sectionId}/subsections/create`);
    });

    document.getElementById('editSubsectionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const subsectionId = document.getElementById('editSubsectionId').value;
        submitForm('editSubsectionForm', `${baseUrl}/subsections/${subsectionId}/update`);
    });

    // Question forms
    document.getElementById('addQuestionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const toSection = document.getElementById('addQuestionToSection').value === '1';
        if (toSection) {
            const sectionId = document.getElementById('addQuestionSectionId').value;
            submitForm('addQuestionForm', `${baseUrl}/sections/${sectionId}/questions/create`);
        } else {
            const subsectionId = document.getElementById('addQuestionSubsectionId').value;
            submitForm('addQuestionForm', `${baseUrl}/subsections/${subsectionId}/questions/create`);
        }
    });

    document.getElementById('editQuestionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const questionId = document.getElementById('editQuestionId').value;
        submitForm('editQuestionForm', `${baseUrl}/questions/${questionId}/update`);
    });

    // Choice forms
    document.getElementById('addChoiceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const questionId = document.getElementById('addChoiceQuestionId').value;
        submitForm('addChoiceForm', `${baseUrl}/questions/${questionId}/choices`);
    });

    document.getElementById('editChoiceForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const choiceId = document.getElementById('editChoiceId').value;
        submitForm('editChoiceForm', `${baseUrl}/choices/${choiceId}/update`);
    });

    // Create & Add Another buttons
    document.getElementById('addQuestionCreateAndAddAnother').addEventListener('click', function(e) {
        e.preventDefault();
        const form = document.getElementById('addQuestionForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        const toSection = document.getElementById('addQuestionToSection').value === '1';
        if (toSection) {
            const sectionId = document.getElementById('addQuestionSectionId').value;
            submitForm('addQuestionForm', `${baseUrl}/sections/${sectionId}/questions/create`, 'POST', true);
        } else {
            const subsectionId = document.getElementById('addQuestionSubsectionId').value;
            submitForm('addQuestionForm', `${baseUrl}/subsections/${subsectionId}/questions/create`, 'POST', true);
        }
    });

    document.getElementById('addChoiceCreateAndAddAnother').addEventListener('click', function(e) {
        e.preventDefault();
        const form = document.getElementById('addChoiceForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        const questionId = document.getElementById('addChoiceQuestionId').value;
        submitForm('addChoiceForm', `${baseUrl}/questions/${questionId}/choices`, 'POST', true);
    });
});
</script>
