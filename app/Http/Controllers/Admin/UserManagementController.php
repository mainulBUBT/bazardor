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

    /**
     * Show the form for creating a new user.
     *
     * @param string $role
     * @return \Illuminate\Contracts\View\View
     */
    public function create(string $role)
    {
        return view('admin.users.create', compact('role'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param UserStoreUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserStoreUpdateRequest $request)
    {
        $this->service->store($request->validated());

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param string $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(string $id)
    {
        $user = $this->service->findById($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param UserStoreUpdateRequest $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserStoreUpdateRequest $request, string $id)
    {
        $this->service->update($id, $request->validated());

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }
} 