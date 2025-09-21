<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'as' => 'api.v1.'], function () {
    Route::middleware('auth:sanctum')->group(function () {
        require __DIR__.'/v1/management.php';
    });

    require __DIR__.'/v1/user.php';
});
