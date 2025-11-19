<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateAppointmentRequest extends FormRequest
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
            'patient_id' => ['nullable', 'exists:patients,id'],
            'person' => ['required_without:patient_id', 'array'],

            'person.name' => ['required', 'string'],
            'person.last_name' => ['required', 'string'],

            'doctor_id' => 'required|exists:doctors,id',
            'scheduled_at' => 'required|date',
            'note' => ['nullable', 'string'],
        ];
    }
}
