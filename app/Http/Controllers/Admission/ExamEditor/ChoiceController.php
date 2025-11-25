<?php

namespace App\Http\Controllers\Admission\ExamEditor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admission\ExamEditor\StoreChoiceRequest;
use App\Http\Requests\Admission\ExamEditor\UpdateChoiceRequest;
use App\Models\Exam;
use App\Models\ExamChoice;
use App\Models\ExamQuestion;

class ChoiceController extends Controller
{
    /**
     * Store a new choice.
     */
    public function store(StoreChoiceRequest $request, Exam $exam, ExamQuestion $question)
    {
        // Validate question belongs to exam
        if ($question->exam_id !== $exam->exam_id) {
            return response()->json([
                'success' => false,
                'message' => 'Question does not belong to this exam.',
            ], 403);
        }

        // Handle choice image upload
        $choiceImagePath = null;
        if ($request->hasFile('choice_image')) {
            $choiceImagePath = $request->file('choice_image')->store('exam_choices', 'public');
        }

        $choice = ExamChoice::create([
            'question_id' => $question->question_id,
            'choice_text' => $request->input('choice_text'),
            'choice_image' => $choiceImagePath,
            'is_correct' => $request->boolean('is_correct'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Choice created successfully.',
            'data' => $choice->fresh(),
        ]);
    }

    /**
     * Update a choice.
     */
    public function update(Exam $exam, ExamChoice $choice, UpdateChoiceRequest $request)
    {
        // Validate choice belongs to exam
        if ($choice->question->exam_id !== $exam->exam_id) {
            return response()->json([
                'success' => false,
                'message' => 'Choice does not belong to this exam.',
            ], 403);
        }

        // Handle choice image upload and removal
        $choiceImagePath = $choice->choice_image;
        
        // Remove existing image if requested
        if ($request->boolean('remove_choice_image')) {
            if ($choiceImagePath) {
                \Storage::disk('public')->delete($choiceImagePath);
            }
            $choiceImagePath = null;
        }
        
        // Upload new image if provided
        if ($request->hasFile('choice_image')) {
            // Delete old image if exists
            if ($choiceImagePath) {
                \Storage::disk('public')->delete($choiceImagePath);
            }
            $choiceImagePath = $request->file('choice_image')->store('exam_choices', 'public');
        }

        $choice->update([
            'choice_text' => $request->input('choice_text', $choice->choice_text),
            'choice_image' => $choiceImagePath,
            'is_correct' => $request->boolean('is_correct'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Choice updated successfully.',
            'data' => $choice->fresh(),
        ]);
    }

    /**
     * Delete a choice.
     */
    public function destroy(Exam $exam, ExamChoice $choice)
    {
        // Validate choice belongs to exam
        if ($choice->question->exam_id !== $exam->exam_id) {
            return response()->json([
                'success' => false,
                'message' => 'Choice does not belong to this exam.',
            ], 403);
        }

        $choice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Choice deleted successfully.',
            'data' => null,
        ]);
    }

    /**
     * Set correct answer for TRUE_FALSE questions.
     */
    public function toggleTrueFalse(Exam $exam, ExamQuestion $question, \Illuminate\Http\Request $request)
    {
        // Validate question belongs to exam
        if ($question->exam_id !== $exam->exam_id) {
            return response()->json([
                'success' => false,
                'message' => 'Question does not belong to this exam.',
            ], 403);
        }

        // Validate question is TRUE_FALSE type
        if ($question->type !== 'TRUE_FALSE') {
            return response()->json([
                'success' => false,
                'message' => 'This operation is only valid for TRUE/FALSE questions.',
            ], 400);
        }

        // Get the desired correct answer from request
        $correctAnswer = $request->input('correct_answer');
        
        // Validate the answer is either 'True' or 'False'
        if (!in_array($correctAnswer, ['True', 'False'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid answer. Must be either "True" or "False".',
            ], 400);
        }

        // Set all choices to false first
        $question->choices()->update(['is_correct' => false]);

        // Set the specified answer as correct
        $question->choices()->where('choice_text', $correctAnswer)->update(['is_correct' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Correct answer set successfully.',
            'data' => $question->fresh(['choices']),
        ]);
    }
}

