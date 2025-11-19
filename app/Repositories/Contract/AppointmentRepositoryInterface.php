<?php

namespace App\Repositories\Contract;

use App\Classes\Enum\TypeAppointmentEnum;
use App\Models\Appointment;
use App\Models\TypeAppointment;

interface AppointmentRepositoryInterface extends BaseRepositoryInterface
{
    public function findByScheduled(int $doctorId, string $scheduledAt) : ?Appointment;

    public function findTypeAppointment(string $name = TypeAppointmentEnum::INITIAL->value): TypeAppointment;
}
