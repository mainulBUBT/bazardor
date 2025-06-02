@extends('layouts.admin.app')

@section('title', 'Edit Banner')

@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Banner</h1>
        <a href="{{ route('admin.banners.index') }}" class="btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> Back to Banners
        </a>
    </div>

    <!-- Banner Edit Form -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $banner->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="url">URL (Optional)</label>
                            <input type="url" class="form-control @error('url') is-invalid @enderror" 
                                   id="url" name="url" value="{{ old('url', $banner->url) }}">
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="target">Target</label>
                            <select class="form-control" id="target" name="target">
                                <option value="_self" {{ old('target', $banner->target) === '_self' ? 'selected' : '' }}>
                                    Same Window
                                </option>
                                <option value="_blank" {{ old('target', $banner->target) === '_blank' ? 'selected' : '' }}>
                                    New Window
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="position">Position</label>
                            <input type="number" class="form-control @error('position') is-invalid @enderror" 
                                   id="position" name="position" value="{{ old('position', $banner->position) }}" required>
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Banner Image</label>
                            <div class="image-preview-container" id="imagePreviewContainer">
                                <input type="file" class="d-none" id="bannerImage" name="image" 
                                       accept="image/*" {{ $banner->image_path ? '' : 'required' }}>
                                <div class="image-preview text-center">
                                    @if($banner->image_path)
                                        <img src="{{ asset($banner->image_path) }}" alt="Current banner" 
                                             id="imgPreviewElem" class="img-fluid mb-2">
                                    @else
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    @endif
                                    <div>Click to choose an image</div>
                                </div>
                            </div>
                            @error('image')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">Description (Optional)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $banner->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Start Date (Optional)</label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" 
                                           id="start_date" name="start_date" 
                                           value="{{ old('start_date', optional($banner->start_date)->format('Y-m-d')) }}">
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">End Date (Optional)</label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" 
                                           id="end_date" name="end_date" 
                                           value="{{ old('end_date', optional($banner->end_date)->format('Y-m-d')) }}">
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" 
                                       name="is_active" {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active</label>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update Banner</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Image Preview Logic
        $('#bannerImage').on('change', function() {
            const file = this.files[0];
            const reader = new FileReader();
            const $previewElement = $('#imgPreviewElem');
            const $previewContainer = $('.image-preview');

            if (file) {
                reader.onload = function(e) {
                    if ($previewElement.length) {
                        $previewElement.attr('src', e.target.result);
                    } else {
                        $previewContainer.html(`
                            <img src="${e.target.result}" alt="Banner preview" id="imgPreviewElem" 
                                 class="img-fluid mb-2">
                        `);
                    }
                }
                reader.readAsDataURL(file);
            }
        });

        // Trigger file input when preview container is clicked
        $('#imagePreviewContainer').on('click', function() {
            $('#bannerImage').click();
        });
    });
</script>
@endpush
