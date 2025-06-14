@extends('layouts.admin.app')

@section('title', translate('messages.Edit Banner'))

@section('content')

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ translate('messages.Edit Banner') }}</h1>
        <a href="{{ route('admin.banners.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-outline-secondary shadow-sm"><i
                class="fas fa-arrow-left fa-sm text-gray-700"></i> {{ translate('messages.Back to Banners') }}</a>
    </div>

    <!-- Banner Type Toggle -->
    <div class="mb-4">
        <label class="font-weight-bold mr-3">{{ translate('messages.Banner Type') }}:</label>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="typeBanner" name="banner_type" class="custom-control-input" value="general" {{ $banner->type === 'general' ? 'checked' : '' }}>
            <label class="custom-control-label" for="typeBanner">{{ translate('messages.Banner') }}</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input type="radio" id="typeFeatured" name="banner_type" class="custom-control-input" value="featured" {{ $banner->type === 'featured' ? 'checked' : '' }}>
            <label class="custom-control-label" for="typeFeatured">{{ translate('messages.Featured Banner') }}</label>
        </div>
    </div>

    <!-- Edit Banner Form -->
    <form  action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data" id="editBannerForm">
        @csrf    
        @method('PUT')
        <input type="hidden" name="type" id="bannerTypeInput" value="{{ $banner->type }}">
        <div class="row">
            <!-- Left Column: Banner Details -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Banner Information') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="bannerTitle" class="form-label">{{ translate('messages.Banner Title') }} <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" id="bannerTitle" value="{{ old('title', $banner->title) }}" placeholder="{{ translate('messages.Enter banner title') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="bannerPosition" class="form-label">{{ translate('messages.Position') }} <span class="text-danger">*</span></label>
                                <input type="number" name="position" class="form-control" id="bannerPosition" min="1" value="{{ old('position', $banner->position) }}" required>
                                <small class="text-muted">{{ translate('messages.Order (1 is first)') }}</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="bannerLink" class="form-label">{{ translate('messages.Link URL') }}</label>
                            <input type="url" name="url" class="form-control" id="bannerLink" value="{{ old('url', $banner->url) }}" placeholder="{{ translate('messages.https://example.com/offer (optional)') }}">
                            <small class="text-muted">{{ translate('messages.Leave blank if no link is needed') }}</small>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="bannerStartDate" class="form-label">{{ translate('messages.Start Date') }}</label>
                                <input type="date" name="start_date" class="form-control" id="bannerStartDate" value="{{ old('start_date', $banner->start_date ? $banner->start_date->format('Y-m-d') : '') }}">
                                <small class="text-muted">{{ translate('messages.Optional: When the banner becomes active') }}</small>
                            </div>
                            <div class="col-md-6">
                                <label for="bannerEndDate" class="form-label">{{ translate('messages.End Date') }}</label>
                                <input type="date" name="end_date" class="form-control" id="bannerEndDate" value="{{ old('end_date', $banner->end_date ? $banner->end_date->format('Y-m-d') : '') }}">
                                <small class="text-muted">{{ translate('messages.Optional: When the banner expires') }}</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="bannerDescription" class="form-label">{{ translate('messages.Description') }}</label>
                            <textarea name="description" class="form-control" id="bannerDescription" rows="3" placeholder="{{ translate('messages.Internal description (optional)') }}">{{ old('description', $banner->description) }}</textarea>
                        </div>

                        <!-- Featured Banner Fields -->
                        <div id="featuredFields" style="display:{{ $banner->type === 'featured' ? 'block' : 'none' }};">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label for="bannerBadgeText" class="form-label">{{ translate('messages.Badge Text') }}</label>
                                    <input type="text" name="badge_text" class="form-control" id="bannerBadgeText" value="{{ old('badge_text', $banner->badge_text) }}" placeholder="e.g., New, Hot, Special">
                                </div>
                                <div class="col-md-4">
                                    <label for="bannerBadgeColor" class="form-label">{{ translate('messages.Badge Color') }}</label>
                                    <select name="badge_color" class="form-control" id="bannerBadgeColor">
                                        <option value="primary" {{ old('badge_color', $banner->badge_color) == 'primary' ? 'selected' : '' }}>Primary (Blue)</option>
                                        <option value="secondary" {{ old('badge_color', $banner->badge_color) == 'secondary' ? 'selected' : '' }}>Secondary (Gray)</option>
                                        <option value="success" {{ old('badge_color', $banner->badge_color) == 'success' ? 'selected' : '' }}>Success (Green)</option>
                                        <option value="danger" {{ old('badge_color', $banner->badge_color) == 'danger' ? 'selected' : '' }}>Danger (Red)</option>
                                        <option value="warning" {{ old('badge_color', $banner->badge_color) == 'warning' ? 'selected' : '' }}>Warning (Yellow)</option>
                                        <option value="info" {{ old('badge_color', $banner->badge_color) == 'info' ? 'selected' : '' }}>Info (Teal)</option>
                                        <option value="light" {{ old('badge_color', $banner->badge_color) == 'light' ? 'selected' : '' }}>Light</option>
                                        <option value="dark" {{ old('badge_color', $banner->badge_color) == 'dark' ? 'selected' : '' }}>Dark</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="bannerIcon" class="form-label">{{ translate('messages.Icon (Bootstrap Icons class)') }}</label>
                                    <input type="text" name="badge_icon" class="form-control" id="bannerIcon" value="{{ old('badge_icon', $banner->badge_icon) }}" placeholder="e.g., bi-basket2-fill">
                                    <small class="form-text text-muted">Find icons at <a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons</a>.</small>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="bannerBackground" class="form-label">{{ translate('messages.Background Style') }}</label>
                                    <select name="badge_background_color" class="form-control" id="bannerBackground">
                                        <option value="promo-banner-blue" {{ old('badge_background_color', $banner->badge_background_color) == 'promo-banner-blue' ? 'selected' : '' }}>Blue</option>
                                        <option value="promo-banner-pink" {{ old('badge_background_color', $banner->badge_background_color) == 'promo-banner-pink' ? 'selected' : '' }}>Pink</option>
                                        <option value="promo-banner-purple" {{ old('badge_background_color', $banner->badge_background_color) == 'promo-banner-purple' ? 'selected' : '' }}>Purple</option>
                                        <option value="promo-banner-teal" {{ old('badge_background_color', $banner->badge_background_color) == 'promo-banner-teal' ? 'selected' : '' }}>Teal</option>
                                        <option value="promo-banner-green" {{ old('badge_background_color', $banner->badge_background_color) == 'promo-banner-green' ? 'selected' : '' }}>Green</option>
                                        <option value="promo-banner-orange" {{ old('badge_background_color', $banner->badge_background_color) == 'promo-banner-orange' ? 'selected' : '' }}>Orange</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="bannerButtonText" class="form-label">{{ translate('messages.Button Text') }}</label>
                                    <input type="text" name="button_text" class="form-control" id="bannerButtonText" value="{{ old('button_text', $banner->button_text) }}" placeholder="e.g., Shop Now">
                                </div>
                            </div>
                        </div>
                        <!-- End Featured Banner Fields -->
                    </div>
                </div>
            </div>

            <!-- Right Column: Image & Status -->
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Image & Status') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="bannerStatus" class="form-label">{{ translate('messages.Status') }} <span class="text-danger">*</span></label>
                            <select name="is_active" class="form-control" id="bannerStatus" required>
                                <option value="1" {{ old('is_active', $banner->is_active) == 1 ? 'selected' : '' }}>{{ translate('messages.Active') }}</option>
                                <option value="0" {{ old('is_active', $banner->is_active) == 0 ? 'selected' : '' }}>{{ translate('messages.Inactive') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Image Upload Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Banner Image') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="bannerImage" class="form-label">{{ translate('messages.Upload Image') }} <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" name="image" class="custom-file-input" id="bannerImage" accept="image/*">
                                <label class="custom-file-label" for="bannerImage" id="bannerImageLabel">{{ translate('messages.Choose file...') }}</label>
                            </div>
                            <small class="text-muted">{{ translate('messages.Recommended: 1200x400px, Max 2MB') }}</small>
                        </div>
                        <div class="image-preview-container mt-3" id="imagePreviewContainer">
                            <div class="image-preview" id="imagePreview">
                                @if($banner->image_path)
                                    <img id="imgPreviewElem" src="{{ asset($banner->image_path) }}" alt="{{ translate('messages.Image Preview') }}" style="max-width:100%; max-height:200px; border-radius:.2rem; margin-top:1rem;"/>
                                @else
                                    <i class="fas fa-image"></i>
                                    <span>{{ translate('messages.Click to Upload or Preview') }}</span>
                                    <img id="imgPreviewElem" src="#" alt="{{ translate('messages.Image Preview') }}" class="d-none"/>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Action Buttons -->
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-end">
                <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary mr-2">{{ translate('messages.Cancel') }}</a>
                <button type="submit" class="btn btn-primary" id="saveBannerBtn">{{ translate('messages.Update Banner') }}</button>
            </div>
        </div>
    </form>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Banner type toggle logic
            $('input[name="banner_type"]').on('change', function() {
                var type = $(this).val();
                $('#bannerTypeInput').val(type);
                if (type === 'featured') {
                    $('#featuredFields').slideDown();
                } else {
                    $('#featuredFields').slideUp();
                }
            });

            // Image Preview Logic
            $('#bannerImage').on('change', function() {
                const file = this.files[0];
                const reader = new FileReader();
                const $previewElement = $('#imgPreviewElem');
                const $previewPlaceholder = $('#imagePreview').find('i, span');
                const $label = $('#bannerImageLabel');

                if (file) {
                    reader.onload = function(e) {
                        $previewElement.attr('src', e.target.result).removeClass('d-none');
                        $previewPlaceholder.addClass('d-none');
                    }
                    reader.readAsDataURL(file);
                    $label.text(file.name); // Update label text
                } else {
                    $previewElement.attr('src', '#').addClass('d-none');
                    $previewPlaceholder.removeClass('d-none');
                    $label.text('Choose file...');
                }
            });

            // Trigger file input when preview container is clicked
            $('#imagePreviewContainer').on('click', function() {
                $('#bannerImage').click();
            });
        });
    </script>
@endpush
