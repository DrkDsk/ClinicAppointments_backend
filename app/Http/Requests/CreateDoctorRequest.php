<?php

namespace App\Http\Requests;

use App\Classes\Const\Specialties;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateDoctorRequest extends FormRequest
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
            'person.email' => ['required', 'email'],
            'person.birthday' => ['required', 'date', 'before:today'],
            'person.phone' => ['required', 'digits:10'],
            'person.last_name' => ['required', 'string'],

            'doctor' => ['required', 'array'],
            'doctor.specialty' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $normalized = strtolower($value);

                    $match = collect(Specialties::all())->first(function ($item) use ($normalized) {
                        return strtolower($item) === $normalized;
                    });

                    if (!$match) {
                        $fail("La especialidad '$value' no es válida.");
                    } else {
                        $this->merge([$attribute => $match]);
                    }
                }
            ],
            'user' => ['nullable', 'array'],
            'user.password' => ['required_with:user', 'string']
        ];
    }
}
