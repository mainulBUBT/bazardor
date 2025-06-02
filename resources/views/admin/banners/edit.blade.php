@extends('layouts.admin.app')

@section('title', translate('messages.Edit Banner'))

@section('content')

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ translate('messages.Edit Banner') }}</h1>
        <a href="{{ route('admin.banners.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-outline-secondary shadow-sm"><i
                class="fas fa-arrow-left fa-sm text-gray-700"></i> {{ translate('messages.Back to Banners') }}</a>
    </div>

    <!-- Add Banner Form -->
    <form  action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data" id="addBannerForm">
        @csrf    
        @method('PUT')
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
                                <input type="text" name="title" class="form-control" id="bannerTitle" placeholder="{{ translate('messages.Enter banner title') }}" required value="{{ $banner->title }}">
                            </div>
                            <div class="col-md-4">
                                <label for="bannerPosition" class="form-label">{{ translate('messages.Position') }} <span class="text-danger">*</span></label>
                                <input type="number" name="position" class="form-control" id="bannerPosition" min="1" value="{{ $banner->position }}" required>
                                <small class="text-muted">{{ translate('messages.Order (1 is first)') }}</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="bannerLink" class="form-label">{{ translate('messages.Link URL') }}</label>
                            <input type="url" name="url" class="form-control" id="bannerLink" placeholder="{{ translate('messages.https://example.com/offer (optional)') }}" value="{{ $banner->url }}">
                            <small class="text-muted">{{ translate('messages.Leave blank if no link is needed') }}</small>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="bannerStartDate" class="form-label">{{ translate('messages.Start Date') }}</label>
                                <input type="date" name="start_date" class="form-control" id="bannerStartDate" value="{{ $banner->start_date ? \Illuminate\Support\Carbon::parse($banner->start_date)->format('Y-m-d') : '' }}">
                                <small class="text-muted">{{ translate('messages.Optional: When the banner becomes active') }}</small>
                            </div>
                            <div class="col-md-6">
                                <label for="bannerEndDate" class="form-label">{{ translate('messages.End Date') }}</label>
                                <input type="date" name="end_date" class="form-control" id="bannerEndDate" value="{{ $banner->end_date ? \Illuminate\Support\Carbon::parse($banner->end_date)->format('Y-m-d') : '' }}">
                                <small class="text-muted">{{ translate('messages.Optional: When the banner expires') }}</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="bannerDescription" class="form-label">{{ translate('messages.Description') }}</label>
                            <textarea name="description" class="form-control" id="bannerDescription" rows="3" placeholder="{{ translate('messages.Internal description (optional)') }}" value="{{ $banner->description }}"></textarea>
                        </div>
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
                                <option value="1" {{ $banner->is_active == 1 ? 'selected' : '' }}>{{ translate('messages.Active') }}</option>
                                <option value="0" {{ $banner->is_active == 0 ? 'selected' : '' }}>{{ translate('messages.Inactive') }}</option>
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
                                <input type="file" name="image" class="custom-file-input" id="bannerImage" accept="image/*" required value="{{ $banner->image }}">
                                <label class="custom-file-label" for="bannerImage" id="bannerImageLabel">{{ translate('messages.Choose file...') }}</label>
                            </div>
                            <small class="text-muted">{{ translate('messages.Recommended: 1200x400px, Max 2MB') }}</small>
                        </div>

                        <div class="image-preview-container mt-3" id="imagePreviewContainer">
                            <div class="image-preview" id="imagePreview">
                                @if ($banner->image_path)
                                    <img id="imgPreviewElem" src="{{ asset($banner->image_path) }}" alt="{{ translate('messages.Image Preview') }}" class="banner-image"/>
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
        <input type="hidden" name="type" value="general">
        <!-- Action Buttons -->
        <div class="row mb-4">
            <div class="col-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary" id="saveBannerBtn">{{ translate('messages.Update Banner') }}</button>
            </div>
        </div>
    </form>

@endsection

@push('scripts')
    <!-- Page level custom scripts -->
    <script>
        $(document).ready(function() {
            // Set initial state for image preview
            var $previewElement = $('#imgPreviewElem');
            var $previewPlaceholder = $('#imagePreview').find('i, span');
            
            // If banner has an image, hide placeholder elements
            if ($previewElement.attr('src') && $previewElement.attr('src') !== '#') {
                $previewPlaceholder.addClass('d-none');
                $previewElement.removeClass('d-none').addClass('banner-image');
            }
            
            // Image Preview Logic - handle file selection
            $('#bannerImage').on('change', function() {
                const file = this.files[0];
                const reader = new FileReader();
                const $label = $('#bannerImageLabel');
                
                if (file) {
                    reader.onload = function(e) {
                        $previewElement.attr('src', e.target.result);
                        $previewElement.removeClass('d-none').addClass('banner-image');
                        $previewPlaceholder.addClass('d-none');
                    }
                    reader.readAsDataURL(file);
                    $label.text(file.name); // Update label text
                } else {
                    $previewElement.attr('src', '#').addClass('d-none');
                    $previewPlaceholder.removeClass('d-none');
                    $label.text('{{ translate("messages.Choose file...") }}');
                }
            });
            
            // Trigger file input when preview container is clicked
            $('#imagePreviewContainer').on('click', function() {
                $('#bannerImage').click();
            });
        });
    </script>
@endpush
