<?php

namespace App\Enum;

enum OrderStatusEnum: string
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
}
