<?php

namespace App\Http\Controllers;

use App\Factories\CreateDoctorDTOFactory;
use App\Http\Requests\CreateDoctorRequest;
use App\Http\Requests\DoctorAvailableScheduleRequest;
use App\Http\Requests\PaginatorRequest;
use App\Http\Resources\DoctorAvailableScheduleResource;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\ErrorResource;
use App\Models\Doctor;
use App\Services\Contract\AppointmentServiceInterface;
use App\Services\Contract\DoctorServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Throwable;

class DoctorController extends Controller
{

    public function __construct(
        private readonly DoctorServiceInterface      $doctorService,
        private readonly AppointmentServiceInterface $appointmentService)
    {
    }

    public function get(PaginatorRequest $request): AnonymousResourceCollection
    {
        $perPage = $request->input('perPage');
        
        $doctors = $this->doctorService->getAllPaginate($perPage);

        return DoctorResource::collection($doctors);
    }

    public function store(CreateDoctorRequest $request): JsonResource
    {
        try {
            $dto = CreateDoctorDTOFactory::fromRequest($request);

            $doctor = $this->doctorService->create($dto);

            $doctor->load('person');

            return (new DoctorResource($doctor));
        } catch (Throwable $e) {
            return new ErrorResource(message: $e->getMessage(), statusCode: 409);
        }
    }

    public function getAvailableTimes(Doctor $doctor, DoctorAvailableScheduleRequest $request): ErrorResource|DoctorAvailableScheduleResource
    {
        try {
            $date = $request->getDate();

            $availableSchedule = $this->appointmentService->getAvailableAppointmentsSchedule(
                $doctor,
                $date
            );

            return new DoctorAvailableScheduleResource(
                data: $availableSchedule,
                message: 'Horarios obtenidos correctamente'
            );
        } catch (Throwable $e) {
            return new ErrorResource(message: $e->getMessage(), statusCode: 200);
        }
    }
}
