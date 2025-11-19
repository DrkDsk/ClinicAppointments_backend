<?php

namespace App\Services\Contract;

use App\Classes\DTOs\Person\PersonDTO;
use App\Models\Person;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PersonServiceInterface
{
    public function getAllPaginate(int $perPage): LengthAwarePaginator;

    public function create(PersonDTO $personDTO, array $filter = []): Person;

    public function search(string $query): Collection;
}
