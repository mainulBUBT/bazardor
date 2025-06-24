@extends('layouts.admin.app')
@section('title', 'Role Management')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Role Management</h1>
        <a href="{{ route('admin.roles.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Create New Role
        </a>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Roles</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Permissions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        <tr>
                            <td>{{ ucfirst($role->name) }}</td>
                            <td>
                                <div style="max-height: 150px; overflow-y: auto;">
                                    @php
                                        $groupedPermissions = [];
                                        foreach($role->permissions as $permission) {
                                            $parts = explode('_', $permission->name);
                                            if(count($parts) >= 2) {
                                                $action = $parts[0];
                                                $resource = implode('_', array_slice($parts, 1));
                                                $resourceTitle = ucwords(str_replace('_', ' ', $resource));
                                                
                                                if(!isset($groupedPermissions[$resourceTitle])) {
                                                    $groupedPermissions[$resourceTitle] = [];
                                                }
                                                
                                                $groupedPermissions[$resourceTitle][] = $action;
                                            }
                                        }
                                    @endphp
                                    
                                    @foreach($groupedPermissions as $resource => $actions)
                                        <div class="mb-2">
                                            <strong>{{ $resource }}:</strong>
                                            @foreach($actions as $action)
                                                @php
                                                    $badgeClass = 'secondary';
                                                    switch($action) {
                                                        case 'create': $badgeClass = 'success'; break;
                                                        case 'view': $badgeClass = 'info'; break;
                                                        case 'edit': $badgeClass = 'warning'; break;
                                                        case 'delete': $badgeClass = 'danger'; break;
                                                        case 'manage': $badgeClass = 'primary'; break;
                                                        case 'approve': $badgeClass = 'secondary'; break;
                                                    }
                                                @endphp
                                                <span class="badge badge-{{ $badgeClass }} mr-1">{{ ucfirst($action) }}</span>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                @if(!in_array($role->name, ['super_admin']))
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(!in_array($role->name, ['super_admin', 'moderator', 'volunteer', 'user']))
                                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this role?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>
@endpush 