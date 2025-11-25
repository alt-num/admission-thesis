<?php

namespace App\Http\Controllers\Admission\ExamEditor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admission\ExamEditor\StoreQuestionRequest;
use App\Http\Requests\Admission\ExamEditor\UpdateQuestionRequest;
use App\Models\Exam;
use App\Models\ExamChoice;
use App\Models\ExamQuestion;
use App\Models\ExamSubsection;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Store a new question directly to a section (auto-creates default subsection if needed).
     */
    public function storeToSection(StoreQuestionRequest $request, Exam $exam, \App\Models\ExamSection $section)
    {
        // Validate section belongs to exam
        if ($section->exam_id !== $exam->exam_id) {
            return response()->json([
                'success' => false,
                'message' => 'Section does not belong to this exam.',
            ], 403);
        }

        // Check if section has subsections
        $subsection = $section->subsections()->first();
        
        // If no subsections exist, create a default one
        if (!$subsection) {
            $subsection = ExamSubsection::create([
                'section_id' => $section->section_id,
                'name' => 'General',
                'order_no' => 1,
            ]);
        }

        // Get the next order number if not provided
        $orderNo = $request->input('order_no');
        if (!$orderNo) {
            $maxOrder = ExamQuestion::where('subsection_id', $subsection->subsection_id)
                ->max('order_no') ?? 0;
            $orderNo = $maxOrder + 1;
        } else {
            // Shift existing questions with order_no >= new order_no
            ExamQuestion::where('subsection_id', $subsection->subsection_id)
                ->where('order_no', '>=', $orderNo)
                ->increment('order_no');
        }

        // Handle question image upload
        $questionImagePath = null;
        if ($request->hasFile('question_image')) {
            $questionImagePath = $request->file('question_image')->store('exam_questions', 'public');
        }

        $question = ExamQuestion::create([
            'exam_id' => $exam->exam_id,
            'section_id' => $subsection->section_id,
            'subsection_id' => $subsection->subsection_id,
            'question_text' => $request->input('question_text'),
            'question_image' => $questionImagePath,
            'type' => $request->input('type'),
            'order_no' => $orderNo,
        ]);

        // Automatically create True/False choices for TRUE_FALSE questions
        if ($request->input('type') === 'TRUE_FALSE') {
            $correctAnswer = $request->input('correct_answer', 'True'); // Default to 'True'
            
            ExamChoice::create([
                'question_id' => $question->question_id,
                'choice_text' => 'True',
                'is_correct' => $correctAnswer === 'True',
            ]);
            ExamChoice::create([
                'question_id' => $question->question_id,
                'choice_text' => 'False',
                'is_correct' => $correctAnswer === 'False',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Question created successfully.',
            'data' => $question->fresh(['choices']),
        ]);
    }

    /**
     * Store a new question.
     */
    public function store(StoreQuestionRequest $request, Exam $exam, ExamSubsection $subsection)
    {
        // Validate subsection belongs to exam
        if ($subsection->section->exam_id !== $exam->exam_id) {
            return response()->json([
                'success' => false,
                'message' => 'Subsection does not belong to this exam.',
            ], 403);
        }

        // Validate section_id if provided
        $sectionId = $request->input('section_id');
        if ($sectionId && $subsection->section_id !== $sectionId) {
            return response()->json([
                'success' => false,
                'message' => 'Section does not match subsection.',
            ], 403);
        }

        // Get the next order number if not provided
        $orderNo = $request->input('order_no');
        if (!$orderNo) {
            $maxOrder = ExamQuestion::where('subsection_id', $subsection->subsection_id)
                ->max('order_no') ?? 0;
            $orderNo = $maxOrder + 1;
        } else {
            // Shift existing questions with order_no >= new order_no
            ExamQuestion::where('subsection_id', $subsection->subsection_id)
                ->where('order_no', '>=', $orderNo)
                ->increment('order_no');
        }

        // Handle question image upload
        $questionImagePath = null;
        if ($request->hasFile('question_image')) {
            $questionImagePath = $request->file('question_image')->store('exam_questions', 'public');
        }

        $question = ExamQuestion::create([
            'exam_id' => $exam->exam_id,
            'section_id' => $subsection->section_id,
            'subsection_id' => $subsection->subsection_id,
            'question_text' => $request->input('question_text'),
            'question_image' => $questionImagePath,
            'type' => $request->input('type'),
            'order_no' => $orderNo,
        ]);

        // Automatically create True/False choices for TRUE_FALSE questions
        if ($request->input('type') === 'TRUE_FALSE') {
            $correctAnswer = $request->input('correct_answer', 'True'); // Default to 'True'
            
            ExamChoice::create([
                'question_id' => $question->question_id,
                'choice_text' => 'True',
                'is_correct' => $correctAnswer === 'True',
            ]);
            ExamChoice::create([
                'question_id' => $question->question_id,
                'choice_text' => 'False',
                'is_correct' => $correctAnswer === 'False',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Question created successfully.',
            'data' => $question->fresh(['choices']),
        ]);
    }

    /**
     * Update a question.
     */
    public function update(Exam $exam, ExamQuestion $question, UpdateQuestionRequest $request)
    {
        // Validate question belongs to exam
        if ($question->exam_id !== $exam->exam_id) {
            return response()->json([
                'success' => false,
                'message' => 'Question does not belong to this exam.',
            ], 403);
        }

        $oldOrderNo = $question->order_no;
        $newOrderNo = $request->input('order_no', $oldOrderNo);

        // Validate section_id and subsection_id if provided
        $sectionId = $request->input('section_id');
        $subsectionId = $request->input('subsection_id');

        if ($sectionId && $question->section_id !== $sectionId) {
            // Validate section belongs to exam
            $section = \App\Models\ExamSection::find($sectionId);
            if (!$section || $section->exam_id !== $question->exam_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Section does not belong to this exam.',
                ], 403);
            }
        }

        if ($subsectionId && $question->subsection_id !== $subsectionId) {
            // Validate subsection belongs to section and exam
            $subsection = ExamSubsection::find($subsectionId);
            if (!$subsection || $subsection->section->exam_id !== $question->exam_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subsection does not belong to this exam.',
                ], 403);
            }
        }

        // Handle order changes and subsection changes
        $oldSubsectionId = $question->subsection_id;
        $targetSubsectionId = $subsectionId ?? $oldSubsectionId;

        // If subsection changed, handle order in both old and new subsections
        if ($targetSubsectionId != $oldSubsectionId) {
            // Decrement order in old subsection for questions after this one
            ExamQuestion::where('subsection_id', $oldSubsectionId)
                ->where('question_id', '!=', $question->question_id)
                ->where('order_no', '>', $oldOrderNo)
                ->decrement('order_no');

            // Get max order in new subsection and set new order
            $maxOrder = ExamQuestion::where('subsection_id', $targetSubsectionId)
                ->max('order_no') ?? 0;
            $newOrderNo = $maxOrder + 1;
        } elseif ($newOrderNo != $oldOrderNo) {
            // Only order changed, same subsection
            if ($newOrderNo > $oldOrderNo) {
                // Moving down: decrement questions between old and new
                ExamQuestion::where('subsection_id', $targetSubsectionId)
                    ->where('question_id', '!=', $question->question_id)
                    ->where('order_no', '>', $oldOrderNo)
                    ->where('order_no', '<=', $newOrderNo)
                    ->decrement('order_no');
            } else {
                // Moving up: increment questions between new and old
                ExamQuestion::where('subsection_id', $targetSubsectionId)
                    ->where('question_id', '!=', $question->question_id)
                    ->where('order_no', '>=', $newOrderNo)
                    ->where('order_no', '<', $oldOrderNo)
                    ->increment('order_no');
            }
        }

        // Handle question image upload and removal
        $questionImagePath = $question->question_image;
        
        // Remove existing image if requested
        if ($request->boolean('remove_question_image')) {
            if ($questionImagePath) {
                \Storage::disk('public')->delete($questionImagePath);
            }
            $questionImagePath = null;
        }
        
        // Upload new image if provided
        if ($request->hasFile('question_image')) {
            // Delete old image if exists
            if ($questionImagePath) {
                \Storage::disk('public')->delete($questionImagePath);
            }
            $questionImagePath = $request->file('question_image')->store('exam_questions', 'public');
        }

        $oldType = $question->type;
        $newType = $request->input('type');

        $question->update([
            'section_id' => $sectionId ?? $question->section_id,
            'subsection_id' => $subsectionId ?? $question->subsection_id,
            'question_text' => $request->input('question_text', $question->question_text),
            'question_image' => $questionImagePath,
            'type' => $newType,
            'order_no' => $newOrderNo,
        ]);

        // Handle TRUE_FALSE type changes
        if ($newType === 'TRUE_FALSE') {
            // Check if question already has True/False choices
            $existingChoices = $question->choices()->whereIn('choice_text', ['True', 'False'])->get();
            
            if ($existingChoices->count() < 2) {
                // Delete all existing choices and create True/False pair
                $question->choices()->delete();
                
                $correctAnswer = $request->input('correct_answer', 'True'); // Default to 'True'
                
                ExamChoice::create([
                    'question_id' => $question->question_id,
                    'choice_text' => 'True',
                    'is_correct' => $correctAnswer === 'True',
                ]);
                ExamChoice::create([
                    'question_id' => $question->question_id,
                    'choice_text' => 'False',
                    'is_correct' => $correctAnswer === 'False',
                ]);
            } else {
                // Update existing True/False choices if correct_answer is provided
                if ($request->has('correct_answer')) {
                    $correctAnswer = $request->input('correct_answer');
                    $question->choices()->update(['is_correct' => false]);
                    $question->choices()->where('choice_text', $correctAnswer)->update(['is_correct' => true]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Question updated successfully.',
            'data' => $question->fresh(['choices']),
        ]);
    }

    /**
     * Delete a question.
     */
    public function destroy(Exam $exam, ExamQuestion $question)
    {
        // Validate question belongs to exam
        if ($question->exam_id !== $exam->exam_id) {
            return response()->json([
                'success' => false,
                'message' => 'Question does not belong to this exam.',
            ], 403);
        }

        $orderNo = $question->order_no;
        $subsectionId = $question->subsection_id;

        // Delete the question
        $question->delete();

        // Decrement order_no for questions after the deleted one
        ExamQuestion::where('subsection_id', $subsectionId)
            ->where('order_no', '>', $orderNo)
            ->decrement('order_no');

        return response()->json([
            'success' => true,
            'message' => 'Question deleted successfully.',
            'data' => null,
        ]);
    }

    /**
     * Reorder questions.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'questions' => 'required|array',
            'questions.*.question_id' => 'required|exists:exam_questions,question_id',
            'questions.*.order_no' => 'required|integer|min:1',
        ]);

        // Update order numbers
        foreach ($request->input('questions') as $item) {
            ExamQuestion::where('question_id', $item['question_id'])
                ->update(['order_no' => $item['order_no']]);
        }

        // Get first question to determine subsection_id for reload
        $firstQuestion = ExamQuestion::find($request->input('questions.0.question_id'));
        if (!$firstQuestion) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid question data.',
            ], 400);
        }

        // Reload questions in new order
        $updatedQuestions = ExamQuestion::where('subsection_id', $firstQuestion->subsection_id)
            ->orderBy('order_no')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Questions reordered successfully.',
            'data' => $updatedQuestions,
        ]);
    }
}

