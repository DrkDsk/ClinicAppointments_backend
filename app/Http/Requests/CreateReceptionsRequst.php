<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateReceptionsRequst extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'person' => ['required', 'array'],
            'person.name' => ['required', 'string'],
            'person.last_name' => ['required', 'string'],
            'person.email' => ['required', 'email'],
            'person.birthday' => ['required', 'date', 'before:today'],
            'person.phone' => ['required', 'digits:10'],

            'user' => ['required', 'array'],
            'user.password' => ['required_with:user', 'string']
        ];
    }
}
