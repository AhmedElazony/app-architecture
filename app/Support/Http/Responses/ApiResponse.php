<?php

namespace App\Support\Http\Responses;

use App\Support\Enums\ResponseMessageEnum;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ApiResponse
{
    public static function success(?string $message = null, mixed $data = null, int $status = 200, array $extra = []): JsonResponse
    {
        return response()->json(array_merge([
            'message' => $message ?? __(ResponseMessageEnum::SUCCESS->value),
            'status' => $status,
            'data' => $data,
        ], $extra), $status);
    }

    public static function error(?string $message = null, int $status = 500, array $errors = [], array $extra = []): JsonResponse
    {
        return response()->json(array_merge([
            'message' => $message ?? __(ResponseMessageEnum::FAILED->value),
            'status' => $status,
            'errors' => $errors ?: null,
        ], $extra), $status);
    }

    public static function paginated(LengthAwarePaginator $data, string $resource, ?string $message = null, int $status = 200): JsonResponse
    {
        return self::success(
            $message ?? __(ResponseMessageEnum::FETCHED_SUCCESSFULLY->value),
            $resource::collection($data->items()),
            $status,
            [
                'pagination' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'from' => $data->firstItem(),
                    'to' => $data->lastItem(),
                ],
            ],
        );
    }

    public static function savedSuccessfully(): JsonResponse
    {
        return self::success(__(ResponseMessageEnum::ADDED_SUCCESSFULLY->value));
    }

    public static function updatedSuccessfully(): JsonResponse
    {
        return self::success(__(ResponseMessageEnum::UPDATED_SUCCESSFULLY->value));
    }

    public static function deletedSuccessfully(): JsonResponse
    {
        return self::success(__(ResponseMessageEnum::DELETED_SUCCESSFULLY->value));
    }
}
