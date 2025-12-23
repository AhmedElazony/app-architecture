<?php

namespace App\Support\Enums;

use App\Support\Traits\HasEnumFunctions;

enum ResponseMessageEnum: string
{
    use HasEnumFunctions;

    case SUCCESS = 'responses.success';
    case FAILED = 'responses.failed';
    case ADDED_SUCCESSFULLY = 'responses.item_added';
    case UPDATED_SUCCESSFULLY = 'responses.item_updated';
    case DELETED_SUCCESSFULLY = 'responses.item_deleted';
}
