<?php

namespace Database\Seeders;

use App\Classes\Enum\TypeAppointmentEnum;
use Illuminate\Database\Seeder;

class TypeAppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $names = [
            TypeAppointmentEnum::INITIAL,
            TypeAppointmentEnum::CONTROL,
            TypeAppointmentEnum::URGENCE
        ];

        TypeAppointmentEnum::insert(
            collect($names)->map(fn($name) => [
                'name' => $name,
                'description' => ''
            ])->toArray()
        );
    }
}
