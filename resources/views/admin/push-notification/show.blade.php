@extends('layouts.admin.app')

@section('title', translate('messages.View Push Notification'))

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ translate('messages.View Push Notification') }}</h1>
            <a href="{{ route('admin.push-notifications.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ translate('messages.Back to Notifications') }}
            </a>
        </div>

        <div class="row">
            <!-- Notification Details -->
            <div class="col-xl-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Notification Details') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="notification-preview p-3 mb-4 border rounded">
                            <div class="d-flex align-items-center mb-2">
                                <div class="mr-2">
                                    <i class="fas fa-store text-primary"></i>
                                </div>
                                <div>
                                    <div class="small font-weight-bold">Bazar-dor</div>
                                    <div class="small text-muted">{{ $notification->sent_at ? $notification->sent_at->diffForHumans() : translate('messages.Not sent') }}</div>
                                </div>
                            </div>
                            <h5 class="text-primary mb-2">{{ $notification->title }}</h5>
                            <p class="mb-2">{{ $notification->message }}</p>
                            @if($notification->image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $notification->image) }}" alt="{{ translate('messages.Notification Image') }}" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                            @endif
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>{{ translate('messages.Type') }}:</strong> {{ ucfirst(str_replace('_', ' ', $notification->type)) }}</p>
                                <p><strong>{{ translate('messages.Target Audience') }}:</strong> {{ ucfirst(str_replace('_', ' ', $notification->target_audience)) }}</p>
                                <p><strong>{{ translate('messages.Priority Level') }}:</strong> {{ ucfirst($notification->priority_level) }}</p>
                                @if($notification->link_action && $notification->link_action !== 'none')
                                    <p><strong>{{ translate('messages.Link Action') }}:</strong> {{ ucfirst(str_replace('_', ' ', $notification->link_action)) }}</p>
                                    @if($notification->link_url)
                                        <p><strong>{{ translate('messages.Link URL') }}:</strong> {{ $notification->link_url }}</p>
                                    @endif
                                @endif
                            </div>
                            <div class="col-md-6">
                                <p><strong>{{ translate('messages.Status') }}:</strong> {!! $notification->status_badge !!}</p>
                                <p><strong>{{ translate('messages.Created At') }}:</strong> {{ $notification->created_at->format('Y-m-d H:i') }}</p>
                                @if($notification->scheduled_at)
                                    <p><strong>{{ translate('messages.Scheduled For') }}:</strong> {{ $notification->scheduled_at->format('Y-m-d H:i') }}</p>
                                @endif
                                @if($notification->sent_at)
                                    <p><strong>{{ translate('messages.Sent At') }}:</strong> {{ $notification->sent_at->format('Y-m-d H:i') }}</p>
                                @endif
                            </div>
                        </div>

                        @if($notification->target_audience === 'custom' && $notification->custom_segment)
                            <div class="mt-3">
                                <p><strong>{{ translate('messages.Custom Segment Criteria') }}:</strong></p>
                                <p class="text-muted">{{ $notification->custom_segment }}</p>
                            </div>
                        @endif

                        <div class="mt-3">
                            <p><strong>{{ translate('messages.Created By') }}:</strong> {{ $notification->creator->name ?? 'System' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics & Actions -->
            <div class="col-xl-4">
                <!-- Statistics -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Statistics') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="mb-3">
                                    <h4 class="text-primary">{{ $notification->recipients_count ?? 0 }}</h4>
                                    <p class="small text-muted">{{ translate('messages.Recipients') }}</p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <h4 class="text-success">{{ $notification->opened_count ?? 0 }}</h4>
                                    <p class="small text-muted">{{ translate('messages.Opened') }}</p>
                                </div>
                            </div>
                        </div>
                        @if($notification->recipients_count > 0)
                            <div class="text-center">
                                <h5 class="text-info">{{ $notification->open_rate }}%</h5>
                                <p class="small text-muted">{{ translate('messages.Open Rate') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Actions') }}</h6>
                    </div>
                    <div class="card-body">
                        @if($notification->status === 'draft')
                            <a href="{{ route('admin.push-notifications.edit', $notification) }}" class="btn btn-warning btn-block mb-2">
                                <i class="fas fa-edit mr-1"></i> {{ translate('messages.Edit Notification') }}
                            </a>
                            <form id="send-notification-{{ $notification->id }}" action="{{ route('admin.push-notifications.send', $notification->id) }}" method="POST">
                                @csrf
                                <button type="button" onclick="formAlert('send-notification-{{ $notification->id }}', '{{ translate('messages.Are you sure you want to send this notification?') }}')" class="btn btn-primary btn-block mb-2">
                                    <i class="fas fa-paper-plane mr-1"></i> {{ translate('messages.Send Notification') }}
                                </button>
                            </form>
                        @endif

                        @if($notification->status === 'sent')
                            <form id="resend-notification-{{ $notification->id }}" action="{{ route('admin.push-notifications.resend', $notification->id) }}" method="POST">
                                @csrf
                                <button type="button" onclick="formAlert('resend-notification-{{ $notification->id }}', '{{ translate('messages.Are you sure you want to resend this notification?') }}')" class="btn btn-success btn-block mb-2">
                                    <i class="fas fa-redo mr-1"></i> {{ translate('messages.Resend Notification') }}
                                </button>
                            </form>
                        @endif

                        <form id="delete-notification-{{ $notification->id }}" action="{{ route('admin.push-notifications.destroy', $notification->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="formAlert('delete-notification-{{ $notification->id }}', '{{ translate('messages.Are you sure you want to delete this notification?') }}')" class="btn btn-danger btn-block">
                                <i class="fas fa-trash mr-1"></i> {{ translate('messages.Delete Notification') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // No custom JavaScript needed - using formAlert from layout
        });
    </script>
@endpush
