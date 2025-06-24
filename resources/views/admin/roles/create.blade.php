@extends('layouts.admin.app')
@section('title', 'Create Role')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Role</h1>
        <a href="{{ route('admin.roles.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Roles
        </a>
    </div>

    <!-- Content Row -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Role Details</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="name">Role Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Permissions</label>
                    @error('permissions')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="select-all">
                            <label class="custom-control-label" for="select-all">Select All Permissions</label>
                        </div>
                    </div>
                </div>

                @foreach($permissionGroups as $resource => $actions)
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input group-select" id="group-{{ Str::slug($resource) }}">
                            <label class="custom-control-label" for="group-{{ Str::slug($resource) }}">
                                {{ $resource }}
                            </label>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($actions['manage'])
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input permission-checkbox" name="permissions[]" value="{{ $actions['manage']->id }}" id="permission-{{ $actions['manage']->id }}" {{ (is_array(old('permissions')) && in_array($actions['manage']->id, old('permissions'))) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="permission-{{ $actions['manage']->id }}">
                                        <span class="badge badge-primary">Manage</span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            
                            @if($actions['create'])
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input permission-checkbox" name="permissions[]" value="{{ $actions['create']->id }}" id="permission-{{ $actions['create']->id }}" {{ (is_array(old('permissions')) && in_array($actions['create']->id, old('permissions'))) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="permission-{{ $actions['create']->id }}">
                                        <span class="badge badge-success">Create</span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            
                            @if($actions['view'])
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input permission-checkbox" name="permissions[]" value="{{ $actions['view']->id }}" id="permission-{{ $actions['view']->id }}" {{ (is_array(old('permissions')) && in_array($actions['view']->id, old('permissions'))) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="permission-{{ $actions['view']->id }}">
                                        <span class="badge badge-info">View</span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            
                            @if($actions['edit'])
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input permission-checkbox" name="permissions[]" value="{{ $actions['edit']->id }}" id="permission-{{ $actions['edit']->id }}" {{ (is_array(old('permissions')) && in_array($actions['edit']->id, old('permissions'))) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="permission-{{ $actions['edit']->id }}">
                                        <span class="badge badge-warning">Edit</span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            
                            @if($actions['delete'])
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input permission-checkbox" name="permissions[]" value="{{ $actions['delete']->id }}" id="permission-{{ $actions['delete']->id }}" {{ (is_array(old('permissions')) && in_array($actions['delete']->id, old('permissions'))) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="permission-{{ $actions['delete']->id }}">
                                        <span class="badge badge-danger">Delete</span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            
                            @if($actions['approve'])
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input permission-checkbox" name="permissions[]" value="{{ $actions['approve']->id }}" id="permission-{{ $actions['approve']->id }}" {{ (is_array(old('permissions')) && in_array($actions['approve']->id, old('permissions'))) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="permission-{{ $actions['approve']->id }}">
                                        <span class="badge badge-secondary">Approve</span>
                                    </label>
                                </div>
                            </div>
                            @endif
                            
                            @foreach($actions['other'] as $permission)
                            <div class="col-md-3 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input permission-checkbox" name="permissions[]" value="{{ $permission->id }}" id="permission-{{ $permission->id }}" {{ (is_array(old('permissions')) && in_array($permission->id, old('permissions'))) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="permission-{{ $permission->id }}">
                                        <span class="badge badge-dark">{{ ucfirst(explode('_', $permission->name)[0]) }}</span>
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endforeach

                <button type="submit" class="btn btn-primary">Create Role</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Select all permissions
        $('#select-all').change(function() {
            $('.permission-checkbox').prop('checked', $(this).prop('checked'));
            $('.group-select').prop('checked', $(this).prop('checked'));
        });

        // Select group permissions
        $('.group-select').change(function() {
            const groupId = $(this).attr('id');
            const isChecked = $(this).prop('checked');
            $(this).closest('.card').find('.permission-checkbox').prop('checked', isChecked);
            
            // Update "Select All" checkbox
            updateSelectAllCheckbox();
        });

        // Update group checkbox when individual permissions change
        $('.permission-checkbox').change(function() {
            const card = $(this).closest('.card');
            const totalPermissions = card.find('.permission-checkbox').length;
            const checkedPermissions = card.find('.permission-checkbox:checked').length;
            
            card.find('.group-select').prop('checked', totalPermissions === checkedPermissions);
            
            // Update "Select All" checkbox
            updateSelectAllCheckbox();
        });

        // Function to update "Select All" checkbox
        function updateSelectAllCheckbox() {
            const totalPermissions = $('.permission-checkbox').length;
            const checkedPermissions = $('.permission-checkbox:checked').length;
            $('#select-all').prop('checked', totalPermissions === checkedPermissions);
        }
    });
</script>
@endpush 