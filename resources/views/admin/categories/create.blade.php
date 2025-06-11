@extends('layouts.admin.app')
@section('title', translate('messages.Add Category'))
@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ translate('messages.Add New Category') }}</h1>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ translate('messages.Back to Categories') }}
    </a>
</div>

<!-- Add Category Form -->
<form id="addCategoryForm" action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
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
                        <input type="text" class="form-control" id="categoryName" name="name" placeholder="{{ translate('messages.Enter category name') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="categorySlug">{{ translate('messages.Slug') }}</label>
                        <input type="text" class="form-control" id="categorySlug" name="slug" placeholder="{{ translate('messages.auto-generated-from-name') }}">
                        <small class="form-text text-muted">{{ translate('messages.Leave empty to auto-generate from name.') }}</small>
                    </div>
                    <div class="form-group">
                        <label for="parentCategory">{{ translate('messages.Parent Category') }}</label>
                        <select class="form-control" id="parentCategory" name="parent_id">
                            <option value="" selected>{{ translate('messages.None (Main Category)') }}</option>
                                @foreach($parentCategories as $parent)
                                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                                @endforeach
                        </select>
                    </div>
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
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="categoryStatusSwitch" name="status" checked>
                            <label class="custom-control-label" for="categoryStatusSwitch">{{ translate('messages.Active') }}</label>
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
                            <label for="categoryImage" class="form-label">{{ translate('messages.Upload Image') }} <span class="text-danger">*</span></label>
                            <div class="custom-file">
                                <input type="file" name="image" class="custom-file-input" id="categoryImage" accept="image/*" required>
                                <label class="custom-file-label" for="categoryImage" id="categoryImageLabel">{{ translate('messages.Choose file...') }}</label>
                            </div>
                            <small class="text-muted">{{ translate('messages.Recommended: 1200x400px, Max 2MB') }}</small>
                        </div>

                        <div class="image-preview-container mt-3" id="imagePreviewContainer">
                            <div class="image-preview" id="imagePreview">
                                <i class="fas fa-image"></i>
                                <span>{{ translate('messages.Click to Upload or Preview') }}</span>
                                <img id="imgPreviewElem" src="#" alt="{{ translate('messages.Image Preview') }}" class="d-none"/>
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
                <i class="fas fa-save"></i> {{ translate('messages.Save Category') }}
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
            placeholder: '{{ translate("messages.Select a parent category") }}', // Adjusted quotes
            allowClear: true
        });
        
        // Status Switch Label Update
        $('#categoryStatusSwitch').on('change', function() {
            const label = $(this).next('.custom-control-label');
            if ($(this).is(':checked')) {
                label.text('{{ translate("messages.Active") }}'); // Adjusted quotes
            } else {
                label.text('{{ translate("messages.Inactive") }}'); // Adjusted quotes
            }
        });

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
                    $label.text(file.name); // Update label text
                } else {
                    $previewElement.attr('src', '#').addClass('d-none');
                    $previewPlaceholder.removeClass('d-none');
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
