@extends('layouts.admin.app')
@section('title', translate('messages.Edit Category'))
@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ translate('messages.Edit Category') }}: {{ $category->name }}</h1>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ translate('messages.Back to Categories') }}
    </a>
</div>

<!-- Edit Category Form -->
<form id="editCategoryForm" action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-lg-8">
            <!-- Category Information Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Category Information') }}</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="categoryName">{{ translate('messages.Name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categoryName" name="name" placeholder="{{ translate('messages.Enter category name') }}" value="{{ old('name', $category->name) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="categorySlug">{{ translate('messages.Slug') }}</label>
                        <input type="text" class="form-control" id="categorySlug" name="slug" placeholder="{{ translate('messages.auto-generated-from-name') }}" value="{{ old('slug', $category->slug) }}">
                        <small class="form-text text-muted">{{ translate('messages.Leave empty to auto-generate from name, or edit manually.') }}</small>
                    </div>
                    @if($category->parent_id)
                        <div class="form-group">
                            <label for="parentCategory">{{ translate('messages.Parent Category') }}</label>
                            <select class="form-control" id="parentCategory" name="parent_id">
                                <option value="">{{ translate('messages.None (Main Category)') }}</option>
                                @foreach($parentCategories as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Status') }}</h6>
                </div>
                <div class="card-body">
                    <div class="form-group mb-0">
                        <input type="hidden" name="is_active" value="0">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="categoryStatusSwitch" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="categoryStatusSwitch">{{ $category->is_active ? translate('messages.Active') : translate('messages.Inactive') }}</label>
                        </div>
                        <small class="form-text text-muted">{{ translate('messages.Inactive categories will not be visible.') }}</small>
                    </div>
                </div>
            </div>

            <!-- Image Upload Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Category Image') }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="categoryImage" class="form-label">{{ translate('messages.Upload Image') }}</label>
                        <div class="custom-file">
                            <input type="file" name="image" class="custom-file-input" id="categoryImage" accept="image/*">
                            <label class="custom-file-label" for="categoryImage" id="categoryImageLabel">{{ translate('messages.Choose file...') }}</label>
                        </div>
                        <small class="text-muted">{{ translate('messages.Recommended: 1200x400px, Max 2MB. Leave empty to keep current image.') }}</small>
                    </div>

                    <div class="image-preview-container mt-3" id="imagePreviewContainer" data-has-existing-image="{{ !empty($category->image_path) ? 'true' : 'false' }}">
                        <div class="image-preview" id="imagePreview">
                            @if($category->image_path)
                                <img id="imgPreviewElem" src="{{ asset('public/storage/categories/' . $category->image_path) }}" alt="{{ translate('messages.Current Image') }}" style="max-width: 100%; height: auto; display: block;"/>
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
    <div class="row">
        <div class="col-lg-12 d-flex justify-content-end mb-4">
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary mr-2">
                <i class="fas fa-times"></i> {{ translate('messages.Cancel') }}
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ translate('messages.Update Category') }}
            </button>
        </div>
    </div>
</form>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize Select2 for Parent Category
        $('#parentCategory').select2({
            placeholder: '{{ translate("messages.Select a parent category") }}',
            allowClear: true
        });

        // Status Switch Label Update
        $('#categoryStatusSwitch').on('change', function() {
            const label = $(this).next('.custom-control-label');
            if ($(this).is(':checked')) {
                label.text('{{ translate("messages.Active") }}');
            } else {
                label.text('{{ translate("messages.Inactive") }}');
            }
        });

        // Store original image URL and flag in JS
        const originalImage = "{{ $category->image_path ? asset('storage/' . $category->image_path) : '' }}";
        const hasExistingImage = "{{ $category->image_path ? 'true' : 'false' }}";

        $('#categoryImage').on('change', function() {
            const file = this.files[0];
            const reader = new FileReader();
            const $previewElement = $('#imgPreviewElem');
            const $previewPlaceholder = $('#imagePreview').find('i, span');
            const $label = $('#categoryImageLabel');

            if (file) {
                reader.onload = function(e) {
                    $previewElement.attr('src', e.target.result).removeClass('d-none');
                    $previewPlaceholder.addClass('d-none');
                }
                reader.readAsDataURL(file);
                $label.text(file.name);
            } else {
                if (hasExistingImage === 'true' && originalImage) {
                    $previewElement.attr('src', originalImage).removeClass('d-none');
                    $previewPlaceholder.addClass('d-none');
                } else {
                    $previewElement.attr('src', '#').addClass('d-none');
                    $previewPlaceholder.removeClass('d-none');
                }
                $label.text('Choose file...');
            }
        });

        // Trigger file input when preview container is clicked
        $('#imagePreviewContainer').on('click', function() {
            $('#categoryImage').click();
        });
    });
</script>
@endpush
