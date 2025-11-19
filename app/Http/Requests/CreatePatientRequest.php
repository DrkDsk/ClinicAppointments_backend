<?php

namespace App\Http\Requests;

use App\Classes\Const\BodyMeasures;
use App\Classes\Enum\HeightMeasureEnum;
use App\Classes\Enum\WeightMeasureEnum;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreatePatientRequest extends FormRequest
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

            'patient' => ['required', 'array'],
            'patient.height' => ['required', 'numeric', 'min:0.01', 'max:999.99'],
            'patient.weight' => ['required', 'numeric', 'min:0.01', 'max:999.99'],
            'patient.height_measure_type' => ['required', Rule::in(BodyMeasures::heightMeasureTypes())],
            'patient.weight_measure_type' => ['required', Rule::in(BodyMeasures::weightMeasureTypes())],

            'user' => ['nullable', 'array'],
            'user.password' => ['required_with:user', 'string']
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'patient.height_measure_type' => $this->input('patient.height_measure_type') ?? HeightMeasureEnum::CENTIMETER->value,
            'patient.weight_measure_type' => $this->input('patient.weight_measure_type') ?? WeightMeasureEnum::KILOGRAM->value,
        ]);
    }
}
