<?php

namespace App\Infrastructure\Services;

use App\Classes\Const\AppointmentsStatus;
use App\Classes\DTOs\Appointment\CreateAppointmentDTO;
use App\Exceptions\AppointmentExistsException;
use App\Exceptions\PersonExistException;
use App\Exceptions\ScheduleNotAvailableException;
use App\Factories\CreatePatientDTOFactory;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Repositories\Contract\AppointmentRepositoryInterface;
use App\Repositories\Contract\PatientRepositoryInterface;
use App\Services\Contract\AppointmentServiceInterface;
use App\Services\Contract\PatientServiceInterface;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class AppointmentService implements AppointmentServiceInterface
{
    public function __construct(
        private AppointmentRepositoryInterface $appointmentRepository,
        private PatientRepositoryInterface     $patientRepository,
        private PatientServiceInterface        $patientService)
    {
    }

    /**
     * @throws Throwable
     */
    public function create(CreateAppointmentDTO $appointmentData): Appointment
    {

        $scheduledAt = $appointmentData->scheduledAt->format('Y-m-d H:i');
        $doctorId = $appointmentData->doctorId;
        $patientId = $appointmentData->patientId;
        $personData = $appointmentData->createPersonDTO->personDTO;
        $typeAppointmentId = $appointmentData->typeAppointmentId;
        $note = $appointmentData->note;

        return DB::transaction(function () use (
            $scheduledAt,
            $doctorId,
            $patientId,
            $personData,
            $typeAppointmentId,
            $note
        ) {

            $appointment = $this->appointmentRepository->findByScheduled(
                doctorId: $doctorId,
                scheduledAt: $scheduledAt
            );

            if ($appointment) {
                $doctorName = $appointment->doctorProfile()->first()->getAttribute('name');
                $messageException = "El doctor: $doctorName ya tiene una cita programada para $scheduledAt";

                throw new AppointmentExistsException($messageException);
            }

            $patientData = CreatePatientDTOFactory::fromData(personData: $personData);
            $typeAppointmentId = $typeAppointmentId ?? $this->appointmentRepository->findTypeAppointment()->id;

            if (!$patientId) {
                try {
                    $patientId = $this->patientService->create($patientData)->id;
                } catch (PersonExistException $e) {
                    $personId = $e->getId();
                    $patient = $this->patientRepository->findByField('person_id', $personId);
                    $patientId = $patient->id;
                }
            }

            return $this->appointmentRepository->firstOrCreate([
                'scheduled_at' => $scheduledAt,
                'patient_id' => $patientId,
                'doctor_id' => $doctorId,
                'type_appointment_id' => $typeAppointmentId,
                'note' => $note,
                'status' => AppointmentsStatus::SCHEDULED
            ]);
        });
    }

    public function getAllPaginated(int $perPage): LengthAwarePaginator
    {
        return $this->appointmentRepository->paginate($perPage, ['doctor', 'patient', 'typeAppointment']);
    }

    /**
     * @throws Throwable
     */
    public function getAvailableAppointmentsSchedule(Doctor $doctor, Carbon $date): array
    {
        $indexDay = $date->dayOfWeek;
        $appointments = $doctor->appointments()->get()->pluck('scheduled_at');
        $availableDates = $doctor->schedule()->where('weekday', $indexDay)->first();

        if (!$availableDates) {
            throw new ScheduleNotAvailableException("No hay espacio disponible para la fecha seleccionada");
        }

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
