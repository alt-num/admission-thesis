<?php

namespace App\Http\Requests\Admission\ExamEditor;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubsectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'order_no' => 'nullable|integer|min:1',
        ];
    }
}

