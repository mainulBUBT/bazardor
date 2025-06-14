@extends('layouts.admin.app')

@section('title', translate('messages.Market Overview'))

@push('css_or_js')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
@endpush

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $market->name }}</h1>
        <div>
            <a href="{{ route('admin.markets.index') }}" class="btn btn-sm btn-secondary shadow-sm mr-2">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ translate('messages.Back to Markets') }}
            </a>
            <a href="{{ route('admin.markets.edit', $market->id) }}" class="btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-edit fa-sm"></i> {{ translate('messages.Edit Market') }}
            </a>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Market Information') }}</h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">{{ translate('messages.Market Name') }}</dt>
                        <dd class="col-sm-8">{{ $market->name }}</dd>

                        <dt class="col-sm-4">{{ translate('messages.Type') }}</dt>
                        <dd class="col-sm-8">{{ $market->type }}</dd>

                        <dt class="col-sm-4">{{ translate('messages.Address') }}</dt>
                        <dd class="col-sm-8">{{ $market->address }}</dd>

                        <dt class="col-sm-4">{{ translate('messages.Status') }}</dt>
                        <dd class="col-sm-8">
                            @if($market->is_active)
                                <span class="badge badge-success">{{ translate('messages.Active') }}</span>
                            @else
                                <span class="badge badge-danger">{{ translate('messages.Inactive') }}</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">{{ translate('messages.Featured') }}</dt>
                        <dd class="col-sm-8">
                            @if($market->is_featured)
                                <span class="badge badge-warning">{{ translate('messages.Yes') }}</span>
                            @else
                                <span class="badge badge-secondary">{{ translate('messages.No') }}</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">{{ translate('messages.Created At') }}</dt>
                        <dd class="col-sm-8">{{ $market->created_at->format('d M Y, h:i A') }}</dd>
                    </dl>
                    @if($market->description)
                        <hr>
                        <h6>{{ translate('messages.Description') }}</h6>
                        <p>{{ $market->description }}</p>
                    @endif
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Location') }}</h6>
                </div>
                <div class="card-body">
                    <div id="location_map_div" style="height: 220px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Market Image') }}</h6>
                </div>
                <div class="card-body text-center">
                    @if($market->image_path)
                        <img src="{{ asset('public/storage/markets/'.$market->image_path) }}" alt="Market Image" class="img-fluid img-thumbnail mb-2" style="max-width: 220px; max-height: 220px;">
                    @else
                        <img src="{{ asset('adminpanel/img/market-placeholder.png') }}" class="img-fluid img-thumbnail mb-2" style="max-width: 220px; max-height: 220px;" alt="placeholder">
                    @endif
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Stats & Ratings') }}</h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-2">
                        <span class="h5">{{ translate('messages.Products') }}:</span>
                        <span class="font-weight-bold">
                            {{ \App\Models\ProductMarketPrice::where('market_id', $market->id)->distinct('product_id')->count('product_id') }}
                        </span>
                    </div>
                    <div class="mb-2">
                        <span class="h5">{{ translate('messages.Ratings') }}:</span>
                        <span class="font-weight-bold">{{ $market->rating_count ?? 0 }}</span>
                    </div>
                    <h2 class="display-4 font-weight-bold mb-0">{{ number_format($market->rating, 1) }}</h2>
                    <p class="text-muted">{{ $market->rating_count ?? 0 }} {{ Str::plural('review', $market->rating_count ?? 0) }}</p>
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

