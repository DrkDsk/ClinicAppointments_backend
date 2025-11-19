<?php

namespace App\Infrastructure\Services;

use App\Classes\Const\Role;
use App\Classes\DTOs\Doctor\CreateDoctorDTO;
use App\Models\Doctor;
use App\Repositories\Contract\DoctorRepositoryInterface;
use App\Services\Contract\DoctorServiceInterface;
use App\Services\Contract\PersonServiceInterface;
use App\Services\Contract\UserServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class DoctorService implements DoctorServiceInterface
{
    public function __construct(
        private DoctorRepositoryInterface $repository,
        private PersonServiceInterface    $personService,
        private UserServiceInterface      $userService
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function create(CreateDoctorDTO $dto): Doctor
    {
        $personData = $dto->person;
        $password = $dto->password;
        $specialty = $dto->specialty;

        return DB::transaction(function () use ($personData, $password, $specialty) {

            $email = $personData->email;
            $phone = $personData->phone;

            $filter = [
                'email' => $email,
                'phone' => $phone,
            ];

            $person = $this->personService->create($personData, $filter);

            $personId = $person->getAttribute('id');

            if ($password) {
                $this->userService->create(
                    password: $password,
                    personId: $personId,
                    role: Role::DOCTOR
                );
            }

            return $this->repository->firstOrCreate([
                'person_id' => $personId,
                'specialty' => $specialty
            ]);
        });
    }

    public function getAllPaginate(int $perPage): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, ['person']);
    }
}

