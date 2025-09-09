<?php

namespace App\Http\V1\Controllers;

use App\Support\Http\Responses\ApiResponse;

abstract class Controller
{
    public function success(?string $message = null, $data = null, int $status = 200, array $extra = [])
    {
        return ApiResponse::success($message, $data, $status, $extra);
    }

    public function error(?string $message = null, int $status = 500, array $errors = [], array $extra = [])
    {
        return ApiResponse::error($message, $status, $errors, $extra);
    }
}
