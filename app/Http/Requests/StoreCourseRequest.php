<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StoreCourseRequest extends ApiRequest
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
            'topic_id' => 'required|exists:topics,id',
            'language_id' => 'required|exists:languages,id',
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'discount_rate' => 'required|numeric|min:0|max:100',
            'thumbnail_url' => 'required|string|max:255|url',
            'level' => ['required', Rule::in(['all', 'beginner', 'intermediate', 'advance'])],
        ];
    }
}
