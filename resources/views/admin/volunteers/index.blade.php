@extends('layouts.admin.app')
@section('title', translate('messages.Volunteers Management'))

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Volunteers Management</h1>
    <p class="mb-4">Manage all your volunteers in one place. View, edit, or manage volunteer accounts as needed.</p>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Volunteers List</h6>
            <a href="{{ route('admin.volunteers.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-user-plus fa-sm"></i> Add New Volunteer
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="volunteersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Joined Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($volunteers as $volunteer)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $volunteer->avatar_url ?? asset('img/undraw_profile.svg') }}" class="user-avatar mr-2" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                                    <div>
                                        <div class="font-weight-bold">{{ $volunteer->name }}</div>
                                        <div class="small text-muted">@{{ $volunteer->username ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-info">Volunteer</span>
                            </td>
                            <td>{{ $volunteer->email }}</td>
                            <td>{{ $volunteer->phone ?? '-' }}</td>
                            <td>
                                <span class="badge badge-{{ $volunteer->status === 'active' ? 'success' : 'warning' }}">{{ ucfirst($volunteer->status ?? 'active') }}</span>
                            </td>
                            <td>{{ $volunteer->created_at ? $volunteer->created_at->format('Y-m-d') : '' }}</td>
                            <td>
                                <a href="#" class="btn btn-primary btn-circle btn-sm" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" class="btn btn-info btn-circle btn-sm" title="View Profile">
                                    <i class="fas fa-user"></i>
                                </a>
                                <a href="#" class="btn btn-danger btn-circle btn-sm" title="Block">
                                    <i class="fas fa-ban"></i>
                                </a>
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