@extends('layouts.admin.app')
@section('title', translate('messages.Edit Role'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ translate('messages.Edit Role') }}: {{ ucwords(str_replace('_', ' ', $role->name)) }}</h1>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ translate('messages.Back to Roles') }}
    </a>
</div>

<!-- Edit Role Form -->
<form action="{{ route('admin.roles.update', $role) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-lg-4">
            <!-- Role Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Role Information') }}</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="roleName">{{ translate('messages.Role Name') }} <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="roleName" 
                               name="name" 
                               value="{{ old('name', $role->name) }}"
                               placeholder="{{ translate('messages.Enter role name') }}" 
                               {{ $role->name === 'super_admin' ? 'readonly' : '' }}
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($role->name === 'super_admin')
                            <small class="text-muted">{{ translate('messages.Super admin role name cannot be changed') }}</small>
                        @endif
                    </div>
                    
                    <div class="form-group">
                        <label>{{ translate('messages.Users with this role') }}</label>
                        <div class="badge badge-info">{{ $role->users->count() }} {{ translate('messages.users') }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <!-- Permissions Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Permissions') }}</h6>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">
                            {{ translate('messages.Select All') }}
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAll">
                            {{ translate('messages.Deselect All') }}
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($permissionGroups)
                        @foreach($permissionGroups as $resource => $permissions)
                            <div class="permission-group mb-4">
                                <h6 class="text-primary border-bottom pb-2">
                                    <i class="fas fa-cog mr-2"></i>{{ $resource }}
                                    <button type="button" class="btn btn-sm btn-outline-info ml-2 group-toggle" data-group="{{ $resource }}">
                                        {{ translate('messages.Toggle All') }}
                                    </button>
                                </h6>
                                <div class="row">
                                    @foreach(['manage', 'create', 'view', 'edit', 'delete', 'approve'] as $action)
                                        @if($permissions[$action])
                                            <div class="col-md-4 col-sm-6 mb-2">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" 
                                                           class="custom-control-input permission-checkbox" 
                                                           id="permission_{{ $permissions[$action]->id }}" 
                                                           name="permissions[]" 
                                                           value="{{ $permissions[$action]->id }}"
                                                           data-group="{{ $resource }}"
                                                           {{ in_array($permissions[$action]->id, $rolePermissions) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="permission_{{ $permissions[$action]->id }}">
                                                        <span class="badge badge-{{ $action === 'delete' ? 'danger' : ($action === 'create' ? 'success' : ($action === 'edit' ? 'warning' : 'info')) }}">
                                                            {{ ucfirst($action) }}
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                    @if(!empty($permissions['other']))
                                        @foreach($permissions['other'] as $permission)
                                            <div class="col-md-4 col-sm-6 mb-2">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" 
                                                           class="custom-control-input permission-checkbox" 
                                                           id="permission_{{ $permission->id }}" 
                                                           name="permissions[]" 
                                                           value="{{ $permission->id }}"
                                                           data-group="{{ $resource }}"
                                                           {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="permission_{{ $permission->id }}">
                                                        <span class="badge badge-secondary">
                                                            {{ ucwords(str_replace('_', ' ', $permission->name)) }}
                                                        </span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                    
                    @error('permissions')
                        <div class="alert alert-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="row">
        <div class="col-lg-12 d-flex justify-content-end mb-4">
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary mr-2">
                <i class="fas fa-times"></i> {{ translate('messages.Cancel') }}
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ translate('messages.Update Role') }}
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Select All functionality
    $('#selectAll').on('click', function() {
        $('.permission-checkbox').prop('checked', true);
    });
    
    // Deselect All functionality
    $('#deselectAll').on('click', function() {
        $('.permission-checkbox').prop('checked', false);
    });
    
    // Group toggle functionality
    $('.group-toggle').on('click', function() {
        const group = $(this).data('group');
        const groupCheckboxes = $(`.permission-checkbox[data-group="${group}"]`);
        const allChecked = groupCheckboxes.length === groupCheckboxes.filter(':checked').length;
        
        groupCheckboxes.prop('checked', !allChecked);
    });
});
</script>
@endpush