<?php

namespace App\Http\Requests\Admission\ExamEditor;

use Illuminate\Foundation\Http\FormRequest;

class StoreChoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'choice_text' => 'nullable|string|max:255',
            'choice_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'remove_choice_image' => 'nullable|boolean',
            'is_correct' => 'required|boolean',
        ];
    }
}

