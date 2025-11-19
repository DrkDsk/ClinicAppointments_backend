<?php

namespace App\Classes\DTOs\Patient;

use App\Classes\DTOs\Person\PersonDTO;
use App\Classes\Enum\HeightMeasureEnum;
use App\Classes\Enum\WeightMeasureEnum;

readonly class CreatePatientDTO
{
    public function __construct(
        public PersonDTO         $person,
        public ?float             $weight  = null,
        public ?float             $height = null,
        public ?WeightMeasureEnum $weightMeasureEnum = null,
        public ?HeightMeasureEnum $heightMeasureEnum = null,
        public ?string     $password = null,
        public ?int              $id = null,
    ) {
    }
}
