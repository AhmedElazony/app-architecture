<?php

namespace App\Support\Enums;

use App\Support\Traits\HasEnumFunctions;

enum ResponseMessageEnum: string
{
    use HasEnumFunctions;

    case SUCCESS = 'responses.success';
    case FAILED = 'responses.failed';
    case FETCHED_SUCCESSFULLY = 'responses.items_fetched';
    case ADDED_SUCCESSFULLY = 'responses.item_added';
    case UPDATED_SUCCESSFULLY = 'responses.item_updated';
    case DELETED_SUCCESSFULLY = 'responses.item_deleted';
    case LOGIN_SUCCESSFUL = 'responses.login_successful';
    case LOGOUT_SUCCESSFUL = 'responses.logout_successful';
    case INVALID_CREDENTIALS = 'responses.invalid_credentials';
    case UNAUTHORIZED = 'responses.unauthorized';
    case FORBIDDEN = 'responses.forbidden';
    case ALREADY_EXISTS = 'responses.already_exists';
    case NOT_FOUND = 'responses.not_found';
}
