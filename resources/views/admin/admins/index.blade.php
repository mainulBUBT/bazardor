@extends('layouts.admin.app')

@section('title', translate('messages.Admins Management'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ translate('messages.Admins Management') }}</h1>
        <p class="mb-0 text-muted">{{ translate('messages.Manage the administrator accounts and their access levels.') }}</p>
    </div>
</div>

<!-- Admins Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            {{ translate('messages.admins_list') }}
        </h6>
        <div class="d-flex">
            <form method="GET" action="{{ route('admin.admins.index') }}" class="form-inline mr-2">
                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search...') }}" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
            </form>
            <a href="{{ route('admin.admins.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-plus fa-sm"></i> {{ translate('messages.add_admin') }}
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="adminsTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>{{ translate('messages.id') }}</th>
                        <th>{{ translate('messages.name') }}</th>
                        <th>{{ translate('messages.email') }}</th>
                        <th>{{ translate('messages.role') }}</th>
                        <th>{{ translate('messages.status') }}</th>
                        <th>{{ translate('messages.created_at') }}</th>
                        <th>{{ translate('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($admins as $admin)
                        <tr>
                            <td>{{ $admin->id }}</td>
                            <td>
                                <div class="font-weight-bold">{{ $admin->name }}</div>
                            </td>
                            <td>{{ $admin->email }}</td>
                            <td>
                                @if($admin->roles->isNotEmpty())
                                    @foreach($admin->roles as $role)
                                        <span class="badge badge-info mr-1">{{ $role->name }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">{{ translate('messages.not_assigned') }}</span>
                                @endif
                            </td>
                            <td>
                                @if($admin->is_active)
                                    <span class="badge badge-success">{{ translate('messages.active') }}</span>
                                @else
                                    <span class="badge badge-danger">{{ translate('messages.inactive') }}</span>
                                @endif
                            </td>
                            <td>{{ $admin->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('admin.admins.show', $admin->id) }}" class="btn btn-sm btn-info" title="{{ translate('messages.view_details') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.admins.edit', $admin->id) }}" class="btn btn-sm btn-primary" title="{{ translate('messages.edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.admins.destroy', $admin->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" data-confirm="{{ __('Are you sure you want to delete this admin?') }}" onclick="return confirm(this.dataset.confirm)" title="{{ translate('messages.delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">{{ translate('messages.no_admins_found') }}</td>
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
    $('#adminsTable').DataTable({
        "order": [[ 0, "desc" ]],
        "pageLength": 25,
        "responsive": true
    });
});
</script>
@endpush

