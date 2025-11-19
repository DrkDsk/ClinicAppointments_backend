<?php

namespace App\Classes\DTOs\Person;

readonly class CreatePersonDTO
{
    public function __construct(
        public PersonDTO $personDTO,
        public ?string $password = null
    )
    {
    }
}
