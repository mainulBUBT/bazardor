@extends('layouts.admin.app')
@section('title', translate('messages.Markets Management'))
@section('content')


    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ translate('messages.Add New Market') }}</h1>
        <div class="ml-auto">
                <a href="{{ route('admin.markets.index') }}" class="btn btn-sm btn-outline-secondary shadow-sm mr-2">
                <i class="fas fa-arrow-left fa-sm"></i> {{ translate('messages.Back to Markets') }}
            </a>
        </div>
    </div>

    <form action="{{ route('admin.markets.store') }}" method="POST" enctype="multipart/form-data" id="addMarketForm">
        @csrf
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
                                <input type="text" name="name" class="form-control" id="marketName" value="{{ old('name') }}" required placeholder="e.g., Mirpur 11 Kacha Bazar">
                            </div>
                            <div class="col-md-4">
                                <label for="marketSlug" class="form-label">{{ translate('messages.Slug') }}</label>
                                <input type="text" name="slug" class="form-control" id="marketSlug" value="{{ old('slug') }}" placeholder="Auto-generated">
                                <small class="form-text text-muted">{{ translate('messages.URL-friendly identifier.') }}</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="marketType" class="form-label">{{ translate('messages.Market Type') }} <span class="text-danger">*</span></label>
                            <select class="form-control select2" id="marketType" name="type" required>
                                <option value="" disabled {{ old('type') ? '' : 'selected' }}>{{ translate('messages.Select Type') }}</option>
                                @foreach(\App\Enums\MarketType::cases() as $type)
                                    <option value="{{ $type->value }}" {{ old('type') == $type->value ? 'selected' : '' }}>{{ $type->value }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="marketDescription" class="form-label">{{ translate('messages.Short Description') }}</label>
                            <textarea name="description" class="form-control" id="marketDescription" rows="3" placeholder="{{ translate('messages.A brief summary of the market.') }}">{{ old('description') }}</textarea>
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
                            <textarea name="address" class="form-control" id="marketAddress" rows="2" required placeholder="e.g., Block C, Section 11, Mirpur, Dhaka">{{ old('address') }}</textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="marketDivision" class="form-label">{{ translate('messages.Division') }} <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="marketDivision" name="division" onchange="getDistricts(this.value)" required>
                                    <option></option> <!-- Placeholder for Select2 -->
                                    @foreach($divisions as $division)
                                        <option value="{{ $division }}" {{ old('division') == $division ? 'selected' : '' }}>{{ $division }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="marketDistrict" class="form-label">{{ translate('messages.District') }} <span class="text-danger">*</span></label>
                                <select class="form-control select2" id="marketDistrict" name="district" onchange="getThanas(document.getElementById('marketDivision').value, this.value)" required disabled>
                                    <option></option> 
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="marketUpazila" class="form-label">{{ translate('messages.Upazila/Thana') }}</label>
                                <select class="form-control select2" id="marketUpazila" name="upazila" disabled>
                                    <option></option> 
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="marketLatitude" class="form-label">{{ translate('messages.Latitude') }}</label>
                                <input type="text" name="latitude" class="form-control" id="marketLatitude" value="{{ old('latitude') }}" placeholder="e.g., 23.8041">
                                <small class="form-text text-muted">{{ translate('messages.Use tools like Google Maps.') }}</small>
                            </div>
                            <div class="col-md-6">
                                <label for="marketLongitude" class="form-label">{{ translate('messages.Longitude') }}</label>
                                <input type="text" name="longitude" class="form-control" id="marketLongitude" value="{{ old('longitude') }}" placeholder="e.g., 90.4152">
                                <small class="form-text text-muted">{{ translate('messages.Click on map or enter manually.') }}</small>
                            </div>
                        </div>
                        <!-- Map Container -->
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
                                    <!-- Data for Monday (template row) -->
                                    @php $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']; @endphp
                                    @foreach($days as $day)
                                    <tr data-day="{{ $day }}">
                                        <td>{{ translate('messages.' . $day) }}</td>
                                        <td><input type="time" name="opening_hours[{{$day}}][opening_time]" class="form-control form-control-sm opening-time" value="{{ old('opening_hours.'.$day.'.opening_time', '08:00') }}"></td>
                                        <td><input type="time" name="opening_hours[{{$day}}][closing_time]" class="form-control form-control-sm closing-time" value="{{ old('opening_hours.'.$day.'.closing_time', '20:00') }}"></td>
                                        <td class="text-center"><div class="custom-control custom-checkbox"><input type="checkbox" name="opening_hours[{{$day}}][is_closed]" class="custom-control-input is-closed" id="closed{{$day}}" {{ old('opening_hours.'.$day.'.is_closed') ? 'checked' : '' }}><label class="custom-control-label" for="closed{{$day}}"></label></div></td>
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
                <!-- Status & Visibility Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Settings') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="marketStatus" class="form-label">{{ translate('messages.Status') }}</label>
                            <select name="status" class="form-control" id="marketStatus">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>{{ translate('messages.Active') }}</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>{{ translate('messages.Inactive') }}</option>
                                <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>{{ translate('messages.Pending Review') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="marketVisibility" class="form-label">{{ translate('messages.Visibility') }}</label>
                            <select class="form-control" id="marketVisibility">
                                <option value="public" selected>{{ translate('messages.Public') }}</option>
                                <option value="private">{{ translate('messages.Private (Admin only)') }}</option>
                            </select>
                        </div>
                            <div class="custom-control custom-switch">
                            <input type="checkbox" name="featured" value="1" class="custom-control-input" id="featuredSwitch" {{ old('featured') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="featuredSwitch">{{ translate('messages.Featured Market') }}</label>
                        </div>
                    </div>
                </div>

                <!-- Market Features Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Features & Amenities') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="custom-control custom-switch mb-2">
                            <input type="checkbox" class="custom-control-input" id="nonVegSwitch">
                            <label class="custom-control-label" for="nonVegSwitch">{{ translate('messages.Non-Veg Items Available') }}</label>
                        </div>
                        <div class="custom-control custom-switch mb-2">
                            <input type="checkbox" class="custom-control-input" id="halalSwitch">
                            <label class="custom-control-label" for="halalSwitch">{{ translate('messages.Halal Items Available') }}</label>
                        </div>
                        <div class="custom-control custom-switch mb-2">
                            <input type="checkbox" class="custom-control-input" id="parkingSwitch">
                            <label class="custom-control-label" for="parkingSwitch">{{ translate('messages.Parking Available') }}</label>
                        </div>
                            <div class="custom-control custom-switch mb-2">
                            <input type="checkbox" class="custom-control-input" id="restroomSwitch">
                            <label class="custom-control-label" for="restroomSwitch">{{ translate('messages.Restroom Available') }}</label>
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="deliverySwitch">
                            <label class="custom-control-label" for="deliverySwitch">{{ translate('messages.Home Delivery Offered') }}</label>
                        </div>
                    </div>
                </div>

                <!-- Market Image Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Market Image') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="image-preview-container mb-3" onclick="document.getElementById('marketImage').click();">
                            <div class="image-preview" id="imagePreview">
                                <i class="fas fa-camera"></i>
                                <span>{{ translate('messages.Click to Upload Image') }}</span>
                            </div>
                        </div>
                        <div class="custom-file">
                            <input type="file" name="image" class="custom-file-input" id="marketImage" accept="image/*" style="display: none;"> <!-- Hide default input -->
                            <label class="custom-file-label" for="marketImage" id="marketImageLabel">{{ translate('messages.Choose file...') }}</label>
                        </div>
                        <small class="form-text text-muted mt-2">{{ translate('messages.Recommended: 1200x800px, Max 2MB') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Action Buttons -->
        <div class="d-flex justify-content-end mb-4">
            <a href="{{ route('admin.markets.index') }}" class="btn btn-secondary mr-2">{{ translate('messages.Cancel') }}</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ translate('messages.Save Market') }}
            </button>
        </div>
    </form>

@endsection
@push('scripts')
     <script>
        $(document).ready(function() {

            // Image Preview & File Input Handling
            $('#marketImage').on('change', function() {
                const file = this.files[0];
                const reader = new FileReader();
                const $previewContainer = $('#imagePreview');
                const $label = $('#marketImageLabel'); 
                if (file) {
                    reader.onload = function(e) {
                        $previewContainer.html('<img src="' + e.target.result + '" alt="Market Preview">');
                    }
                    reader.readAsDataURL(file);
                    $label.text(file.name); 
                } else {
                    $previewContainer.html('<i class="fas fa-camera"></i><span>{{ translate('messages.Click to Upload Image') }}</span>');
                    $label.text('{{ translate('messages.Choose file...') }}');
                }
            });

             // --- Operating Hours Logic ---
             $('#applyHoursToAll').on('click', function() {
                const $firstRow = $('#operatingHoursTable tbody tr:first');
                const firstOpening = $firstRow.find('.opening-time').val();
                const firstClosing = $firstRow.find('.closing-time').val();
                const firstIsClosed = $firstRow.find('.is-closed').prop('checked');

                $('#operatingHoursTable tbody tr').each(function(index) {
                    if(index > 0) {
                        $(this).find('.opening-time').val(firstOpening).prop('disabled', firstIsClosed);
                        $(this).find('.closing-time').val(firstClosing).prop('disabled', firstIsClosed);
                        $(this).find('.is-closed').prop('checked', firstIsClosed);
                    }
                });
             });

             // Disable time inputs when 'Closed' is checked
             $('#operatingHoursTable').on('change', '.is-closed', function() {
                 const $row = $(this).closest('tr');
                 const isChecked = $(this).prop('checked');
                 $row.find('.opening-time, .closing-time').prop('disabled', isChecked);
             });
             // Initial check on load
             $('#operatingHoursTable .is-closed').trigger('change');

            // --- Leaflet Map Logic ---
            const latInput = document.getElementById('marketLatitude');
            const lngInput = document.getElementById('marketLongitude');
            const addressInput = document.getElementById('marketAddress');

            // Initialize map centered on Dhaka, Bangladesh
            const map = L.map('map').setView([23.8103, 90.4125], 12);

            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Add a draggable marker
            let marker = L.marker([23.8103, 90.4125], { draggable: true }).addTo(map);
            updateLatLngInputs(marker.getLatLng());

            // --- GeoSearch Control ---
            const { GeoSearchControl, OpenStreetMapProvider } = window.GeoSearch;
            const provider = new OpenStreetMapProvider({
                params: {
                    'accept-language': 'en', // for language consistency
                    countrycodes: 'bd', // limit search to Bangladesh
                },
            });

            const searchControl = new GeoSearchControl({
                provider: provider,
                style: 'bar',
                showMarker: false,
                showPopup: false,
                autoClose: true,
                retainZoomLevel: false,
                animateZoom: true,
                keepResult: true,
                searchLabel: 'Search for an address...',
            });
            map.addControl(searchControl);

            map.on('geosearch/showlocation', function(result) {
                const latlng = { lat: result.location.y, lng: result.location.x };
                marker.setLatLng(latlng);
                updateLatLngInputs(latlng);
                addressInput.value = result.location.label; // Use address from search result
            });

            // Update lat/lng on marker drag
            marker.on('dragend', function(event) {
                const position = marker.getLatLng();
                updateLatLngInputs(position);
                updateAddressInput(position);
            });

            // Update marker on map click
            map.on('click', function(e) {
                marker.setLatLng(e.latlng);
                updateLatLngInputs(e.latlng);
                updateAddressInput(e.latlng);
            });

            function updateLatLngInputs(latlng) {
                const newLat = latlng.lat.toFixed(6);
                const newLng = latlng.lng.toFixed(6);
                // Only update if the value is different to prevent re-triggering 'input' event
                if (latInput.value !== newLat) {
                    latInput.value = newLat;
                }
                if (lngInput.value !== newLng) {
                    lngInput.value = newLng;
                }
            }

            function updateAddressInput(latlng) {
                addressInput.value = 'Loading address...';
                addressInput.classList.add('address-loading');

                // Using Nominatim for reverse geocoding
                const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${latlng.lat}&lon=${latlng.lng}`;
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.display_name) {
                            addressInput.value = data.display_name;
                        } else {
                            addressInput.value = 'Address not found. Please enter manually.';
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching address:', err);
                        addressInput.value = 'Could not fetch address. Please enter manually.';
                    })
                    .finally(() => {
                        addressInput.classList.remove('address-loading');
                    });
            }

            // --- Two-way binding for Lat/Lng inputs ---
            let debounceTimeout;
            function updateMapFromInputs() {
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(() => {
                    const lat = parseFloat(latInput.value);
                    const lng = parseFloat(lngInput.value);

                    if (!isNaN(lat) && !isNaN(lng)) {
                        const newLatLng = L.latLng(lat, lng);
                        // Use a tolerance for float comparison to avoid infinite loops
                        if (!marker.getLatLng().equals(newLatLng, 1e-6)) { 
                             marker.setLatLng(newLatLng);
                             map.panTo(newLatLng);
                             updateAddressInput(newLatLng);
                        }
                    }
                }, 800); // 800ms delay
            }

            latInput.addEventListener('input', updateMapFromInputs);
            lngInput.addEventListener('input', updateMapFromInputs);

            // --- Logic to handle old input for dependent dropdowns ---
            const oldDivision = "{{ old('division') }}";
            const oldDistrict = "{{ old('district') }}";
            const oldUpazila = "{{ old('upazila') }}";

            if (oldDivision) {
                getDistricts(oldDivision, oldDistrict, oldUpazila);
            }
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
    </script>
@endpush