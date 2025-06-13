@extends('layouts.admin.app')
@section('title', translate('messages.Edit Market'))
@section('content')

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ translate('messages.Edit Market') }}: {{ $market->name }}</h1>
        <div class="ml-auto">
            <a href="{{ route('admin.markets.index') }}" class="btn btn-sm btn-outline-secondary shadow-sm mr-2">
                <i class="fas fa-arrow-left fa-sm"></i> {{ translate('messages.Back to Markets') }}
            </a>
        </div>
    </div>

    <form action="{{ route('admin.markets.update', $market->id) }}" method="POST" enctype="multipart/form-data" id="editMarketForm">
        @csrf
        @method('PUT')
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="row">
            <!-- Main Market Details Column -->
            <div class="col-lg-8">
                <!-- Market Information Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Basic Information') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <label for="marketName" class="form-label">{{ translate('messages.Market Name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" id="marketName" value="{{ old('name', $market->name) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="marketSlug" class="form-label">{{ translate('messages.Slug') }}</label>
                                <input type="text" name="slug" class="form-control" id="marketSlug" value="{{ old('slug', $market->slug) }}">
                                <small class="form-text text-muted">{{ translate('messages.URL-friendly identifier.') }}</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="marketType" class="form-label">{{ translate('messages.Market Type') }} <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="marketType" name="type" required>
                                @foreach(\App\Enums\MarketType::cases() as $type)
                                    <option value="{{ $type->value }}" {{ old('type', $market->type) == $type->value ? 'selected' : '' }}>{{ $type->value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="marketDescription" class="form-label">{{ translate('messages.Short Description') }}</label>
                            <textarea name="description" class="form-control" id="marketDescription" rows="3">{{ old('description', $market->description) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Location Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Location Details') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="marketAddress" class="form-label">{{ translate('messages.Full Address') }} <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control" id="marketAddress" rows="2" required>{{ old('address', $market->address) }}</textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="marketDivision" class="form-label">{{ translate('messages.Division') }} <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="marketDivision" name="division" onchange="getDistricts(this.value)" required>
                                    <option></option>
                                    @foreach($divisions as $division)
                                        <option value="{{ $division }}" {{ old('division', $market->location->division ?? '') == $division ? 'selected' : '' }}>{{ $division }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="marketDistrict" class="form-label">{{ translate('messages.District') }} <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="marketDistrict" name="district" onchange="getThanas(document.getElementById('marketDivision').value, this.value)" required>
                                    <option></option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="marketUpazila" class="form-label">{{ translate('messages.Upazila/Thana') }}</label>
                                <select class="form-control select2" id="marketUpazila" name="upazila">
                                    <option></option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="marketLatitude" class="form-label">{{ translate('messages.Latitude') }}</label>
                                <input type="text" name="latitude" class="form-control" id="marketLatitude" value="{{ old('latitude', $market->latitude) }}">
                            </div>
                            <div class="col-md-6">
                                <label for="marketLongitude" class="form-label">{{ translate('messages.Longitude') }}</label>
                                <input type="text" name="longitude" class="form-control" id="marketLongitude" value="{{ old('longitude', $market->longitude) }}">
                            </div>
                        </div>
                        <div id="map" style="height: 400px; border-radius: 0.35rem;" class="mb-3"></div>
                    </div>
                </div>

                <!-- Operating Hours Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Operating Hours') }}</h6>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="applyHoursToAll">{{ translate('messages.Apply First Row to All') }}</button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm" id="operatingHoursTable" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th style="width: 20%;">{{ translate('messages.Day') }}</th>
                                        <th style="width: 30%;">{{ translate('messages.Opening Time') }}</th>
                                        <th style="width: 30%;">{{ translate('messages.Closing Time') }}</th>
                                        <th style="width: 10%; text-align: center;">{{ translate('messages.Closed') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']; @endphp
                                    @foreach($days as $day)
                                    @php
                                        $day_key = strtolower($day);
                                        $hours = $market->opening_hours[$day] ?? null;
                                        $opening_time = old("opening_hours.{$day}.opening_time", $hours['opening_time'] ?? '08:00');
                                        $closing_time = old("opening_hours.{$day}.closing_time", $hours['closing_time'] ?? '20:00');
                                        $is_closed = old("opening_hours.{$day}.is_closed", $hours['is_closed'] ?? false);
                                    @endphp
                                    <tr data-day="{{ $day }}">
                                        <td>{{ translate('messages.' . $day) }}</td>
                                        <td><input type="time" name="opening_hours[{{$day}}][opening_time]" class="form-control form-control-sm opening-time" value="{{ $opening_time }}"></td>
                                        <td><input type="time" name="opening_hours[{{$day}}][closing_time]" class="form-control form-control-sm closing-time" value="{{ $closing_time }}"></td>
                                        <td class="text-center"><div class="custom-control custom-checkbox"><input type="checkbox" name="opening_hours[{{$day}}][is_closed]" class="custom-control-input is-closed" id="closed{{$day}}" {{ $is_closed ? 'checked' : '' }}><label class="custom-control-label" for="closed{{$day}}"></label></div></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Column -->
            <div class="col-lg-4">
                <!-- Settings Card (Copied from create) -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Settings') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="marketStatus">{{ translate('messages.Status') }}</label>
                            <select name="is_active" id="marketStatus" class="form-control">
                                <option value="1" {{ old('is_active', $market->is_active) == 1 ? 'selected' : '' }}>{{ translate('messages.Active') }}</option>
                                <option value="0" {{ old('is_active', $market->is_active) == 0 ? 'selected' : '' }}>{{ translate('messages.Inactive') }}</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="marketVisibility">{{ translate('messages.Visibility') }}</label>
                            <select name="visibility" id="marketVisibility" class="form-control">
                                <option value="public" {{ old('visibility', $market->visibility) == 'public' ? 'selected' : '' }}>{{ translate('messages.Public') }}</option>
                                <option value="private" {{ old('visibility', $market->visibility) == 'private' ? 'selected' : '' }}>{{ translate('messages.Private') }}</option>
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="is_featured" value="1" class="custom-control-input" id="featuredSwitch" {{ old('is_featured', $market->is_featured) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="featuredSwitch">{{ translate('messages.Featured Market') }}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Image Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Market Image') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="marketImage" class="d-block">{{ translate('messages.Upload Image') }}</label>
                            <div class="image-upload-wrap" style="position: relative; width: 100%; min-height: 180px; border: 2px dashed #e3e6f0; border-radius: 0.35rem; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                                <input type="file" name="image" id="marketImage" class="d-none" accept="image/*">
                                <div id="imagePreview" class="text-center w-100">
                                    @if($market->image_path)
                                        <img src="{{ asset('storage/' . $market->image_path) }}" alt="Market image preview" class="img-fluid" style="max-height: 160px;">
                                    @else
                                        <i class="fas fa-camera fa-2x text-secondary"></i>
                                        <div>{{ translate('messages.Click to Upload Image') }}</div>
                                    @endif
                                </div>
                                <label for="marketImage" class="stretched-link" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; cursor: pointer;"></label>
                            </div>
                            <small class="form-text text-muted mt-2">{{ translate('messages.Recommended size: 400x400 pixels. Max size: 2MB.') }}</small>
                        </div>
                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block">{{ translate('messages.Update Market') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-geosearch@3.0.0/dist/geosearch.css" />
<script src="https://unpkg.com/leaflet-geosearch@3.0.0/dist/geosearch.umd.js"></script>

<script>
    $(document).ready(function() {
        // Image Preview & File Input Handling (Copied from create)
        $('#marketImage').on('change', function() {
            const file = this.files[0];
            const reader = new FileReader();
            const $previewContainer = $('#imagePreview');
            const $wrap = $(this).closest('.image-upload-wrap');

            if (file) {
                reader.onload = function(e) {
                    $previewContainer.html('<img src="' + e.target.result + '" alt="Image Preview" class="img-fluid" style="max-height: 160px;">');
                }
                reader.readAsDataURL(file);
            } else {
                $previewContainer.html('<i class="fas fa-camera fa-2x text-secondary"></i><div>{{ translate('messages.Click to Upload Image') }}</div>');
            }
        });

        // Allow clicking preview area to open file dialog
        $(document).on('click', '.image-upload-wrap .stretched-link', function(e) {
            e.preventDefault();
            $('#marketImage').trigger('click');
        });

        // Operating Hours
        $('#operatingHoursTable').on('change', '.is-closed', function() {
            var row = $(this).closest('tr');
            var isChecked = $(this).is(':checked');
            row.find('.opening-time, .closing-time').prop('disabled', isChecked);
            if(isChecked) {
                row.find('.opening-time, .closing-time').val('');
            }
        });
        $('.is-closed').trigger('change');

        $('#applyHoursToAll').on('click', function() {
            var firstRow = $('#operatingHoursTable tbody tr:first');
            var openingTime = firstRow.find('.opening-time').val();
            var closingTime = firstRow.find('.closing-time').val();
            var isClosed = firstRow.find('.is-closed').is(':checked');

            $('#operatingHoursTable tbody tr').each(function() {
                var row = $(this);
                row.find('.opening-time').val(openingTime);
                row.find('.closing-time').val(closingTime);
                row.find('.is-closed').prop('checked', isClosed).trigger('change');
            });
        });
    });

    // Location Dropdowns
    var selectedDivision = '{{ old('division', $market->location->division ?? '') }}';
    var selectedDistrict = '{{ old('district', $market->location->district ?? '') }}';
    var selectedUpazila = '{{ old('upazila', $market->location->upazila ?? '') }}';

    function getDistricts(division, targetDistrict = null) {
        if (!division) {
            $('#marketDistrict').html('<option></option>').prop('disabled', true);
            $('#marketUpazila').html('<option></option>').prop('disabled', true);
            return;
        }
        $.ajax({
            url: '{{ url("/admin/locations/districts") }}/' + division,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var districtSelect = $('#marketDistrict');
                districtSelect.html('<option></option>');
                $.each(data, function(index, district) {
                    districtSelect.append($('<option>', {
                        value: district,
                        text: district
                    }));
                });
                districtSelect.prop('disabled', false);
                if (targetDistrict) {
                    districtSelect.val(targetDistrict).trigger('change');
                }
            }
        });
    }

    function getThanas(division, district, targetThana = null) {
        if (!division || !district) {
            $('#marketUpazila').html('<option></option>').prop('disabled', true);
            return;
        }
        $.ajax({
            url: '{{ url("/admin/locations/thanas") }}/' + division + '/' + district,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var upazilaSelect = $('#marketUpazila');
                upazilaSelect.html('<option></option>');
                $.each(data, function(index, thana) {
                    upazilaSelect.append($('<option>', {
                        value: thana,
                        text: thana
                    }));
                });
                upazilaSelect.prop('disabled', false);
                if (targetThana) {
                    upazilaSelect.val(targetThana).trigger('change');
                }
            }
        });
    }

    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Select an option'
        });

        if (selectedDivision) {
            $('#marketDivision').val(selectedDivision).trigger('change');
            getDistricts(selectedDivision, selectedDistrict);
        }

        $('#marketDistrict').on('change', function() {
            var division = $('#marketDivision').val();
            var district = $(this).val();
            if(district === selectedDistrict) {
                 getThanas(division, district, selectedUpazila);
            } else {
                 getThanas(division, district);
            }
        });
    });

    // Leaflet Map
    var map = L.map('map').setView([{{ old('latitude', $market->latitude ?? 23.8041) }}, {{ old('longitude', $market->longitude ?? 90.4152) }}], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var marker = L.marker([{{ old('latitude', $market->latitude ?? 23.8041) }}, {{ old('longitude', $market->longitude ?? 90.4152) }}], {draggable: true}).addTo(map);

    marker.on('dragend', function(event) {
        var position = marker.getLatLng();
        $('#marketLatitude').val(position.lat);
        $('#marketLongitude').val(position.lng);
    });

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        $('#marketLatitude').val(e.latlng.lat);
        $('#marketLongitude').val(e.latlng.lng);
    });

    const provider = new GeoSearch.OpenStreetMapProvider();
    const searchControl = new GeoSearch.GeoSearchControl({
        provider: provider,
        style: 'bar',
        showMarker: true,
        showPopup: false,
        marker: {
            icon: new L.Icon.Default(),
            draggable: false,
        },
        autoClose: true,
        searchLabel: 'Enter address',
    });
    map.addControl(searchControl);

    map.on('geosearch/showlocation', function(result) {
        marker.setLatLng(result.location);
        $('#marketLatitude').val(result.location.lat);
        $('#marketLongitude').val(result.location.lng);
    });

</script>
@endpush