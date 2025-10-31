@extends('layouts.admin.app')

@section('title', translate('messages.Send Push Notification'))

@section('content')
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">{{ translate('messages.Send Push Notification') }}</h1>
            <a href="{{ route('admin.push-notifications.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
                <i class="fas fa-list fa-sm text-white-50"></i> {{ translate('messages.View All Notifications') }}
            </a>
        </div>

        <form action="{{ route('admin.push-notifications.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <!-- Left Column - Main Form -->
                <div class="col-xl-8">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Notification Details') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="title">{{ translate('messages.Notification Title') }}*</label>
                                <input type="text" class="form-control" id="title" name="title" required
                                    value="{{ old('title') }}" placeholder="{{ translate('messages.Enter a concise title') }}">
                                <small class="form-text text-muted">{{ translate('messages.Maximum 50 characters') }}</small>
                            </div>
                            <div class="form-group">
                                <label for="message">{{ translate('messages.Notification Message') }}*</label>
                                <textarea class="form-control" id="message" name="message" rows="3" required
                                    placeholder="{{ translate('messages.Enter the notification message') }}">{{ old('message') }}</textarea>
                                <small class="form-text text-muted">{{ translate('messages.Maximum 150 characters') }}</small>
                            </div>
                            <div class="form-group">
                                <label for="type">{{ translate('messages.Notification Type') }}</label>
                                <select class="form-control select2" id="type" name="type">
                                    <option value="announcement" {{ old('type') == 'announcement' ? 'selected' : '' }}>{{ translate('messages.Announcement') }}</option>
                                    <option value="price_alert" {{ old('type') == 'price_alert' ? 'selected' : '' }}>{{ translate('messages.Price Alert') }}</option>
                                    <option value="promotion" {{ old('type') == 'promotion' ? 'selected' : '' }}>{{ translate('messages.Promotion') }}</option>
                                    <option value="system" {{ old('type') == 'system' ? 'selected' : '' }}>{{ translate('messages.System Update') }}</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="link_url">{{ translate('messages.Link URL') }} ({{ translate('messages.Optional') }})</label>
                                <input type="text" class="form-control" id="link_url" name="link_url" 
                                    value="{{ old('link_url') }}" placeholder="{{ translate('messages.Enter URL or ID') }}">
                                <small class="form-text text-muted">{{ translate('messages.For markets, products, or categories, enter the ID. For URLs, enter the full URL') }}.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Target, Image & Preview -->
                <div class="col-xl-4">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Target Audience') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="zone_id">{{ translate('messages.Zone') }}</label>
                                <select class="form-control select2" id="zone_id" name="zone_id">
                                    <option value="">{{ translate('messages.All Zones') }}</option>
                                    @foreach($zones ?? [] as $zone)
                                        <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>{{ $zone->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="target_audience">{{ translate('messages.Target Audience') }}*</label>
                                <select class="form-control select2" id="target_audience" name="target_audience" required>
                                    <option value="all" {{ old('target_audience') == 'all' ? 'selected' : '' }}>{{ translate('messages.All Users') }}</option>
                                    <option value="volunteers" {{ old('target_audience') == 'volunteers' ? 'selected' : '' }}>{{ translate('messages.Volunteers') }}</option>
                                    <option value="inactive" {{ old('target_audience') == 'inactive' ? 'selected' : '' }}>{{ translate('messages.Inactive Users (30+ days)') }}</option>
                                    <option value="new" {{ old('target_audience') == 'new' ? 'selected' : '' }}>{{ translate('messages.New Users (< 7 days)') }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Image Card -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Notification Image') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="image-preview-container mb-3" onclick="document.getElementById('notificationImage').click();">
                                <div class="image-preview" id="imagePreview">
                                    <i class="fas fa-camera"></i>
                                    <span>{{ translate('messages.Click to Upload Image') }}</span>
                                </div>
                            </div>
                            <div class="custom-file">
                                <input type="file" name="image" class="custom-file-input" id="notificationImage" accept="image/*" style="display: none;">
                                <label class="custom-file-label" for="notificationImage" id="notificationImageLabel">{{ translate('messages.Choose file...') }}</label>
                            </div>
                            <small class="form-text text-muted mt-2">{{ translate('messages.Recommended: 1080x540px, Max 1MB') }}</small>
                        </div>
                    </div>

                    <!-- Notification Preview -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Preview') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="notification-preview p-3 bg-light rounded">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="mr-2">
                                        <i class="fas fa-store text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="small font-weight-bold">Bazar-dor</div>
                                        <div class="small text-muted">{{ translate('messages.Just now') }}</div>
                                    </div>
                                </div>
                                <h6 class="mb-1 font-weight-bold" id="preview-title">{{ translate('messages.Notification Title') }}</h6>
                                <p class="mb-2 small" id="preview-message">{{ translate('messages.Notification message will appear here. Write something to see the preview') }}.</p>
                                <div id="preview-image-container" style="display: none;">
                                    <img id="preview-image" src="#" alt="{{ translate('messages.Notification Image') }}" class="img-fluid rounded mb-1" style="max-height: 100px; width: 100%; object-fit: cover;">
                                </div>
                            </div>
                            
                            <div class="estimated-reach mt-3">
                                <div class="d-flex justify-content-between">
                                    <span class="small">{{ translate('messages.Estimated reach') }}:</span>
                                    <span class="small font-weight-bold" id="estimated-reach">0 {{ translate('messages.users') }}</span>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-paper-plane mr-1"></i> {{ translate('messages.Send Notification') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: function(){
                    $(this).data('placeholder');
                },
                allowClear: true
            });

            // Handle image upload
            $('#notificationImage').change(function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview').html('<img src="' + e.target.result + '" alt="Preview" style="width: 100%; height: 200px; object-fit: cover; border-radius: 0.35rem;">');
                        $('#preview-image').attr('src', e.target.result);
                        $('#preview-image-container').show();
                    }
                    reader.readAsDataURL(file);
                    $('#notificationImageLabel').text(file.name);
                }
            });

            // Live preview updates
            $('#title').keyup(function() {
                $('#preview-title').text($(this).val() || '{{ translate('messages.Notification Title') }}');
            });

            $('#message').keyup(function() {
                $('#preview-message').text($(this).val() || '{{ translate('messages.Notification message will appear here. Write something to see the preview') }}.');
            });

            // Update estimated reach based on target audience and zone
            function updateEstimatedReach() {
                const audience = $('#target_audience').val();
                const zoneId = $('#zone_id').val();
                
                $.get('{{ route('admin.push-notifications.get-estimated-reach') }}', {
                    target_audience: audience,
                    zone_id: zoneId
                })
                .done(function(response) {
                    $('#estimated-reach').text(response.text);
                })
                .fail(function() {
                    $('#estimated-reach').text('0 {{ translate('messages.users') }}');
                });
            }

            // Initialize estimated reach and update on change
            updateEstimatedReach();
            $('#target_audience').change(updateEstimatedReach);
            $('#zone_id').change(updateEstimatedReach);
        });
    </script>
@endpush
