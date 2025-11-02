<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminStoreUpdateRequest;
use Illuminate\Http\Request;
use App\Services\AdminManagementService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Gate;

class AdminManagementController extends Controller
{
    public function __construct(
        protected AdminManagementService $adminService
    ) {
        // $this->middleware(['permission:manage admins']);
    }

    /**
     * Display a listing of the admins.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function index(Request $request)
    {   
        $admins = $this->adminService->getAdmins($request->search);

        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new admin.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function create()
    {
        $roles = $this->adminService->getAllRoles();
        
        return view('admin.admins.create', compact('roles'));
    }

    /**
     * Store a newly created admin in storage.
     *
     * @param AdminStoreUpdateRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AdminStoreUpdateRequest $request)
    {
        $data = $request->validated();
        $this->adminService->store($data);

        Toastr::success('Admin created successfully');
        return redirect()->route('admin.admins.index');
    }

    /**
     * Show the form for editing the specified admin.
     *
     * @param string $id
     * @return \Illuminate\Contracts\View\View
     */
    public function edit(string $id)
    {
        $admin = $this->adminService->findById($id);
        $roles = $this->adminService->getAllRoles();
        
        return view('admin.admins.edit', compact('admin', 'roles'));
    }

    /**
     * Update the specified admin in storage.
     *
     * @param AdminStoreUpdateRequest $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(AdminStoreUpdateRequest $request, string $id)
    {
        $data = $request->validated();
        $this->adminService->update($id, $data);
        Toastr::success('Admin updated successfully');

        return redirect()->route('admin.admins.index');
    }

    /**
     * Display the specified admin.
     *
     * @param string $id
     * @return \Illuminate\Contracts\View\View
     */
    public function show(string $id)
    {
        $admin = $this->adminService->findById($id, ['roles', 'permissions']);
        return view('admin.admins.show', compact('admin'));
    }

    /**
     * Remove the specified admin from storage.
     *
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(string $id)
    {
        $this->adminService->delete($id);
        Toastr::success('Admin deleted successfully');
        return redirect()->route('admin.admins.index');
    }
}
