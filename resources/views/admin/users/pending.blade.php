@extends('layouts.admin.app')
@section('title', translate('messages.pending_users'))

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">{{ translate('messages.pending_users') }}</h1>
    <p class="mb-4">{{ translate('messages.pending_users_description') }}</p>

    <!-- DataTables Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="row align-items-center">
                <div class="col-6">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.pending_users_list') }}</h6>
                </div>
                <div class="col-6 text-right">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-users"></i> {{ translate('messages.all_users') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="pendingUsersTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ translate('messages.id') }}</th>
                            <th>{{ translate('messages.name') }}</th>
                            <th>{{ translate('messages.email') }}</th>
                            <th>{{ translate('messages.phone') }}</th>
                            <th>{{ translate('messages.registration_date') }}</th>
                            <th>{{ translate('messages.status') }}</th>
                            <th class="text-center">{{ translate('messages.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $key=>$user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
                                <td>
                                    <span class="badge badge-warning">{{ translate('messages.pending') }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.users.show', $user->id) }}" 
                                           class="btn btn-info btn-sm" 
                                           title="{{ translate('messages.view') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user->id) }}" 
                                           class="btn btn-primary btn-sm"
                                           title="{{ translate('messages.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.users.approve', $user->id) }}" 
                                              method="POST" 
                                              class="d-inline-block">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-success btn-sm"
                                                    title="{{ translate('messages.approve') }}"
                                                    onclick="return confirm('{{ translate('messages.are_you_sure_to_approve') }}')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.users.reject', $user->id) }}" 
                                              method="POST" 
                                              class="d-inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-danger btn-sm"
                                                    title="{{ translate('messages.reject') }}"
                                                    onclick="return confirm('{{ translate('messages.are_you_sure_to_reject') }}')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
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
        $('#pendingUsersTable').DataTable({
            order: [[4, 'desc']], // Sort by registration date by default
            language: {
                search: "{{ translate('messages.search') }}",
                lengthMenu: "{{ translate('messages.show') }} _MENU_ {{ translate('messages.entries') }}",
                info: "{{ translate('messages.showing') }} _START_ {{ translate('messages.to') }} _END_ {{ translate('messages.of') }} _TOTAL_ {{ translate('messages.entries') }}",
                paginate: {
                    first: "{{ translate('messages.first') }}",
                    last: "{{ translate('messages.last') }}",
                    next: "{{ translate('messages.next') }}",
                    previous: "{{ translate('messages.previous') }}"
                }
            }
        });
    });
</script>
@endpush
