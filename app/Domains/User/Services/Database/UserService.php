<?php

namespace App\Domains\User\Services\Database;

use App\Domains\User\Models\User;
use App\Domains\User\Services\Contracts\UserService as UserServiceContract;
use App\Support\Services\Database\BaseService;

class UserService extends BaseService implements UserServiceContract
{
    public function __construct()
    {
        parent::__construct(User::class);
    }
}
