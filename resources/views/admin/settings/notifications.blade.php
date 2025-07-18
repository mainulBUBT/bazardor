@section('title', translate('messages.Notification Settings'))
@extends('layouts.admin.app')
@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">{{ translate('messages.Notification Settings') }}</h1>
    <p class="mb-4">{{ translate('messages.Configure notification preferences and delivery settings') }}</p>

    @include('admin.settings._partials.tabs')

    <!-- Settings Container -->
    <div id="settingsContainer">
        <!-- Notification Settings Section -->
        <div class="settings-section notification-settings {{ request()->query('tab') == NOTIFICATION_SETTINGS ? 'active' : '' }}" id="notification-settings">
            
            <!-- User Notifications Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.User Notifications') }}</h6>
                </div>
                <div class="card-body">
                    <div class="toggle-item">
                        <label class="toggle-label">
                            <i class="fas fa-envelope"></i> {{ translate('messages.Email notifications') }}
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" id="enableEmailNotifications" onchange="statusAlert(this)" data-url="{{ route('admin.settings.update-status', ['tab' => NOTIFICATION_SETTINGS]) }}" name="enable_email_notifications" {{ ($settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'enable_email_notifications')->first()?->value ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <label class="toggle-label">
                            <i class="fas fa-mobile-alt"></i> {{ translate('messages.Push notifications') }}
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" id="enablePushNotifications" onchange="statusAlert(this)" data-url="{{ route('admin.settings.update-status', ['tab' => NOTIFICATION_SETTINGS]) }}" name="enable_push_notifications" {{ ($settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'enable_push_notifications')->first()?->value ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <label class="toggle-label">
                            <i class="fas fa-sms"></i> {{ translate('messages.SMS notifications') }}
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" id="enableSmsNotifications" onchange="statusAlert(this)" data-url="{{ route('admin.settings.update-status', ['tab' => NOTIFICATION_SETTINGS]) }}" name="enable_sms_notifications" {{ ($settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'enable_sms_notifications')->first()?->value ?? false) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <label class="toggle-label">
                            <i class="fas fa-chart-line"></i> {{ translate('messages.Price drop alerts') }}
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" id="notifyPriceDrops" onchange="statusAlert(this)" data-url="{{ route('admin.settings.update-status', ['tab' => NOTIFICATION_SETTINGS]) }}" name="notify_price_drops" {{ ($settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'notify_price_drops')->first()?->value ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <label class="toggle-label">
                            <i class="fas fa-store"></i> {{ translate('messages.New market alerts') }}
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" id="notifyNewMarkets" onchange="statusAlert(this)" data-url="{{ route('admin.settings.update-status', ['tab' => NOTIFICATION_SETTINGS]) }}" name="notify_new_markets" {{ ($settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'notify_new_markets')->first()?->value ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Admin Notifications Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Admin Notifications') }}</h6>
                </div>
                <div class="card-body">
                    <div class="toggle-item">
                        <label class="toggle-label">
                            <i class="fas fa-user-plus"></i> {{ translate('messages.New user registrations') }}
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" id="notifyNewUserRegistrations" onchange="statusAlert(this)" data-url="{{ route('admin.settings.update-status', ['tab' => NOTIFICATION_SETTINGS]) }}" name="notify_new_user_registrations" {{ ($settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'notify_new_user_registrations')->first()?->value ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <label class="toggle-label">
                            <i class="fas fa-store-alt"></i> {{ translate('messages.New market submissions') }}
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" id="notifyNewMarketSubmissions" onchange="statusAlert(this)" data-url="{{ route('admin.settings.update-status', ['tab' => NOTIFICATION_SETTINGS]) }}" name="notify_new_market_submissions" {{ ($settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'notify_new_market_submissions')->first()?->value ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <label class="toggle-label">
                            <i class="fas fa-box"></i> {{ translate('messages.New product submissions') }}
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" id="notifyNewProductSubmissions" onchange="statusAlert(this)" data-url="{{ route('admin.settings.update-status', ['tab' => NOTIFICATION_SETTINGS]) }}" name="notify_new_product_submissions" {{ ($settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'notify_new_product_submissions')->first()?->value ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <label class="toggle-label">
                            <i class="fas fa-flag"></i> {{ translate('messages.User reports and flags') }}
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" id="notifyUserReportsFlags" onchange="statusAlert(this)" data-url="{{ route('admin.settings.update-status', ['tab' => NOTIFICATION_SETTINGS]) }}" name="notify_user_reports_flags" {{ ($settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'notify_user_reports_flags')->first()?->value ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="toggle-item">
                        <label class="toggle-label">
                            <i class="fas fa-exclamation-triangle"></i> {{ translate('messages.System errors and warnings') }}
                        </label>
                        <label class="toggle-switch">
                            <input type="checkbox" id="notifySystemErrorsWarnings" onchange="statusAlert(this)" data-url="{{ route('admin.settings.update-status', ['tab' => NOTIFICATION_SETTINGS]) }}" name="notify_system_errors_warnings" {{ ($settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'notify_system_errors_warnings')->first()?->value ?? true) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Notification Schedules Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Notification Schedules') }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update', ['tab' => NOTIFICATION_SETTINGS]) }}" method="POST">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="digestFrequency">{{ translate('messages.Digest Frequency') }}</label>
                                <select class="form-control @error('digest_frequency') is-invalid @enderror" id="digestFrequency" name="digest_frequency" required>
                                    <option value="real-time" {{ ($settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'digest_frequency')->first()?->value ?? 'daily') == 'real-time' ? 'selected' : '' }}>{{ translate('messages.Real-time') }}</option>
                                    <option value="daily" {{ ($settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'digest_frequency')->first()?->value ?? 'daily') == 'daily' ? 'selected' : '' }}>{{ translate('messages.Daily') }}</option>
                                    <option value="weekly" {{ ($settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'digest_frequency')->first()?->value ?? 'daily') == 'weekly' ? 'selected' : '' }}>{{ translate('messages.Weekly') }}</option>
                                </select>
                                @error('digest_frequency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">{{ translate('messages.How often to send notification digests') }}</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="digestDeliveryTime">{{ translate('messages.Digest Delivery Time') }}</label>
                                <input type="time" class="form-control @error('digest_delivery_time') is-invalid @enderror" id="digestDeliveryTime" name="digest_delivery_time" value="{{ $settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'digest_delivery_time')->first()?->value ?? '09:00' }}" required>
                                @error('digest_delivery_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">{{ translate('messages.Time to deliver daily/weekly digests') }}</small>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="quietHoursStart">{{ translate('messages.Quiet Hours Start') }}</label>
                                <input type="time" class="form-control @error('quiet_hours_start') is-invalid @enderror" id="quietHoursStart" name="quiet_hours_start" value="{{ $settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'quiet_hours_start')->first()?->value ?? '22:00' }}" required>
                                @error('quiet_hours_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">{{ translate('messages.Start time for quiet hours (no notifications)') }}</small>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="quietHoursEnd">{{ translate('messages.Quiet Hours End') }}</label>
                                <input type="time" class="form-control @error('quiet_hours_end') is-invalid @enderror" id="quietHoursEnd" name="quiet_hours_end" value="{{ $settings->where('settings_type', NOTIFICATION_SETTINGS)->where('key_name', 'quiet_hours_end')->first()?->value ?? '07:00' }}" required>
                                @error('quiet_hours_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">{{ translate('messages.End time for quiet hours') }}</small>
                            </div>
                        </div>

                        <!-- Save Settings Button -->
                        <div class="col-12 text-right">
                            <button type="submit" class="btn btn-primary" id="saveNotificationSettings">
                                <i class="fas fa-save mr-1"></i>{{ translate('messages.Save Notification Settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Time picker validation for notification schedules
    function validateTimeInputs() {
        const quietStart = $('#quietHoursStart').val();
        const quietEnd = $('#quietHoursEnd').val();
        const deliveryTime = $('#digestDeliveryTime').val();
        
        // Basic time format validation (HH:MM)
        const timeRegex = /^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/;
        
        if (quietStart && !timeRegex.test(quietStart)) {
            $('#quietHoursStart').addClass('is-invalid');
            return false;
        } else {
            $('#quietHoursStart').removeClass('is-invalid');
        }
        
        if (quietEnd && !timeRegex.test(quietEnd)) {
            $('#quietHoursEnd').addClass('is-invalid');
            return false;
        } else {
            $('#quietHoursEnd').removeClass('is-invalid');
        }
        
        if (deliveryTime && !timeRegex.test(deliveryTime)) {
            $('#digestDeliveryTime').addClass('is-invalid');
            return false;
        } else {
            $('#digestDeliveryTime').removeClass('is-invalid');
        }
        
        return true;
    }
    
    // Validate time inputs on change
    $('#quietHoursStart, #quietHoursEnd, #digestDeliveryTime').on('change blur', function() {
        validateTimeInputs();
    });
    
    // Form submission validation
    $('form').on('submit', function(e) {
        if (!validateTimeInputs()) {
            e.preventDefault();
            toastr.error('{{ translate("messages.Please enter valid time format (HH:MM)") }}');
            return false;
        }
    });
    
    // Add loading state to save button
    $('#saveNotificationSettings').on('click', function() {
        const $btn = $(this);
        const originalText = $btn.html();
        
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>{{ translate("messages.Saving...") }}');
        
        // Re-enable button after form submission (in case of validation errors)
        setTimeout(function() {
            $btn.prop('disabled', false).html(originalText);
        }, 3000);
    });
});

// Enhanced statusAlert function for notification settings
function statusAlert(obj) {
    let url = $(obj).data('url');
    let checked = $(obj).prop("checked");
    let status = checked === true ? 1 : 0;
    let settingName = $(obj).attr('name');
    
    // Get user-friendly setting name for confirmation
    let settingLabel = $(obj).closest('.toggle-item').find('.toggle-label').text().trim();
    
    Swal.fire({
        title: '{{ translate("messages.are_you_sure") }}?',
        text: '{{ translate("messages.want_to_change_status") }} "' + settingLabel + '"?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#4e73df',
        cancelButtonColor: '#6c757d',
        cancelButtonText: '{{ translate("messages.no") }}',
        confirmButtonText: '{{ translate("messages.yes") }}',
        reverseButtons: true
    }).then((result) => {
        if (result.value) {
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status,
                    id: settingName,
                    tab: '{{ NOTIFICATION_SETTINGS }}'
                },
                success: function (response) {
                    toastr.success(response.message || "{{ translate('messages.status_changed_successfully') }}");
                },
                error: function (xhr) {
                    // Revert the toggle state
                    $(obj).prop('checked', !checked);
                    
                    // Show error message
                    let errorMessage = "{{ translate('messages.status_change_failed') }}";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    toastr.error(errorMessage);
                }
            });
        } else if (result.dismiss === 'cancel') {
            // Revert the toggle state
            $(obj).prop('checked', !checked);
            toastr.info("{{ translate('messages.status_is_not_changed') }}");
        }
    });
}
</script>
@endpush