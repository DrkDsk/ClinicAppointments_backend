<?php

namespace App\Repositories\Contract;

use App\Models\Person;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;

interface PersonRepositoryInterface extends BaseRepositoryInterface
{
    public function existsByField(string $value, string $field = "phone"): ?Person;

    public function findWithFields(array $search): Builder;

    public function search(string $query): Collection;
}
