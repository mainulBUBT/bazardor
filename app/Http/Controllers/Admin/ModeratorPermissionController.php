<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Enums\Role;
use App\Enums\Permission;

class ModeratorPermissionController extends Controller
{
    public function index()
    {
        $moderators = User::where('role', Role::MODERATOR)->get();
        $permissions = Permission::cases();
        return view('admin.moderators.permissions', compact('moderators', 'permissions'));
    }
} 