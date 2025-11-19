<?php

namespace App\Infrastructure\Services;

use App\Classes\Const\Role;
use App\Classes\DTOs\Person\PersonDTO;
use App\Models\Receptionist;
use App\Repositories\Contract\ReceptionistRepositoryInterface;
use App\Services\Contract\PersonServiceInterface;
use App\Services\Contract\ReceptionistServiceInterface;
use App\Services\Contract\UserServiceInterface;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class ReceptionistService implements ReceptionistServiceInterface
{
    public function __construct(
        protected PersonServiceInterface          $personService,
        protected UserServiceInterface            $userService,
        protected ReceptionistRepositoryInterface $repository)
    {
    }

    /**
     * @throws Throwable
     */
    public function create(PersonDTO $dto, string $password): Receptionist
    {
        return DB::transaction(function () use ($dto, $password) {

            $filter = [
                'email' => $dto->email,
                'phone' => $dto->phone,
            ];

            $person = $this->personService->create($dto, $filter);
            $personId = $person->id;

            $this->userService->create($password, $personId, Role::RECEPTIONIST);

            return $this->repository->firstOrCreate([
                'person_id' => $personId
            ]);
        });
    }
}
