@extends('layouts.admin.app')
@section('title', translate('messages.Backup & Maintenance'))

@section('content')
    <h1 class="h3 mb-2 text-gray-800">{{ translate('messages.Settings') }}</h1>
    <p class="mb-4">{{ translate('messages.Configure your system settings for Bazar-dor application') }}</p>

    @include('admin.settings._partials.tabs')

    <!-- Settings Container -->
    <div id="settingsContainer">
        <div class="settings-section {{ request()->query('tab') == 'backup' ? 'active' : '' }}" id="backup-settings">
            
            <!-- Maintenance Mode -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Maintenance Mode') }}</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">{{ translate('messages.Enable maintenance mode to show a maintenance message to users') }}</p>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="maintenanceMode" {{ ($settings->where('settings_type', 'general')->where('key_name', 'maintenance_mode')->first()->value ?? '0') == '1' ? 'checked' : '' }}>
                        <label class="custom-control-label" for="maintenanceMode">
                            <strong>{{ translate('messages.Enable Maintenance Mode') }}</strong>
                        </label>
                    </div>
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="fas fa-info-circle mr-2"></i>
                        {{ translate('messages.When enabled, users will see a maintenance message on the app') }}
                    </div>
                </div>
            </div>

            <!-- Cache Management -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Cache Management') }}</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">{{ translate('messages.Clear cached data to improve performance or resolve issues') }}</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-info h-100">
                                <div class="card-body">
                                    <h6 class="font-weight-bold text-info">{{ translate('messages.Application Cache') }}</h6>
                                    <p class="small text-muted mb-3">{{ translate('messages.Clear all cached configuration, routes, and views') }}</p>
                                    <button type="button" class="btn btn-sm btn-info" onclick="clearCache('all')">
                                        <i class="fas fa-sync-alt mr-1"></i> {{ translate('messages.Clear All Cache') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-warning h-100">
                                <div class="card-body">
                                    <h6 class="font-weight-bold text-warning">{{ translate('messages.View Cache') }}</h6>
                                    <p class="small text-muted mb-3">{{ translate('messages.Clear compiled Blade templates') }}</p>
                                    <button type="button" class="btn btn-sm btn-warning" onclick="clearCache('view')">
                                        <i class="fas fa-eye-slash mr-1"></i> {{ translate('messages.Clear View Cache') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Database Backup -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Database Backup') }}</h6>
                    <span class="badge badge-info">{{ translate('messages.Manual Backup') }}</span>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">{{ translate('messages.Create a backup of your database to protect your data') }}</p>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        {{ translate('messages.Backup will be saved to storage/app/backups directory') }}
                    </div>
                    <button type="button" class="btn btn-primary" onclick="createBackup()">
                        <i class="fas fa-download mr-1"></i> {{ translate('messages.Create Backup Now') }}
                    </button>
                </div>
            </div>

            <!-- System Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.System Information') }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless mb-0">
                            <tbody>
                                <tr>
                                    <td class="font-weight-bold" width="30%">{{ translate('messages.Laravel Version') }}</td>
                                    <td>{{ app()->version() }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">{{ translate('messages.PHP Version') }}</td>
                                    <td>{{ PHP_VERSION }}</td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">{{ translate('messages.Environment') }}</td>
                                    <td>
                                        <span class="badge badge-{{ app()->environment('production') ? 'success' : 'warning' }}">
                                            {{ strtoupper(app()->environment()) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="font-weight-bold">{{ translate('messages.Debug Mode') }}</td>
                                    <td>
                                        <span class="badge badge-{{ config('app.debug') ? 'danger' : 'success' }}">
                                            {{ config('app.debug') ? translate('messages.Enabled') : translate('messages.Disabled') }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Maintenance mode toggle
    $('#maintenanceMode').on('change', function() {
        const isEnabled = $(this).is(':checked');
        const action = isEnabled ? '{{ translate("messages.enable") }}' : '{{ translate("messages.disable") }}';
        
        Swal.fire({
            title: '{{ translate("messages.Are you sure?") }}',
            text: '{{ translate("messages.Do you want to") }} ' + action + ' {{ translate("messages.maintenance mode?") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '{{ translate("messages.Yes") }}',
            cancelButtonText: '{{ translate("messages.Cancel") }}'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.settings.toggle-maintenance") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        enable: isEnabled
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire('{{ translate("messages.Success") }}', response.message, 'success');
                        } else {
                            Swal.fire('{{ translate("messages.Error") }}', response.message, 'error');
                            $('#maintenanceMode').prop('checked', !isEnabled);
                        }
                    },
                    error: function() {
                        Swal.fire('{{ translate("messages.Error") }}', '{{ translate("messages.An error occurred") }}', 'error');
                        $('#maintenanceMode').prop('checked', !isEnabled);
                    }
                });
            } else {
                $('#maintenanceMode').prop('checked', !isEnabled);
            }
        });
    });
});

function clearCache(type) {
    Swal.fire({
        title: '{{ translate("messages.Clear Cache?") }}',
        text: '{{ translate("messages.Are you sure you want to clear the cache?") }}',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{ translate("messages.Yes, Clear it") }}',
        cancelButtonText: '{{ translate("messages.Cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.settings.clear-cache") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    type: type
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('{{ translate("messages.Success") }}', response.message, 'success');
                    } else {
                        Swal.fire('{{ translate("messages.Error") }}', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('{{ translate("messages.Error") }}', '{{ translate("messages.An error occurred") }}', 'error');
                }
            });
        }
    });
}

function createBackup() {
    Swal.fire({
        title: '{{ translate("messages.Create Backup?") }}',
        text: '{{ translate("messages.Are you sure you want to create a database backup?") }}',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '{{ translate("messages.Yes, Create it") }}',
        cancelButtonText: '{{ translate("messages.Cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: '{{ translate("messages.Creating backup...") }}',
                text: '{{ translate("messages.Please wait") }}',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route("admin.settings.create-backup") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire('{{ translate("messages.Success") }}', response.message, 'success');
                        if (response.download_url) {
                            window.location.href = response.download_url;
                        }
                    } else {
                        Swal.fire('{{ translate("messages.Error") }}', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('{{ translate("messages.Error") }}', '{{ translate("messages.An error occurred") }}', 'error');
                }
            });
        }
    });
}
</script>
@endpush
