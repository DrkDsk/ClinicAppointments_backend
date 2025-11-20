<?php

namespace App\Http\Controllers;

use App\Factories\CreateAppointmentDTOFactory;
use App\Http\Requests\CreateAppointmentRequest;
use App\Http\Requests\PaginatorRequest;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\ErrorResource;
use App\Models\Appointment;
use App\Services\Contract\AppointmentServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Throwable;

class AppointmentController extends Controller
{
    public function __construct(protected readonly AppointmentServiceInterface $service)
    {
    }

    public function store(CreateAppointmentRequest $request): ErrorResource|AppointmentResource
    {
        try {

            $appointmentData = CreateAppointmentDTOFactory::fromRequest($request);

            $appointment = $this->service->create($appointmentData);

            $appointment->load(['doctor', 'patient', 'typeAppointment']);

            return new AppointmentResource($appointment);
        } catch (Throwable $exception) {
            return new ErrorResource(message: $exception->getMessage(), statusCode: 409);
        }
    }

    public function get(PaginatorRequest $request): AnonymousResourceCollection
    {
        $perPage = $request->input('perPage');

        $appointments = $this->service->getAllPaginated($perPage);

        return AppointmentResource::collection($appointments);
    }

    public function show(Appointment $appointment): AppointmentResource
    {
        $appointment->load(['doctor', 'patient', 'typeAppointment']);

        return new AppointmentResource($appointment);
    }
}
