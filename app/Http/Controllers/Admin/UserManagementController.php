<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserStoreUpdateRequest;
use Illuminate\Http\Request;
use App\Services\UserManagementService;

class UserManagementController extends Controller
{
    public function __construct(protected UserManagementService $service) {

    }

    /**
     * Display a listing of the users.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {   
        $role = $request->input('role', Role::USER->value);
        $users = $this->service->getUsers($role, $request->search);
        $userStats = $this->service->getUserStats();

        return view('admin.users.index', compact(
            'users',
            'userStats',
            'role'
        ));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function store(UserStoreUpdateRequest $request)
    {
        $this->service->storeUser($request->validated());

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UserStoreUpdateRequest $request, User $user)
    {
        $this->service->updateUser($user, $request->validated());

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }
} 