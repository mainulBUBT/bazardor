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
                                        <option value="{{ $division }}" {{ old('division', $division ?? '') == $division ? 'selected' : '' }}>{{ $division }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="marketDistrict" class="form-label">{{ translate('messages.District') }} <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="marketDistrict" name="district" onchange="getThanas(document.getElementById('marketDivision').value, this.value)" required>
                                    <option></option>
                                    @foreach($districts as $district)
                                        <option value="{{ $district }}" {{ old('district', $district ?? '') == $district ? 'selected' : '' }}>{{ $district }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="marketUpazila" class="form-label">{{ translate('messages.Upazila/Thana') }}</label>
                                <select class="form-control select2" id="marketUpazila" name="upazila">
                                    <option></option>
                                    @foreach($upazilas as $upazila)
                                        <option value="{{ $upazila }}" {{ old('upazila', $upazila ?? '') == $upazila ? 'selected' : '' }}>{{ $upazila }}</option>
                                    @endforeach
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
                            <select name="status" id="marketStatus" class="form-control">
                                <option value="active" {{ $market->is_active == '1' ? 'selected' : '' }}>{{ translate('messages.Active') }}</option>
                                <option value="inactive" {{ $market->is_active == '0' ? 'selected' : '' }}>{{ translate('messages.Inactive') }}</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label for="marketVisibility">{{ translate('messages.Visibility') }}</label>
                            <select name="visibility" id="marketVisibility" class="form-control">
                                <option value="public" {{ old('visibility', $market->visibility) == '0' ? 'selected' : '' }}>{{ translate('messages.Public') }}</option>
                                <option value="private" {{ old('visibility', $market->visibility) == '1' ? 'selected' : '' }}>{{ translate('messages.Private') }}</option>
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

               <!-- Market Image Card -->
               <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Market Image') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="image-preview-container mb-3" style="cursor: pointer;" onclick="document.getElementById('marketImage').click();">
                            <div class="image-preview" id="imagePreview">
                                @if($market->image_path)
                                    <img src="{{ asset('public/storage/markets/' . $market->image_path) }}" alt="{{ $market->name }}" class="img-fluid" style="max-height: 160px; border-radius: 8px;">
                                @else
                                    <i class="fas fa-camera fa-2x text-secondary"></i>
                                    <div class="mt-2">{{ translate('messages.Click to Upload Image') }}</div>
                                @endif
                            </div>
                        </div>
                        <div class="custom-file">
                            <input type="file" name="image" class="custom-file-input" id="marketImage" accept="image/*"> <!-- Default input is now visually replaced by the label -->
                            <label class="custom-file-label" for="marketImage" id="marketImageLabel" data-default-text="{{ translate('messages.Choose file...') }}">{{ translate('messages.Choose file...') }}</label>
                        </div>
                        <small class="form-text text-muted mt-2">{{ translate('messages.Recommended: 1200x800px, Max 2MB') }}</small>
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

<script>
    $(document).ready(function() {
        // Image Preview & File Input Handling
        const $fileInput = $('#marketImage');
        const $fileLabel = $('#marketImageLabel');
        const $previewContainer = $('#imagePreview');
        const originalPreviewHTML = $previewContainer.html(); // Save original state

        $fileInput.on('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $previewContainer.html(
                        `<img src="${e.target.result}" alt="Image Preview" class="img-fluid" style="max-height: 160px; border-radius: 8px;">`
                    );
                }
                reader.readAsDataURL(file);
                $fileLabel.text(file.name);
            } else {
                // User canceled file selection, revert to original
                $previewContainer.html(originalPreviewHTML);
                $fileLabel.text($fileLabel.data('default-text'));
            }
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

    
    function getDistricts(division, selectedDistrict = null, selectedUpazila = null) {
    const districtSelect = document.getElementById('marketDistrict');
    const upazilaSelect = document.getElementById('marketUpazila');
    districtSelect.innerHTML = '<option></option>';
    districtSelect.disabled = true;
    upazilaSelect.innerHTML = '<option></option>';
    upazilaSelect.disabled = true;

    if (!division) {
        $(districtSelect).val('').trigger('change');
        $(upazilaSelect).val('').trigger('change');
        return;
    };

    let url = `{{ url('admin/markets/get-districts') }}/${division}`;
    fetch(url)
        .then(response => response.json())
        .then(data => {
            districtSelect.disabled = false;
            data.forEach(district => {
                const option = new Option(district, district, false, district === selectedDistrict);
                districtSelect.appendChild(option);
            });
            $(districtSelect).val(selectedDistrict).trigger('change');
            if (selectedDistrict) {
                getThanas(division, selectedDistrict, selectedUpazila);
            }
        })
        .catch(error => console.error('Error fetching districts:', error));
    }

    function getThanas(division, district, selectedUpazila = null) {
        const upazilaSelect = document.getElementById('marketUpazila');
        upazilaSelect.innerHTML = '<option></option>';
        upazilaSelect.disabled = true;

        if (!district) {
            $(upazilaSelect).val('').trigger('change');
            return
        };

        let url = `{{ url('admin/markets/get-thanas') }}/${division}/${district}`;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                upazilaSelect.disabled = false;
                data.forEach(thana => {
                    const option = new Option(thana, thana, false, thana === selectedUpazila);
                    upazilaSelect.appendChild(option);
                });
                $(upazilaSelect).val(selectedUpazila).trigger('change');
            })
            .catch(error => console.error('Error fetching thanas:', error));
    }


    // Leaflet Map
    const latInput = document.getElementById('marketLatitude');
    const lngInput = document.getElementById('marketLongitude');
    const addressInput = document.getElementById('marketAddress');

    var map = L.map('map').setView([{{ old('latitude', $market->latitude ?? 23.8041) }}, {{ old('longitude', $market->longitude ?? 90.4152) }}], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    var marker = L.marker([{{ old('latitude', $market->latitude ?? 23.8041) }}, {{ old('longitude', $market->longitude ?? 90.4152) }}], {draggable: true}).addTo(map);

    // --- Map Logic ---
    const provider = new GeoSearch.OpenStreetMapProvider({
        params: {
            'accept-language': 'en',
            countrycodes: 'bd',
        },
    });

    const searchControl = new GeoSearch.GeoSearchControl({
        provider: provider,
        style: 'bar',
        showMarker: false,
        showPopup: false,
        autoClose: true,
        retainZoomLevel: false,
        animateZoom: true,
        keepResult: true,
    });
    map.addControl(searchControl);

    map.on('geosearch/showlocation', function(result) {
        marker.setLatLng(result.location);
        updateLatLngInputs(result.location);
        updateAddressInput(result.location);
    });

    marker.on('dragend', function(event) {
        const position = marker.getLatLng();
        updateLatLngInputs(position);
        updateAddressInput(position);
    });

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        updateLatLngInputs(e.latlng);
        updateAddressInput(e.latlng);
    });

    function updateLatLngInputs(latlng) {
        latInput.value = latlng.lat.toFixed(6);
        lngInput.value = latlng.lng.toFixed(6);
    }

    function updateAddressInput(latlng) {
        addressInput.value = 'Loading address...';
        addressInput.classList.add('address-loading');
        const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latlng.lat}&lon=${latlng.lon}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data && data.display_name) {
                    addressInput.value = data.display_name;
                } else {
                    addressInput.value = 'Address not found. Please enter manually.';
                }
            })
            .catch(error => {
                console.error('Error fetching address:', error);
                addressInput.value = 'Could not fetch address';
            })
            .finally(() => {
                addressInput.classList.remove('address-loading');
            });
    }

</script>
@endpush