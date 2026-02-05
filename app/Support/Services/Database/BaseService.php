<?php

namespace App\Support\Services\Database;

use App\Support\Enums\ResponseMessageEnum;
use App\Support\Services\Contracts\BaseService as BaseServiceContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

class BaseService implements BaseServiceContract
{
    public function __construct(protected string $model) {}

    protected function model()
    {
        return app($this->model);
    }

    public function paginate(array $with = [], array $filters = [], int $perPage = 15, array $columns = ['*']): LengthAwarePaginator
    {
        $query = $this->model()->with($with);

        if (! empty($filters) && method_exists($this->model, 'filter')) {
            $query->filter($filters);
        }

        return $query->latest()->paginate($perPage, $columns);
    }

    public function get(array $with = [], array $filters = [], array $columns = ['*']): Collection
    {
        $query = $this->model()->with($with);

        if (! empty($filters) && method_exists($this->model, 'filter')) {
            $query->filter($filters);
        }

        return $query->latest()->get($columns);
    }

    public function findBy(string $field, string $value): Model
    {
        $result = $this->model()->firstWhere($field, $value);

        if (! $result) {
            throw new \Exception(
                __(ResponseMessageEnum::NOT_FOUND->value),
                Response::HTTP_NOT_FOUND
            );
        }

        return $result;
    }

    public function create(array $data): Model
    {
        return $this->model()->create($data);
    }

    public function update(Model $model, array $data): Model
    {
        $model->update($data);

        return $model->refresh();
    }

    public function delete(Model $model): void
    {
        $model->delete();
    }
}
