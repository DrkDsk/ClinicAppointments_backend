<?php

namespace App\Infrastructure\Services;

use App\Classes\Const\AppointmentsStatus;
use App\Classes\DTOs\Appointment\CreateAppointmentDTO;
use App\Exceptions\AppointmentExistsException;
use App\Exceptions\ScheduleNotAvailableException;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Repositories\Contract\AppointmentRepositoryInterface;
use App\Services\Contract\AppointmentServiceInterface;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class AppointmentService implements AppointmentServiceInterface
{
    public function __construct(private AppointmentRepositoryInterface $appointmentRepository)
    {
    }

    /**
     * @throws Throwable
     */
    public function create(CreateAppointmentDTO $appointmentData): Appointment
    {
        return DB::transaction(function () use ($appointmentData) {
            $scheduledAt = $appointmentData->scheduledAt->format('Y-m-d H:i');

            $appointment = $this->appointmentRepository->findByScheduled(
                doctorId: $appointmentData->doctorId,
                scheduledAt: $scheduledAt
            );

            if ($appointment) {
                $doctorName = $appointment->doctorProfile()->first()->getAttribute('name');
                $messageException = "El doctor: $doctorName ya tiene una cita programada para $scheduledAt";

                throw new AppointmentExistsException($messageException);
            }

            return $this->appointmentRepository->create([
                'scheduled_at' => $scheduledAt,
                'patient_id' => $appointmentData->patientId,
                'doctor_id' => $appointmentData->doctorId,
                'type_appointment_id' => $appointmentData->typeAppointmentId,
                'note' => $appointmentData->note,
                'status' => AppointmentsStatus::SCHEDULED
            ]);
        });
    }

    public function getAllPaginated(int $perPage) : LengthAwarePaginator
    {
        return $this->appointmentRepository->paginate($perPage, ['doctor', 'patient', 'typeAppointment']);
    }

    /**
     * @throws Throwable
     */
    public function getAvailableAppointmentsSchedule(Doctor $doctor, string $strDate): array
    {
        $date = Carbon::parse($strDate);
        $indexDay = $date->dayOfWeek;
        $appointments = $doctor->appointments()->get()->pluck('scheduled_at');
        $availableDates = $doctor->schedule()->where('weekday', $indexDay)->first();

        throw_unless($availableDates, new ScheduleNotAvailableException("No hay espacio disponible para la fecha seleccionada"));

        $startTime = explode(":", $availableDates->start_time);
        $endTime = explode(":", $availableDates->end_time);

        $startDate = $date->copy()->setTime($startTime[0], $startTime[1]);
        $endDate = $date->copy()->setTime($endTime[0], $endTime[1]);


        $hours = [];
        $current = $startDate->copy();

        while ($current->lte($endDate)) {

            $isNearAppointment = $appointments->contains(function ($appointment) use ($current) {
                $appointmentTime = Carbon::parse($appointment);
                return abs($appointmentTime->diffInMinutes($current)) < 30;
            });

            if (!$isNearAppointment) {
                $hours[] = $current->format('H:i');
            }

            $current->addMinutes(30);
        }

        return $hours;
    }
}
