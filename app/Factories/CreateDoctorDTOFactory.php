<?php

namespace App\Factories;

use App\Classes\DTOs\Doctor\CreateDoctorDTO;
use App\Classes\DTOs\Person\PersonDTO;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CreateDoctorDTOFactory
{
    public static function fromRequest(Request $request): CreateDoctorDTO
    {
        $person = $request->input('person', []);
        $user = $request->input('user');
        $doctor = $request->input('doctor', []);

        $personDto = new PersonDTO(
            name: $person['name'],
            lastName: $person['last_name'],
            email: $person['email'],
            birthday: Carbon::parse($person['birthday']),
            phone: $person['phone']
        );

        $password = $user ? $user['password'] : null;

        return new CreateDoctorDTO(
            person: $personDto,
            specialty: $doctor['specialty'],
            password: $password
        );
    }
}
