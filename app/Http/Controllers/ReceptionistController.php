<?php

namespace App\Http\Controllers;

use App\Factories\CreatePersonDTOFactory;
use App\Http\Requests\CreateReceptionsRequst;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ReceptionistResource;
use App\Services\Contract\ReceptionistServiceInterface;
use Illuminate\Http\Resources\Json\JsonResource;
use Throwable;

class ReceptionistController extends Controller
{
    public function __construct(protected readonly ReceptionistServiceInterface $service)
    {
    }

    /**
     * @throws Throwable
     */
    public function store(CreateReceptionsRequst $request): JsonResource
    {
        try {
            $personData = CreatePersonDTOFactory::fromRequest($request);

            $receptionist = $this->service->create($personData->personDTO, $personData->password);

            $receptionist->load('person');

            return new ReceptionistResource($receptionist);
        } catch (Throwable $e) {
            return new ErrorResource(message: $e->getMessage(), statusCode: 409);
        }
    }
}
