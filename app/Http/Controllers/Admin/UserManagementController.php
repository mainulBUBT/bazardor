<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreUpdateRequest;
use Illuminate\Http\Request;
use App\Services\UserManagementService;
use Brian2694\Toastr\Facades\Toastr;

class UserManagementController extends Controller
{
    public function __construct(
        protected UserManagementService $userService
    ) {
    }

    /**
     * Display a listing of the users (API users only).
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {   
        $userType = $request->input('user_type', UserType::USER->value);
        $users = $this->userService->getUsers($userType, $request->search);
        $userStats = $this->userService->getUserStats();

        return view('admin.users.index', compact(
            'users',
            'userStats',
            'userType'
        ));
    }

    /**
     * Show the form for creating a new API user.
     *
     * @param string $userType
     * @return \Illuminate\Contracts\View\View
     */
    public function create(string $userType)
    {
        $userTypeOptions = $this->userService->getUserTypeOptions();
        
        return view('admin.users.create', compact('userType', 'userTypeOptions'));
    }

    /**
     * Store a newly created API user in storage.
     *
     * @param UserStoreUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserStoreUpdateRequest $request)
    {
        $data = $request->validated();
        $this->userService->store($data);

        Toastr::success('User created successfully');
        return redirect()->route('admin.users.index', ['user_type' => $data['user_type']]);
    }

    /**
     * Show the form for editing the specified API user.
     *
     * @param string $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(string $id)
    {
        $user = $this->userService->findById($id);
        $userTypeOptions = $this->userService->getUserTypeOptions();
        
        return view('admin.users.edit', compact('user', 'userTypeOptions'));
    }

    /**
     * Update the specified API user in storage.
     *
     * @param UserStoreUpdateRequest $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserStoreUpdateRequest $request, string $id)
    {
        $data = $request->validated();
        $this->userService->update($id, $data);
        Toastr::success('User updated successfully');

        return redirect()->route('admin.users.index', ['user_type' => $data['user_type']]);
    }
    /**
     * Display the specified API user.
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
     * Remove the specified API user from storage.
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        $this->userService->delete($id);
        Toastr::success('User deleted successfully');
        return redirect()->route('admin.users.index');
    }

    /**
     * Display a listing of pending API users.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function pending()
    {
        $users = $this->userService->getPendingUsers();
        return view('admin.users.pending', compact('users'));
    }

    /**
     * Approve a pending API user.
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
     * Reject a pending API user.
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