<?php

namespace App\Service\Enum;

enum Status
{
    case ACTIVE;
    case INACTIVE;

    public function status(): string
    {
        return match ($this) {
            Status::ACTIVE => 'active',
            Status::INACTIVE => 'inactive',
        };
    }
}
