<?php

namespace App\Infrastructure\Services;

use App\Exceptions\UserExistsException;
use App\Models\Person;
use App\Models\User;
use App\Services\Contract\UserServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

readonly class EnrollService
{
    public function __construct(private UserServiceInterface $userService)
    {
    }

    /**
     * @throws Throwable
     */
    public function enroll(Person $person, string $password) : User
    {
        return DB::transaction(function () use ($person, $password) {
            throw_if($person->user()->exists(), new UserExistsException("Este usuario ya existe.", code: 409));

            /** @var User $user */
            $user = $person->user()->create([
                'password' => Hash::make($password),
            ]);

            $this->userService->assignRoleTo($user, $person);

            return $user;
        });
    }
}
