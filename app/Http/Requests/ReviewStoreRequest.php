<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReviewStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'score' => ['required', 'integer', 'between:1,5'],
            'feedback' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
