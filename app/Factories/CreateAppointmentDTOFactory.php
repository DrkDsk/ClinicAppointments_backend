<?php

namespace App\Factories;

use App\Classes\DTOs\Appointment\CreateAppointmentDTO;
use App\Classes\DTOs\Person\CreatePersonDTO;
use App\Http\Requests\CreateAppointmentRequest;
use Carbon\Carbon;

class CreateAppointmentDTOFactory
{
    static public function fromRequest(CreateAppointmentRequest $request): CreateAppointmentDTO {
        $patientId = $request->input('patient_id');
        $personRequest = $request->input('person', []);

        $personData = CreatePersonDTOFactory::fromData($personRequest);

        $doctorId = $request->input('doctor_id');
        $scheduledAt =  Carbon::parse($request->input('scheduled_at'));
        $typeAppointmentId = $request->input('type_appointment_id');
        $note = $request->input('note');

        return new CreateAppointmentDTO(
            patientId:  $patientId,
            createPersonDTO: $personData,
            doctorId: $doctorId,
            scheduledAt: $scheduledAt,
            typeAppointmentId: $typeAppointmentId,
            note: $note
        );
    }
}
