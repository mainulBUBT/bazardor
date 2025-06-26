<?php

namespace App\Enums;

enum UserType: string
{
    case SUPER_ADMIN = 'super_admin';
    case MODERATOR = 'moderator';
    case VOLUNTEER = 'volunteer';
    case USER = 'user';
} 