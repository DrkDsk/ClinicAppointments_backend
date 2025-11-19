<?php

namespace App\Factories;

use App\Classes\DTOs\Patient\CreatePatientDTO;
use App\Classes\DTOs\Person\PersonDTO;
use App\Classes\Enum\HeightMeasureEnum;
use App\Classes\Enum\WeightMeasureEnum;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CreatePatientDTOFactory
{
    public static function fromRequest(Request $request): CreatePatientDTO
    {
        $weightType = WeightMeasureEnum::tryFrom($request->patient["weight_measure_type"]) ?? WeightMeasureEnum::KILOGRAM;
        $heightType = HeightMeasureEnum::tryFrom($request->patient["height_measure_type"]) ?? HeightMeasureEnum::CENTIMETER;

        $personDto = new PersonDTO(
            name: $request->person["name"],
            lastName: $request->person["last_name"],
            email: $request->person["email"],
            birthday: Carbon::parse($request->person["birthday"]),
            phone: $request->person["phone"]
        );

        $user = $request->user;

        return new CreatePatientDTO(
            person: $personDto,
            weight: $request->patient["weight"],
            height: $request->patient["height"],
            weightMeasureEnum: $weightType,
            heightMeasureEnum: $heightType,
            password: $user ? $request->user["password"] : null
        );
    }

    public static function fromData(PersonDTO $personData): CreatePatientDTO
    {
        return new CreatePatientDTO(
            person: $personData
        );
    }
}
