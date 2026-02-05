<?php

namespace App\Support\Services\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface BaseService
{
    public function paginate(array $with = [], array $filters = [], int $perPage = 15, array $columns = ['*']): LengthAwarePaginator;

    public function get(array $with = [], array $filters = [], array $columns = ['*']): Collection;

    public function findBy(string $field, string $value): Model;

    public function create(array $data): Model;

    public function update(Model $model, array $data): Model;

    public function delete(Model $model): void;
}
