<?php

namespace App\Http\Controllers;

use App\Exceptions\UserExistsException;
use App\Http\Requests\EnrollUserRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\UserResource;
use App\Infrastructure\Services\EnrollService;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Throwable;

class UserController extends Controller
{
    public function __construct(private readonly EnrollService $service)
    {
    }

    public function get(Request $request): JsonResource
    {
        $user = $request->user();

        if (!$user) {
            return new ErrorResource("El usuario no está autenticado", statusCode: 200);
        }

        return new UserResource($user);
    }
    public function enroll(Person $person, EnrollUserRequest $request) : JsonResource
    {
        try {
            $user = $this->service->enroll($person, $request->validated('user.password'));

            return new UserResource($user);
        } catch (UserExistsException $e) {
            return new ErrorResource($e->getMessage(), statusCode: 409);
        }
        catch (Throwable) {
            return new ErrorResource(statusCode: 500);
        }
    }
}
