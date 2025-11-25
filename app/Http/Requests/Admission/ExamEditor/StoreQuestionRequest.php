<?php

namespace App\Http\Requests\Admission\ExamEditor;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question_text' => 'nullable|string',
            'question_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'remove_question_image' => 'nullable|boolean',
            'type' => 'required|in:MCQ,TRUE_FALSE',
            'correct_answer' => 'nullable|in:True,False', // For TRUE_FALSE questions
            'section_id' => 'nullable|exists:exam_sections,section_id',
            'subsection_id' => 'nullable|exists:exam_subsections,subsection_id',
            'order_no' => 'nullable|integer|min:1',
        ];
    }
}

