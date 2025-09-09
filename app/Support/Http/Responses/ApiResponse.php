<?php

namespace App\Support\Http\Responses;

use App\Domains\Management\Enums\ResponseMessageEnum;
use Illuminate\Http\JsonResponse;

class ApiResponse
{
    public static function success(?string $message = null, $data = null, int $status = 200, array $extra = []): JsonResponse
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
