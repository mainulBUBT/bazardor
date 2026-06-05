@extends('layouts.admin.app')

@section('title', translate('messages.Edit Banner'))

@section('content')
@php
    $locales = get_enabled_locales();
    $languages = get_enabled_languages();
    $defaultLocale = get_default_locale();
@endphp

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ translate('messages.Edit Banner') }}</h1>
        <a href="{{ route('admin.banners.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-outline-secondary shadow-sm"><i
                class="fas fa-arrow-left fa-sm text-gray-700"></i> {{ translate('messages.Back to Banners') }}</a>
    </div>

    <!-- Edit Banner Form -->
    <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data" id="editBannerForm">
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
                        <div class="mb-3">
                            @if(count($locales) > 1)
                            <ul class="nav nav-tabs mb-3" role="tablist">
                                @foreach($languages as $lang)
                                <li class="nav-item">
                                    <a class="nav-link {{ $loop->first ? 'active' : '' }}"
                                       data-toggle="tab" href="#lang-{{ $lang['code'] }}" role="tab">
                                        {{ strtoupper($lang['code']) }}
                                        <small class="text-muted">{{ $lang['code'] === $defaultLocale ? '(Default)' : $lang['name'] }}</small>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                            <div class="tab-content">
                                @foreach($languages as $lang)
                                @php
                                    $locale = $lang['code'];
                                    $isDefault = $locale === $defaultLocale;
                                    $fieldTitle = $isDefault ? 'title' : "title_{$locale}";
                                    $translation = $banner->getTranslation($locale, false);
                                @endphp
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="lang-{{ $locale }}" role="tabpanel">
                                    <div class="form-group">
                                        <label>{{ translate('messages.Banner Title') }} ({{ $lang['name'] }}) @if($isDefault) <span class="text-danger">*</span> @endif</label>
                                        <input type="text" name="{{ $fieldTitle }}" class="form-control"
                                               {{ $isDefault ? 'required' : '' }}
                                               value="{{ old($fieldTitle, $translation?->title) }}" placeholder="{{ translate('messages.Enter banner title') }}">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="form-group">
                                <label for="bannerTitle" class="form-label">{{ translate('messages.Banner Title') }} <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control" id="bannerTitle" value="{{ old('title', $banner->title) }}" placeholder="{{ translate('messages.Enter banner title') }}" required>
                            </div>
                            @endif
                        </div>

                        @php($selectedZoneIds = old('zone_ids', $selectedZoneIds ?? []))
                        @php($isAllZones = count($selectedZoneIds) === count($zones ?? []))
                        <div class="mb-3">
                            <select name="zone_ids[]" id="bannerZone" class="form-control select2" style="width: 100%;" multiple>
                                <option value="all" {{ ($isAllZones || in_array('all', old('zone_ids', []))) ? 'selected' : '' }}>{{ translate('messages.All Zones') }}</option>
                                @foreach(($zones ?? []) as $zoneItem)
                                    <option value="{{ $zoneItem->id }}" {{ (in_array($zoneItem->id, $selectedZoneIds) && !$isAllZones) ? 'selected' : '' }}>{{ $zoneItem->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">{{ translate('messages.Select "All Zones" or specific zones') }}</small>
                        </div>

                        <div class="mb-3">
                            <label for="bannerLink" class="form-label">{{ translate('messages.Link URL') }}</label>
                            <input type="url" name="link" class="form-control" id="bannerLink" value="{{ old('link', $banner->link) }}" placeholder="{{ translate('messages.https://example.com/offer (optional)') }}">
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

                        <div class="mb-3">
                            <label class="form-label">{{ translate('messages.Featured') }}</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_featured" value="1" class="custom-control-input" id="bannerFeatured" {{ old('is_featured', $banner->is_featured) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="bannerFeatured">{{ translate('messages.Mark as featured') }}</label>
                            </div>
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
                            <label for="bannerImage" class="form-label">{{ translate('messages.Upload Image') }}</label>
                            <div class="custom-file">
                                <input type="file" name="image" class="custom-file-input" id="bannerImage" accept="image/*">
                                <label class="custom-file-label" for="bannerImage" id="bannerImageLabel">{{ translate('messages.Choose file...') }}</label>
                            </div>
                            <small class="text-muted">{{ translate('messages.Recommended: 1200x400px, Max 2MB') }}</small>
                        </div>
                        <div class="image-preview-container mt-3" id="imagePreviewContainer">
                            <div class="image-preview" id="imagePreview">
                                <i class="fas fa-image {{ $banner->image_path ? 'd-none' : '' }}" id="imgPlaceholderIcon"></i>
                                <span class="{{ $banner->image_path ? 'd-none' : '' }}" id="imgPlaceholderText">{{ translate('messages.Click to Upload or Preview') }}</span>
                                @if($banner->image_path)
                                    <img id="imgPreviewElem" src="{{ $banner->image_full_url }}" alt="{{ translate('messages.Image Preview') }}" style="max-width:100%; max-height:200px; border-radius:.2rem; margin-top:1rem;"/>
                                @else
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
            // Image Preview Logic
            $('#bannerImage').on('change', function() {
                const file = this.files[0];
                const reader = new FileReader();
                const $previewElement = $('#imgPreviewElem');
                const $placeholderIcon = $('#imgPlaceholderIcon');
                const $placeholderText = $('#imgPlaceholderText');
                const $label = $('#bannerImageLabel');

                if (file) {
                    reader.onload = function(e) {
                        $previewElement.attr('src', e.target.result).removeClass('d-none');
                        $placeholderIcon.addClass('d-none');
                        $placeholderText.addClass('d-none');
                    }
                    reader.readAsDataURL(file);
                    $label.text(file.name);
                } else {
                    $previewElement.attr('src', '#').addClass('d-none');
                    $placeholderIcon.removeClass('d-none');
                    $placeholderText.removeClass('d-none');
                    $label.text('Choose file...');
                }
            });

            // Trigger file input when preview container is clicked
            $('#imagePreviewContainer').on('click', function() {
                $('#bannerImage').click();
            });

            $('#bannerZone').select2({
                placeholder: "{{ translate('messages.Select zones') }}",
                allowClear: true,
                width: 'resolve'
            });

            // When "All Zones" is selected, deselect others; when a specific zone is picked, deselect "All"
            $('#bannerZone').on('change', function() {
                var vals = $(this).val() || [];
                if (vals.includes('all')) {
                    if (vals.length > 1) {
                        var last = vals[vals.length - 1];
                        if (last === 'all') {
                            $(this).val(['all']).trigger('change.select2');
                        } else {
                            $(this).val(vals.filter(function(v) { return v !== 'all'; })).trigger('change.select2');
                        }
                    }
                }
            });
        });
    </script>
@endpush
