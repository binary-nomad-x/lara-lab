<?php

namespace App\Enums;

enum UserStatuses: string {
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case DISABLED = 'disabled';
}
