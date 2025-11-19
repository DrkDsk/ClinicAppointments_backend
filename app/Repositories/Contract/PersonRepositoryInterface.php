<?php

namespace App\Repositories\Contract;

use App\Models\Person;
use Illuminate\Database\Eloquent\Collection;

interface PersonRepositoryInterface extends BaseRepositoryInterface
{
    public function existsByField(string $value, string $field = "phone"): ?Person;
    public function existByNames(string $name, string $lastName): ?Person;
    public function search(string $query): Collection;
}
