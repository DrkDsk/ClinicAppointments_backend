<?php

namespace App\Infrastructure\Services;

use App\Classes\Const\Role;
use App\Classes\DTOs\Patient\CreatePatientDTO;
use App\Models\Patient;
use App\Repositories\Contract\PatientRepositoryInterface;
use App\Services\Contract\PatientServiceInterface;
use App\Services\Contract\PersonServiceInterface;
use App\Services\Contract\UserServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class PatientService implements PatientServiceInterface
{
    public function __construct(
        private PatientRepositoryInterface $patientRepository,
        private PersonServiceInterface     $personService,
        private UserServiceInterface       $userService
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function create(CreatePatientDTO $dto): Patient
    {
        $personData = $dto->person;
        $patientData = $dto;

        return DB::transaction(function () use ($personData, $patientData) {

            $filter = [
                "email" => $personData->email,
                'phone' => $personData->phone,
            ];

            $person = $this->personService->create($personData, $filter);

            $personId = $person->id;

            $password = $patientData->password;

            if ($password) {
                $this->userService->create(
                    password: $password,
                    personId: $personId,
                    role: Role::PATIENT
                );
            }

            $payload = [
                'person_id' => $personId,
                'weight' => $patientData->weight,
                'height' => $patientData->height,
                'weight_measure_type' => $patientData->weightMeasureEnum,
                'height_measure_type' => $patientData->heightMeasureEnum
            ];
            
            $search = [
                'person_id' => $personId,
            ];

            return $this->patientRepository->firstOrCreate($payload, $search);
        });
    }

    public function getAllPaginate(int $perPage): LengthAwarePaginator
    {
        return $this->patientRepository->paginate($perPage, ['person']);
    }
}
