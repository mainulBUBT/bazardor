@extends('layouts.admin.app')
@section('title', translate('messages.Roles Management'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ translate('messages.Roles Management') }}</h1>
    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> {{ translate('messages.Add New Role') }}
    </a>
</div>

<!-- Roles Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.All Roles') }}</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>{{ translate('messages.ID') }}</th>
                        <th>{{ translate('messages.Role Name') }}</th>
                        <th>{{ translate('messages.Permissions Count') }}</th>
                        <th>{{ translate('messages.Users Count') }}</th>
                        <th>{{ translate('messages.Created At') }}</th>
                        <th>{{ translate('messages.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td>{{ $role->id }}</td>
                            <td>
                                <span class="badge badge-primary">{{ ucwords(str_replace('_', ' ', $role->name)) }}</span>
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $role->permissions->count() }}</span>
                            </td>
                            <td>
                                <span class="badge badge-success">{{ $role->users->count() }}</span>
                            </td>
                            <td>{{ $role->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.roles.edit', $role) }}" 
                                       class="btn btn-sm btn-outline-primary"
                                       title="{{ translate('messages.Edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(!in_array($role->name, ['super_admin', 'moderator', 'volunteer', 'user']))
                                        <form action="{{ route('admin.roles.destroy', $role) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('{{ translate('messages.Are you sure you want to delete this role?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="{{ translate('messages.Delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">{{ translate('messages.No roles found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#dataTable').DataTable({
        "order": [[ 0, "desc" ]],
        "pageLength": 25,
        "responsive": true
    });
});
</script>
@endpush