<?php

namespace App\Repositories\Eloquent;

use App\Models\Patient;
use App\Repositories\Contract\PatientRepositoryInterface;

class EloquentPatientRepository extends BaseRepository implements PatientRepositoryInterface
{

    public function __construct(Patient $model)
    {
        parent::__construct($model);
    }

    public function create(array $data): Patient {
        $patient = $this->model->where('person_id', $data['person_id'])->first();

        if ($patient) {
            return $patient;
        }

        return $this->model->create($data);
    }
}
