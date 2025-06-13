@extends('layouts.admin.app')

@section('title', translate('messages.Market Details'))

@push('css_or_js')
    <style>
        #location_map_div {
            height: 350px;
        }
    </style>
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
                        <div id="location_map_div"></div>
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

@push('script_2')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('app.google_maps_api_key') }}&libraries=places"></script>
    <script>
        function initMap() {
            var lat = {{ json_encode($market->latitude ?? '23.8103') }};
            var lng = {{ json_encode($market->longitude ?? '90.4125') }};
            var myLatlng = new google.maps.LatLng(parseFloat(lat), parseFloat(lng));
            var mapOptions = {
                zoom: 13,
                center: myLatlng,
                mapTypeId: 'roadmap'
            };
            var map = new google.maps.Map(document.getElementById("location_map_div"), mapOptions);
            var marker = new google.maps.Marker({
                position: myLatlng,
                map: map,
                title: "{{ $market->name }}"
            });
        }
        google.maps.event.addDomListener(window, 'load', initMap);
    </script>
@endpush
