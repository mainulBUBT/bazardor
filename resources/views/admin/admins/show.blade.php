@extends('admin.layouts.app')

@section('title', 'Admin Details')

@section('content')
<div class="page-header">
    <h1 class="page-title">Admin Details</h1>
    <div class="page-actions">
        <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
        <!-- @can('edit admins') -->
            <a href="{{ route('admin.admins.edit', $admin->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
        <!-- @endcan -->
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Basic Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td>{{ $admin->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>{{ $admin->email }}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>
                            @if ($admin->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Created:</strong></td>
                        <td>{{ $admin->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Last Updated:</strong></td>
                        <td>{{ $admin->updated_at->format('M d, Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Roles & Permissions</h5>
            </div>
            <div class="card-body">
                <h6>Assigned Roles</h6>
                @if ($admin->roles->isNotEmpty())
                    @foreach ($admin->roles as $role)
                        <span class="badge badge-info mr-1">{{ $role->name }}</span>
                    @endforeach
                @else
                    <p class="text-muted">No roles assigned</p>
                @endif

                <h6 class="mt-3">Permissions</h6>
                @if ($admin->getAllPermissions()->isNotEmpty())
                    <div class="row">
                        @foreach ($admin->getAllPermissions()->chunk(4) as $chunk)
                            <div class="col-md-3">
                                @foreach ($chunk as $permission)
                                    <small class="d-block text-muted">{{ $permission->name }}</small>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No permissions assigned</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
