<?php

namespace App\Infrastructure\Services;

use App\Classes\DTOs\Person\PersonDTO;
use App\Exceptions\PersonExistException;
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
    public function create(PersonDTO $personDTO, array $filter = []): Person
    {
        $person = $this->repository->findWithFields($filter)->first();

        if ($person) {
            throw new PersonExistException(
                id: $person->id,
                message: "Este usuario ya se encuentra registrado.",
            );
        }

        return DB::transaction(function () use ($personDTO) {
            return $this->repository->firstOrCreate($personDTO->toArray());
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
