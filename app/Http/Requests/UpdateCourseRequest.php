<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class UpdateCourseRequest extends ApiRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'topic_id' => 'sometimes|required|exists:topics,id',
            'language_id' => 'sometimes|required|exists:languages,id',
            'user_id' => 'sometimes|required|exists:users,id',
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'short_description' => 'sometimes|required|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'discount_rate' => 'sometimes|required|numeric|min:0|max:100',
            'thumbnail_url' => 'sometimes|required|string|max:255|url',
            'level' => ['sometimes', 'required', Rule::in(['all', 'beginner', 'intermediate', 'advance'])],
        ];
    }
}
