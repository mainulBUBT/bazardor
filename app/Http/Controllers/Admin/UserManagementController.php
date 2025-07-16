<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreUpdateRequest;
use Illuminate\Http\Request;
use App\Services\UserManagementService;
use App\Services\RoleService;
use Brian2694\Toastr\Facades\Toastr;


class UserManagementController extends Controller
{
    public function __construct(
        protected UserManagementService $userService,
        protected RoleService $roleService
    ) {
    }

    /**
     * Display a listing of the users.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {   
        $role = $request->input('role', UserType::USER->value);
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
        $functionalRoles = $this->userService->getFunctionalRoles();
        $userTypeOptions = $this->userService->getUserTypeOptions();
        
        return view('admin.users.create', compact('role', 'functionalRoles', 'userTypeOptions'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param UserStoreUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserStoreUpdateRequest $request)
    {
        $data = $request->validated();
        
        // Handle functional roles if provided
        if ($request->has('functional_roles')) {
            $data['functional_roles'] = $request->functional_roles;
        }
        
        $this->userService->store($data);

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
        $functionalRoles = $this->userService->getFunctionalRoles();
        $userTypeOptions = $this->userService->getUserTypeOptions();
        
        return view('admin.users.edit', compact('user', 'functionalRoles', 'userTypeOptions'));
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
        $data = $request->validated();
        
        // Handle functional roles if provided
        if ($request->has('functional_roles')) {
            $data['functional_roles'] = $request->functional_roles;
        }
        
        $this->userService->update($id, $data);
        Toastr::success('User updated successfully');

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
        $user = $this->userService->findById($id, ['roles']);
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
        Toastr::success('User approved successfully');
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
        Toastr::success('User rejected successfully');
        return redirect()->route('admin.users.pending');
    }
}