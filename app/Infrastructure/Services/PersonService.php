<?php

namespace App\Infrastructure\Services;

use App\Classes\DTOs\Person\PersonDTO;
use App\Models\Person;
use App\Repositories\Contract\PersonRepositoryInterface;
use App\Services\Contract\PersonServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Throwable;

readonly class PersonService implements PersonServiceInterface
{
    public function __construct(private PersonRepositoryInterface $repository)
    {
    }

    /**
     * @throws Throwable
     */
    public function create(PersonDTO $personDTO): Person
    {

        $email = $personDTO->email;
        $phone = $personDTO->phone;
        $name = $personDTO->name;
        $lastName = $personDTO->lastName;

        $person = null;

        if ($email) {
            $person = $this->repository->existsByField(value: $email, field: "email");
        } else if ($phone) {
            $person = $this->repository->existsByField(value: $phone);
        } else if ($name) {
            $person = $this->repository->existByNames(name: $name, lastName: $lastName);
        }

        if ($person) {
            return $person;
        }

        return DB::transaction(function () use ($personDTO) {
            return $this->repository->create($personDTO->toArray());
        });
    }

    public function getAllPaginate(int $perPage): LengthAwarePaginator
    {
       return $this->repository->paginate($perPage);
    }

    public function search(string $query): Collection
    {
        return $this->repository->search($query);
    }
}
