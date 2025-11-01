<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;

class Admin extends Authenticatable
{
    use Notifiable, HasRoles, HasUuid;

    protected $guarded = [];
    protected $guard_name = 'admin';
    public $incrementing = false;
    protected $keyType = 'string';

}
