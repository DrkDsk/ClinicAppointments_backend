<?php

namespace App\Http\Controllers;

use App\Factories\CreatePatientDTOFactory;
use App\Http\Requests\CreatePatientRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\PatientResource;
use App\Services\Contract\PatientServiceInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Throwable;

class PatientController extends Controller
{
    public function __construct(protected readonly PatientServiceInterface $patientService)
    {
    }

    public function get(): AnonymousResourceCollection
    {
        try {
            $perPage = request()->input('perPage', 10);
            $patients = $this->patientService->getAllPaginate($perPage);

            return PatientResource::collection($patients);
        } catch (Throwable $exception) {
            return ErrorResource::collection($exception->getMessage());
        }
    }

    public function store(CreatePatientRequest $request): JsonResource
    {
        try {
            $dto = CreatePatientDTOFactory::fromRequest($request);

            $patient = $this->patientService->create($dto);

            $patient->load('person');

            return new PatientResource($patient);
        } catch (Throwable $e) {
            return new ErrorResource(message: $e->getMessage(), statusCode: 409);
        }
    }
}
