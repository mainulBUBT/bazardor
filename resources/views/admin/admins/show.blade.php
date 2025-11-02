@extends('layouts.admin.app')

@section('title', translate('messages.Admin Details'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ translate('messages.Admin Details') }}</h1>
        <p class="mb-0 text-muted">{{ translate('messages.View administrative profile information and permissions.') }}</p>
    </div>
    <div>
        <a href="{{ route('admin.admins.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left fa-sm"></i> {{ translate('messages.Back to list') }}
        </a>
        @can('edit admins')
            <a href="{{ route('admin.admins.edit', $admin->id) }}" class="btn btn-sm btn-primary">
                <i class="fas fa-edit fa-sm"></i> {{ translate('messages.Edit Admin') }}
            </a>
        @endcan
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Basic Information') }}</h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <span class="font-weight-bold text-muted d-block text-uppercase small">{{ translate('messages.Name') }}</span>
                        <span>{{ $admin->name }}</span>
                    </li>
                    <li class="mb-2">
                        <span class="font-weight-bold text-muted d-block text-uppercase small">{{ translate('messages.Email') }}</span>
                        <span>{{ $admin->email }}</span>
                    </li>
                    <li class="mb-2">
                        <span class="font-weight-bold text-muted d-block text-uppercase small">{{ translate('messages.Status') }}</span>
                        @if ($admin->is_active)
                            <span class="badge badge-success">{{ translate('messages.Active') }}</span>
                        @else
                            <span class="badge badge-danger">{{ translate('messages.Inactive') }}</span>
                        @endif
                    </li>
                    <li class="mb-2">
                        <span class="font-weight-bold text-muted d-block text-uppercase small">{{ translate('messages.Created At') }}</span>
                        <span>{{ $admin->created_at->format('M d, Y H:i') }}</span>
                    </li>
                    <li>
                        <span class="font-weight-bold text-muted d-block text-uppercase small">{{ translate('messages.Last updated') }}</span>
                        <span>{{ $admin->updated_at->format('M d, Y H:i') }}</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Roles & Permissions') }}</h6>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-uppercase text-muted small font-weight-bold">{{ translate('messages.Assigned Roles') }}</h6>
                    @if ($admin->roles->isNotEmpty())
                        @foreach ($admin->roles as $role)
                            <span class="badge badge-info mr-1 mb-1">{{ $role->name }}</span>
                        @endforeach
                    @else
                        <p class="text-muted mb-0">{{ translate('messages.No roles assigned') }}</p>
                    @endif
                </div>

                <h6 class="text-uppercase text-muted small font-weight-bold">{{ translate('messages.Permissions') }}</h6>
                @if ($admin->getAllPermissions()->isNotEmpty())
                    <div class="row">
                        @foreach ($admin->getAllPermissions()->chunk(3) as $chunk)
                            <div class="col-md-4">
                                <ul class="list-unstyled">
                                    @foreach ($chunk as $permission)
                                        <li class="mb-1">
                                            <i class="fas fa-check text-success mr-1"></i>
                                            <span class="text-muted small">{{ $permission->name }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">{{ translate('messages.No permissions assigned') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

