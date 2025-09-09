<?php

namespace App\Support\Traits;

use Illuminate\Pagination\LengthAwarePaginator;

trait WithPagination
{
    public function returnPaginated(LengthAwarePaginator $paginatedData)
    {
        return [
            'pagination' => [
                'current_page' => $paginatedData->currentPage(),
                'last_page' => $paginatedData->lastPage(),
                'per_page' => $paginatedData->perPage(),
                'total' => $paginatedData->total(),
                'from' => $paginatedData->firstItem(),
                'to' => $paginatedData->lastItem(),
            ],
        ];
    }
}
