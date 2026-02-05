<?php

namespace App\Support\Traits;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

trait HasFilters
{
    // TODO: add your searchable fields here.
    protected array $searchable = ['name'];

    #[Scope()]
    protected function filter(Builder $query, array $filters)
    {
        if (empty($filters)) {
            return $query;
        }

        foreach ($filters as $field => $value) {
            if (is_null($value)) {
                continue;
            }

            if ($field === 'q') {
                $query->where(function (Builder $query) use ($value) {
                    foreach ($this->searchable as $searchableField) {
                        $query->orWhere($searchableField, 'LIKE', "%{$value}%");
                    }
                });
            }
        }
    }
}
