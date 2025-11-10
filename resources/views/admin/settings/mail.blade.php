@extends('layouts.admin.app')
@section('title', translate('messages.Mail Settings'))

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">{{ translate('messages.Mail Settings') }}</h1>
    <p class="mb-4">{{ translate('messages.Configure outgoing mail credentials and defaults for platform notifications.') }}</p>

    @include('admin.settings._partials.tabs')

    <div id="settingsContainer">
        <div class="settings-section active" id="mail-settings">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Mail Configuration') }}</h6>
                    <span class="badge badge-light text-primary">{{ strtoupper($settings['driver'] ?? 'smtp') }}</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.update', ['tab' => 'mail']) }}" method="POST">
                        @csrf
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="mailDriver">{{ translate('messages.Mail Driver') }}</label>
                                <select class="form-control" id="mailDriver" name="driver">
                                    @php($drivers = ['smtp' => 'SMTP', 'sendmail' => 'Sendmail', 'mailgun' => 'Mailgun', 'ses' => 'Amazon SES'])
                                    @foreach($drivers as $value => $label)
                                        <option value="{{ $value }}" {{ ($settings['driver'] ?? 'smtp') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="mailHost">{{ translate('messages.Mail Host') }}</label>
                                <input type="text" class="form-control" id="mailHost" name="host" value="{{ old('host', $settings['host'] ?? '') }}" placeholder="smtp.mailtrap.io">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="mailPort">{{ translate('messages.Mail Port') }}</label>
                                <input type="text" class="form-control" id="mailPort" name="port" value="{{ old('port', $settings['port'] ?? '') }}" placeholder="2525">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="mailEncryption">{{ translate('messages.Mail Encryption') }}</label>
                                <select class="form-control" id="mailEncryption" name="encryption">
                                    @php($encryptions = ['tls' => 'TLS', 'ssl' => 'SSL', 'null' => translate('messages.None')])
                                    @foreach($encryptions as $value => $label)
                                        <option value="{{ $value === 'null' ? null : $value }}" {{ ($settings['encryption'] ?? 'tls') === ($value === 'null' ? null : $value) ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="mailUsername">{{ translate('messages.Mail Username') }}</label>
                                <input type="text" class="form-control" id="mailUsername" name="username" value="{{ old('username', $settings['username'] ?? '') }}" autocomplete="off">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="mailPassword">{{ translate('messages.Mail Password') }}</label>
                                <input type="password" class="form-control" id="mailPassword" name="password" value="{{ old('password', $settings['password'] ?? '') }}" autocomplete="new-password">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="mailFromAddress">{{ translate('messages.From Address') }}</label>
                                <input type="email" class="form-control" id="mailFromAddress" name="from_address" value="{{ old('from_address', $settings['from_address'] ?? '') }}" placeholder="no-reply@example.com">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="mailFromName">{{ translate('messages.From Name') }}</label>
                                <input type="text" class="form-control" id="mailFromName" name="from_name" value="{{ old('from_name', $settings['from_name'] ?? '') }}" placeholder="{{ config('app.name') }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-outline-secondary" id="testMailConnection" data-toggle="modal" data-target="#testMailModal">
                                <i class="fas fa-paper-plane mr-1"></i> {{ translate('messages.Test Mail Connection') }}
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>{{ translate('messages.Save Settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Test Mail Modal -->
    <div class="modal fade" id="testMailModal" tabindex="-1" role="dialog" aria-labelledby="testMailModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testMailModalLabel">
                        <i class="fas fa-paper-plane mr-2"></i> {{ translate('messages.Test Mail Connection') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="testMailForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="testEmail">{{ translate('messages.Recipient Email') }}</label>
                            <input type="email" class="form-control" id="testEmail" name="test_email" required placeholder="{{ translate('messages.Enter email address to send test mail') }}">
                            <small class="form-text text-muted">{{ translate('messages.A test email will be sent to this address to verify mail configuration') }}</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('messages.Cancel') }}</button>
                        <button type="submit" class="btn btn-primary" id="sendTestMailBtn">
                            <i class="fas fa-paper-plane mr-1"></i> {{ translate('messages.Send Test Mail') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(function () {
        const testMailConfig = <?php echo json_encode([
            'sendingText' => translate('messages.Sending...'),
            'fallbackError' => translate('messages.Failed to send test email'),
            'route' => route('admin.settings.test-mail'),
            'token' => csrf_token(),
        ]); ?>;

        $('#testMailForm').on('submit', function (e) {
            e.preventDefault();

            const email = $('#testEmail').val();
            const $submitBtn = $('#sendTestMailBtn');
            const originalHtml = $submitBtn.html();

            $submitBtn.html(`<i class="fas fa-spinner fa-spin mr-1"></i>${testMailConfig.sendingText}`).prop('disabled', true);

            $.ajax({
                url: testMailConfig.route,
                method: 'POST',
                data: {
                    _token: testMailConfig.token,
                    test_email: email
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#testMailModal').modal('hide');
                        $('#testMailForm')[0].reset();
                    } else {
                        toastr.error(response.message || testMailConfig.fallbackError);
                    }
                },
                error: function (xhr) {
                    const message = xhr.responseJSON?.message || testMailConfig.fallbackError;
                    toastr.error(message);
                },
                complete: function () {
                    $submitBtn.html(originalHtml).prop('disabled', false);
                }
            });
        });

        $('#testMailModal').on('hidden.bs.modal', function () {
            $('#testMailForm')[0].reset();
        });
    });
</script>
@endpush
