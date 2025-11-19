<?php

namespace App\Classes\DTOs\Appointment;

use App\Classes\DTOs\Person\CreatePersonDTO;
use Carbon\Carbon;

readonly class CreateAppointmentDTO
{
    public function __construct(
        public ?string          $patientId,
        public ?CreatePersonDTO $createPersonDTO,
        public string           $doctorId,
        public Carbon           $scheduledAt,
        public ?string          $typeAppointmentId,
        public ?string          $note,
    )
    {
    }
}
