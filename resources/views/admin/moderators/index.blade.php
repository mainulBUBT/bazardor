@extends('layouts.admin.app')
@section('title', translate('messages.Moderators Management'))

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Moderators Management</h1>
    <p class="mb-4">Manage all your moderators in one place. View, edit, or manage moderator accounts as needed.</p>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Moderators List</h6>
            <a href="{{ route('admin.moderators.create') }}" class="btn btn-sm btn-primary">
                <i class="fas fa-user-plus fa-sm"></i> Add New Moderator
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="moderatorsTable" width="100%" cellspacing="0">
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
                        @foreach($moderators as $moderator)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $moderator->avatar_url ?? asset('img/undraw_profile.svg') }}" class="user-avatar mr-2" style="width:40px;height:40px;border-radius:50%;object-fit:cover;">
                                    <div>
                                        <div class="font-weight-bold">{{ $moderator->name }}</div>
                                        <div class="small text-muted">@{{ $moderator->username ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-danger">Moderator</span>
                            </td>
                            <td>{{ $moderator->email }}</td>
                            <td>{{ $moderator->phone ?? '-' }}</td>
                            <td>
                                <span class="badge badge-{{ $moderator->status === 'active' ? 'success' : 'warning' }}">{{ ucfirst($moderator->status ?? 'active') }}</span>
                            </td>
                            <td>{{ $moderator->created_at ? $moderator->created_at->format('Y-m-d') : '' }}</td>
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