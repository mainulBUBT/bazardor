@extends('layouts.admin.app')

@section('title', translate('messages.Market Details'))

@push('css_or_js')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
@endpush

@section('content')
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-print-none pb-2">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{ $market->name }}</h1>
                    <span>{{ translate('messages.market_id') }} #{{ $market->id }}</span>
                    <div class="mt-2">
                        <i class="tio-date-range"></i>
                        {{ translate('messages.joined_at') }}: {{ $market->created_at->format('d M Y, h:i A') }}
                    </div>
                </div>

                <div class="col-sm-auto">
                    <a href="{{ route('admin.markets.edit', $market->id) }}" class="btn btn-primary">
                        <i class="tio-edit"></i> {{ translate('messages.edit') }}
                    </a>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">{{ translate('messages.market_information') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>{{ translate('messages.name') }}:</strong> {{ $market->name }}</p>
                                <p><strong>{{ translate('messages.type') }}:</strong> {{ $market->type }}</p>
                                <p><strong>{{ translate('messages.address') }}:</strong> {{ $market->address }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>{{ translate('messages.status') }}:</strong> 
                                    @if($market->is_active)
                                        <span class="badge badge-success">{{ translate('messages.active') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ translate('messages.inactive') }}</span>
                                    @endif
                                </p>
                                <p><strong>{{ translate('messages.featured') }}:</strong> 
                                    @if($market->is_featured)
                                        <span class="badge badge-success">{{ translate('messages.yes') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ translate('messages.no') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if($market->description)
                            <hr>
                            <h6>{{ translate('messages.description') }}</h6>
                            <p>{{ $market->description }}</p>
                        @endif
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">{{ translate('messages.market_location') }}</h5>
                    </div>
                    <div class="card-body">
                        <div id="location_map_div" style="height: 350px"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">{{ translate('messages.market_owner_information') }}</h5>
                    </div>
                    <div class="card-body">
                        {{-- Placeholder for owner info --}}
                        <p>{{ translate('messages.owner_info_not_available') }}</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">{{ translate('messages.market_ratings') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <h2 class="display-4 font-weight-bold">{{ number_format($market->rating, 1) }}</h2>
                            <p class="text-muted">{{ $market->rating_count ?? 0 }} {{ Str::plural('review', $market->rating_count ?? 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script id="map-data" type="application/json">
        {
            "lat": @json($market->latitude),
            "lng": @json($market->longitude),
            "name": @json($market->name),
            "noLocationMessage": @json(translate('messages.location_data_not_available'))
        }
    </script>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const mapDiv = document.getElementById('location_map_div');
            const mapDataElement = document.getElementById('map-data');
            const mapData = JSON.parse(mapDataElement.textContent);

            const lat = parseFloat(mapData.lat);
            const lng = parseFloat(mapData.lng);

            if (!isNaN(lat) && !isNaN(lng)) {
                const map = L.map('location_map_div').setView([lat, lng], 13);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                const marker = L.marker([lat, lng]).addTo(map);

                marker.bindPopup(`<b>${mapData.name}</b>`).openPopup();

                // Disable all map interactions
                map.dragging.disable();
                map.touchZoom.disable();
                map.doubleClickZoom.disable();
                map.scrollWheelZoom.disable();
                map.boxZoom.disable();
                map.keyboard.disable();
                if (map.tap) map.tap.disable();
                mapDiv.style.cursor = 'default';

            } else {
                mapDiv.innerHTML = `<div class="text-center p-5">${mapData.noLocationMessage}</div>`;
            }
        });
    </script>
@endpush
