<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\GetRoleRequest;
use App\Http\Resources\UserResource;
use App\Repositories\Contract\UserRepositoryInterface;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RoleController extends Controller
{

    public function __construct(private readonly UserRepositoryInterface $userRepository)
    {
    }

    public function set(CreateRoleRequest $request): UserResource
    {
        $user = $request->user();
        $user->syncRoles($request->get('roles'));

        return new UserResource($user);
    }

    public function only(GetRoleRequest $request): AnonymousResourceCollection
    {
        $role = $request->input('role');
        $perPage = $request->input('perPage', 10);
        
        $users = $this->userRepository->getByRolesPaginated($role, $perPage);

        return UserResource::collection($users);
    }
}
