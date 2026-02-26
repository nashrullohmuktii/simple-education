<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends ApiRequest
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
        $userId = $this->route('user')->id ?? null;

        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => "sometimes|required|email|unique:users,email,{$userId}",
            'password' => ['sometimes', 'required', Password::min(8)],
            'role' => 'sometimes|string|in:admin,user',
        ];
    }
}
