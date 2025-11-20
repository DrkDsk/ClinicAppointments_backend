<?php

namespace App\Repositories\Contract;

use App\Models\Patient;

interface PatientRepositoryInterface extends BaseRepositoryInterface
{
    public function findByField(string $field, string $value): ?Patient;
}
