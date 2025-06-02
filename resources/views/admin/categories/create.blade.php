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
                            <!-- Parent categories will be populated here -->
                        </select>
                    </div>
                </div>
            </div>

            <!-- Category Icon Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Category Icon') }}</h6>
                </div>
                <div class="card-body">
                    <label class="form-label mb-2">{{ translate('messages.Select an icon for this category:') }}</label>
                    <div class="icon-selection">
                        <!-- Font Awesome Icons -->
                        <div class="icon-option active" data-icon="fas fa-tag"><i class="fas fa-tag"></i></div>
                        <div class="icon-option" data-icon="fas fa-carrot"><i class="fas fa-carrot"></i></div>
                        <div class="icon-option" data-icon="fas fa-lemon"><i class="fas fa-lemon"></i></div>
                        <div class="icon-option" data-icon="fas fa-seedling"><i class="fas fa-seedling"></i></div>
                        <div class="icon-option" data-icon="fas fa-pepper-hot"><i class="fas fa-pepper-hot"></i></div>
                        <div class="icon-option" data-icon="fas fa-cheese"><i class="fas fa-cheese"></i></div>
                        <div class="icon-option" data-icon="fas fa-bread-slice"><i class="fas fa-bread-slice"></i></div>
                        <div class="icon-option" data-icon="fas fa-drumstick-bite"><i class="fas fa-drumstick-bite"></i></div>
                        <div class="icon-option" data-icon="fas fa-fish"><i class="fas fa-fish"></i></div>
                        <div class="icon-option" data-icon="fas fa-wine-bottle"><i class="fas fa-wine-bottle"></i></div>
                        <div class="icon-option" data-icon="fas fa-cookie-bite"><i class="fas fa-cookie-bite"></i></div>
                        <div class="icon-option" data-icon="fas fa-box"><i class="fas fa-box"></i></div>
                        <div class="icon-option" data-icon="fas fa-shopping-basket"><i class="fas fa-shopping-basket"></i></div>
                        <div class="icon-option" data-icon="fas fa-store"><i class="fas fa-store"></i></div>
                    </div>
                    <input type="hidden" id="selectedIcon" name="icon" value="fas fa-tag">
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
                    <div class="form-group mb-0">
                        <label for="categoryImage">{{ translate('messages.Upload Image') }} ({{ translate('messages.Optional') }})</label>
                        <input type="file" class="form-control-file" id="categoryImage" name="image">
                        <small class="form-text text-muted">{{ translate('messages.Recommended size: 200x200px') }}</small>
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
<style>
    .icon-selection {
        margin-bottom: 1rem;
    }
    
    .icon-option {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        background-color: #f8f9fc;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        margin: 4px;
    }
    
    .icon-option i {
        font-size: 1.5rem;
        color: #4e73df; /* Primary color */
    }
    
    .icon-option:hover {
        background-color: #eaecf4;
    }
    
    .icon-option.active {
        background-color: #4e73df;
        border-color: #2e59d9;
    }
    
    .icon-option.active i {
        color: #fff;
    }
</style>

<script>
    $(document).ready(function() {
        // Initialize Select2 for Parent Category
        $('#parentCategory').select2({
            placeholder: "{{ translate('messages.Select a parent category') }}",
            allowClear: true
        });

        // Generate Slug from Name
        const categoryName = document.getElementById('categoryName');
        const categorySlug = document.getElementById('categorySlug');

        function generateSlug(text) {
            return text.toString().toLowerCase()
                .replace(/\s+/g, '-')           // Replace spaces with -
                .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                .replace(/^-+/, '')             // Trim - from start of text
                .replace(/-+$/, '');            // Trim - from end of text
        }

        categoryName.addEventListener('input', function() {
            // Only auto-update slug if the slug field is empty or matches the generated slug
            const currentSlug = categorySlug.value.trim();
            const generatedSlug = generateSlug(this.value.trim());
            if (!currentSlug || currentSlug === generateSlug(categoryName.value.trim().slice(0, -1))) {
                categorySlug.value = generatedSlug;
            }
        });
        
        // Status Switch Label Update
        $('#categoryStatusSwitch').on('change', function() {
            const label = $(this).next('.custom-control-label');
            if ($(this).is(':checked')) {
                label.text('{{ translate('messages.Active') }}');
            } else {
                label.text('{{ translate('messages.Inactive') }}');
            }
        });

        // Handle icon selection
        const iconOptions = document.querySelectorAll('.icon-option');
        const selectedIconInput = document.getElementById('selectedIcon');

        iconOptions.forEach(option => {
            option.addEventListener('click', function() {
                // Remove active class from all options
                iconOptions.forEach(opt => opt.classList.remove('active'));
                // Add active class to clicked option
                this.classList.add('active');
                // Get the icon class from data attribute
                const iconClass = this.dataset.icon;
                // Update the hidden input
                selectedIconInput.value = iconClass;
            });
        });
    });
</script>
@endpush
