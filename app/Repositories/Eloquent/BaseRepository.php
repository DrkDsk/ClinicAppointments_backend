<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contract\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find(int $id): ?Model
    {
        return $this->model->where('id', $id)->first();
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        $instance = $this->find($id);
        return $instance && $instance->update($data);
    }

    public function delete(int $id): bool
    {
        $instance = $this->find($id);
        return $instance ? $instance->delete() : false;
    }

    public function paginate(int $perPage, array $relations = []): LengthAwarePaginator
    {
        return $this->model->with($relations)->paginate($perPage);
    }

    public function firstOrCreate(array $payload, array $search = []): Model
    {
        if (!count($search)) {
            $search = $payload;
        }

        return $this->model->firstOrCreate($search, $payload);
    }
}
