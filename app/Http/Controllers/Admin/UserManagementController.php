<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreUpdateRequest;
use Illuminate\Http\Request;
use App\Services\UserManagementService;
use Brian2694\Toastr\Facades\Toastr;

class UserManagementController extends Controller
{
    public function __construct(protected UserManagementService $userService) {

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
        $users = $this->userService->getUsers($role, $request->search);
        $userStats = $this->userService->getUserStats();

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
        $this->userService->store($request->validated());

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
        $user = $this->userService->findById($id);
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
        $this->userService->update($id, $request->validated());
        Toastr::success(translate('messages.user_updated_successfully'));

        return redirect()->route('admin.users.index', ['role' => $request->role ?? 'user']);
    }
    /**
     * Display the specified user.
     *
     * @param string $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show(string $id)
    {
        $user = $this->userService->findById($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Remove the specified user from storage.
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        $this->userService->delete($id);
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    /**
     * Display a listing of pending users.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function pending()
    {
        $users = $this->userService->getPendingUsers();
        return view('admin.users.pending', compact('users'));
    }

    /**
     * Approve a pending user.
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(string $id)
    {
        $this->userService->approveUser($id);
        Toastr::success(translate('messages.user_approved_successfully'));
        return redirect()->route('admin.users.pending');
    }

    /**
     * Reject a pending user.
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(string $id)
    {
        $this->userService->rejectUser($id);
        Toastr::success(translate('messages.user_rejected_successfully'));
        return redirect()->route('admin.users.pending');
    }
}