<?php

namespace App\Exceptions;

use Exception;

class PersonExistException extends Exception
{
    private int $id;
    public function __construct(int $id, string $message = "", int $code = 409)
    {
        $this->id = $id;
        parent::__construct($message, code: $code);
    }

    public function getId(): string {
        return $this->id;
    }
}
