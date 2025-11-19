<?php

namespace App\Classes\DTOs\Person;

use Carbon\Carbon;

readonly class PersonDTO
{
    public function __construct(
        public string $name,
        public string $lastName,
        public ?string $email = null,
        public ?Carbon $birthday = null,
        public ?string $phone = null,
        public ?int   $id = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            "name" => $this->name,
            "last_name" => $this->lastName,
            "email" => $this->email ?? null,
            "birthday" => $this->birthday?->format('Y-m-d') ?? null,
            "phone" => $this->phone ?? null,
        ];
    }
}
