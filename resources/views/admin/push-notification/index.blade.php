@extends('layouts.admin.app')

@section('title', translate('messages.Push Notifications'))

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ translate('messages.Push Notifications') }}</h1>
        </div>

        <!-- Push Notifications Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.All Push Notifications') }}</h6>
                <div class="d-flex">
                    <a href="{{ route('admin.push-notifications.create') }}" class="btn btn-sm btn-primary mr-2">
                        <i class="fas fa-paper-plane fa-sm"></i> {{ translate('messages.Send New Notification') }}
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="notificationsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>{{ translate('messages.ID') }}</th>
                                <th>{{ translate('messages.Title') }}</th>
                                <th>{{ translate('messages.Message') }}</th>
                                <th>{{ translate('messages.Type') }}</th>
                                <th>{{ translate('messages.Target') }}</th>
                                <th>{{ translate('messages.Sent Date') }}</th>
                                <th>{{ translate('messages.Recipients') }}</th>
                                <th>{{ translate('messages.Status') }}</th>
                                <th>{{ translate('messages.Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notifications as $notification)
                                <tr>
                                    <td>{{ $notification->id }}</td>
                                    <td>{{ Str::limit($notification->title, 30) }}</td>
                                    <td>{{ Str::limit($notification->message, 50) }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $notification->type)) }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $notification->target_audience)) }}</td>
                                    <td>{{ $notification->sent_at ? $notification->sent_at->format('Y-m-d H:i') : 'Not sent' }}</td>
                                    <td>{{ $notification->recipients_count ?? 0 }}</td>
                                    <td>{!! $notification->status_badge !!}</td>
                                    <td>
                                        <a href="{{ route('admin.push-notifications.show', $notification) }}" class="btn btn-info btn-circle btn-sm" title="{{ translate('messages.View') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($notification->status === 'draft')
                                            <a href="{{ route('admin.push-notifications.edit', $notification) }}" class="btn btn-warning btn-circle btn-sm" title="{{ translate('messages.Edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form id="send-notification-{{ $notification->id }}" action="{{ route('admin.push-notifications.send', $notification->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="button" onclick="formAlert('send-notification-{{ $notification->id }}', '{{ translate('messages.Are you sure you want to send this notification?') }}')" class="btn btn-primary btn-circle btn-sm" title="{{ translate('messages.Send') }}">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($notification->status === 'sent')
                                            <form id="resend-notification-{{ $notification->id }}" action="{{ route('admin.push-notifications.resend', $notification->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="button" onclick="formAlert('resend-notification-{{ $notification->id }}', '{{ translate('messages.Are you sure you want to resend this notification?') }}')" class="btn btn-success btn-circle btn-sm" title="{{ translate('messages.Resend') }}">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form id="delete-notification-{{ $notification->id }}" action="{{ route('admin.push-notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="formAlert('delete-notification-{{ $notification->id }}', '{{ translate('messages.Are you sure you want to delete this notification?') }}')" class="btn btn-danger btn-circle btn-sm" title="{{ translate('messages.Delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">{{ translate('messages.No notifications found') }}</td>
                                </tr>
                            @endforelse
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
            $('#notificationsTable').DataTable({
                responsive: true,
                order: [[0, 'desc']]
            });
        });

        </script>
@endpush
