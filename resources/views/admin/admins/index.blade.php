@extends('admin.layouts.app')

@section('title', 'Admins')

@section('content')
<div class="page-header">
    <h1 class="page-title">Admins</h1>
    <div class="page-actions">
        <!-- @can('create admins') -->
            <a href="{{ route('admin.admins.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Admin
            </a>
        <!-- @endcan -->
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.admins.index') }}" class="mb-3">
            <div class="row">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control" placeholder="Search admins..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-secondary">Search</button>
                    <a href="{{ route('admin.admins.index') }}" class="btn btn-link">Clear</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($admins as $admin)
                        <tr>
                            <td>{{ $admin->name }}</td>
                            <td>{{ $admin->email }}</td>
                            <td>{{ $admin->roles->pluck('name')->implode(', ') ?: '—' }}</td>
                            <td>
                                @if ($admin->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </td>
                            <td>{{ $admin->created_at->format('M d, Y') }}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a href="{{ route('admin.admins.show', $admin->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <!-- @can('edit admins') -->
                                        <a href="{{ route('admin.admins.edit', $admin->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    <!-- @endcan -->
                                    @can('delete admins')
                                        <form method="POST" action="{{ route('admin.admins.destroy', $admin->id) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No admins found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center">
            {{ $admins->links() }}
        </div>
    </div>
</div>
@endsection
