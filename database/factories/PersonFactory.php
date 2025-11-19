<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Classes\Const\Role as RoleClass;

/**
 * @extends Factory<Person>
 */
class PersonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'birthday' => $this->faker->date('Y-m-d', '2005-01-01'),
            'phone' => $this->faker->phoneNumber()
        ];
    }

    public function configure(): Factory|PersonFactory
    {
        return $this->afterCreating(function (Person $person) {

            $role = fake()->randomElement(RoleClass::all());

            $createUser = fake()->boolean();

            if (!$createUser) {
                return;
            }

            $user = User::factory()->make([
                'person_id' => $person->id
            ]);

            $user->assignRole($role);
            $user->save();

            if ($role === RoleClass::PATIENT) {
                Patient::factory()->create([
                    'person_id' => $person->id,
                ]);
            }

            if ($role === RoleClass::DOCTOR) {
                Doctor::factory()->create([
                    'person_id' => $person->id,
                ]);
            }
        });
    }
}
