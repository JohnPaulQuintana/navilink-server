<?php

namespace App\Http\Requests\Feedback;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FeedbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category' => ['required', 'string', 'max:50'],

            'rating' => ['required', 'integer', 'min:1', 'max:5'],

            'message' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
