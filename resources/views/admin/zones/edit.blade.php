@extends('layouts.admin.app')

@section('title', translate('messages.edit_zone'))

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 mb-0 text-gray-800">{{translate('messages.edit_zone')}}</h1>
        <a href="{{route('admin.zones.index')}}" class="d-none d-sm-inline-block btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{translate('messages.back')}}
        </a>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.edit_zone') }}</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.zones.update', $zone->id) }}" method="POST" class="row">
                        @csrf
                        @method('PUT')
                        <div class="col-lg-4 col-12 mb-3">
                            <div class="form-group">
                                <label for="name">{{ translate('messages.name') }}</label>
                                <input type="text" name="name" class="form-control" id="name" placeholder="{{ translate('messages.enter_zone_name') }}" value="{{ old('name', $zone->name) }}" required>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group mb-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', $zone->is_active) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">{{ translate('messages.active') }}</label>
                                </div>
                                @error('is_active')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-1"></i>{{ translate('messages.update') }}
                                </button>
                                <button type="button" id="editClearPolygon" class="btn btn-warning" style="display: none;">
                                    <i class="fas fa-redo mr-1"></i>Clear Polygon
                                </button>
                            </div>
                        </div>
                        <div class="col-lg-8 col-12 mb-3">
                            <label class="d-flex justify-content-between align-items-center mb-1">
                                <span>{{ translate('messages.select_area') }}</span>
                                <span class="text-muted small">{{ translate('messages.click_on_map_to_draw_polygon') }}</span>
                            </label>
                            <div style="position: relative; height: 400px;">
                                <div id="editMap" style="height: 100%; width: 100%; border-radius: 5px; position: relative;"></div>
                                <div style="position: absolute; top: 20px; left: 50%; transform: translateX(-50%); width: 95%; max-width: 460px; z-index: 999;">
                                    <div class="input-group" style="box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                                        <input type="text" id="editSearchBox" class="form-control" placeholder="Search location..." autocomplete="off">
                                        <div class="input-group-append">
                                            <button type="button" id="editStartPolygon" class="btn btn-primary" title="Draw polygon">
                                                <i class="fas fa-draw-polygon"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div id="editSearchResults" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-top: none; max-height: 200px; overflow-y: auto; display: none; z-index: 1000; margin-top: 2px; border-radius: 0 0 4px 4px;"></div>
                                </div>
                                <div style="position: absolute; bottom: 20px; left: 20px; z-index: 999; background: rgba(255,255,255,0.9); padding: 6px 12px; border-radius: 4px; font-size: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                                    <span><span style="display:inline-block;width:12px;height:12px;background:#2563eb;border-radius:2px;margin-right:6px;"></span>Current zone & New drawing</span>
                                    <span class="ml-3"><span style="display:inline-block;width:12px;height:12px;background:#f97316;border-radius:2px;margin-right:6px;"></span>Other zones</span>
                                </div>
                            </div>
                            <input type="hidden" name="coordinates" id="editCoordinates" value="{{ old('coordinates') }}" data-has-old="{{ old('coordinates') ? '1' : '0' }}">
                            @error('coordinates')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>
<script>
// Zone Map Configuration (shared with create page)
const ZoneMapConfig = {
    // Bangladesh bounds for search filtering
    bounds: { lat: [20.34, 26.64], lng: [88.01, 92.67] },
    // Default map center (Bangladesh)
    center: [23.8103, 90.4125],
    // Default zoom level
    zoom: 12,
    // Search API settings
    search: {
        url: 'https://photon.komoot.io/api/',
        timeout: 8000,
        delay: 300,
        limit: 15,
        lang: 'en'
    },
    // Polygon drawing colors
    colors: {
        currentZone: '#2563eb',
        otherZones: '#f97316'
    }
};

// Initialize zone map for edit page
$(function() {
    const currentZoneCoords = @json($currentZoneCoords ?? []);
    const otherZones = @json($otherZonesCoords ?? []);
    const zoneMap = new ZoneMapEdit('editMap', currentZoneCoords, otherZones);
    zoneMap.init();
});

/**
 * Zone Map for Edit Page
 * Handles map initialization, existing zone display, and zone editing
 */
class ZoneMapEdit {
    constructor(mapId, currentZoneCoords = [], otherZones = []) {
        this.mapId = mapId;
        this.currentZoneCoords = currentZoneCoords;
        this.otherZones = otherZones;
        this.map = null;
        this.existingLayer = null;
        this.otherZonesLayer = null;
        this.drawnLayer = null;
        this.drawControl = null;
        this.ui = null;
    }

    init() {
        this.ui = this.getUiElements();
        this.map = this.createMap();
        this.addTileLayer();
        this.setupLayers();
        this.setupDrawControls();
        this.setupEventHandlers();
        this.renderZones();
        this.loadInitialDrawing();
    }

    getUiElements() {
        return {
            coordinates: $('#editCoordinates'),
            clearBtn: $('#editClearPolygon'),
            searchInput: $('#editSearchBox'),
            searchResults: $('#editSearchResults'),
            startPolygon: $('#editStartPolygon')
        };
    }

    createMap() {
        return L.map(this.mapId).setView(ZoneMapConfig.center, ZoneMapConfig.zoom);
    }

    addTileLayer() {
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(this.map);
    }

    setupLayers() {
        this.existingLayer = new L.FeatureGroup();
        this.otherZonesLayer = new L.FeatureGroup();
        this.drawnLayer = new L.FeatureGroup();
        this.map.addLayer(this.existingLayer);
        this.map.addLayer(this.otherZonesLayer);
        this.map.addLayer(this.drawnLayer);
    }

    setupDrawControls() {
        this.drawControl = new L.Control.Draw({
            position: 'topright',
            draw: {
                polygon: {
                    allowIntersection: false,
                    showArea: true,
                    drawError: {
                        color: '#e1e100',
                        message: '<strong>Error:</strong> Shape edges cannot cross!'
                    },
                    shapeOptions: {
                        color: ZoneMapConfig.colors.currentZone,
                        fillOpacity: 0.3,
                        weight: 2
                    }
                },
                polyline: false,
                circle: false,
                rectangle: false,
                marker: false,
                circlemarker: false
            },
            edit: {
                featureGroup: this.drawnLayer,
                remove: true
            }
        });
        this.map.addControl(this.drawControl);
    }

    setupEventHandlers() {
        // Draw controls
        this.setupPolygonButton();
        this.registerDrawHandlers();
        this.setupClearButton();
        
        // Search functionality
        this.setupSearch();
        this.setupGlobalClick();
    }

    setupPolygonButton() {
        this.ui.startPolygon.on('click', () => {
            const toolbar = this.drawControl?._toolbars?.draw;
            if (!toolbar?._modes?.polygon?.handler) {
                return;
            }
            this.drawnLayer.clearLayers();
            toolbar._modes.polygon.handler.enable();
        });
    }

    registerDrawHandlers() {
        this.map.on(L.Draw.Event.CREATED, (event) => {
            this.drawnLayer.clearLayers();
            this.drawnLayer.addLayer(event.layer);
            const latLngs = this.normalizeLatLngs(event.layer);
            this.storePolygon(latLngs);
        });

        this.map.on(L.Draw.Event.EDITED, (event) => {
            event.layers.eachLayer((layer) => {
                const latLngs = this.normalizeLatLngs(layer);
                this.storePolygon(latLngs);
            });
        });

        this.map.on(L.Draw.Event.DELETED, () => {
            this.drawnLayer.clearLayers();
            this.resetToExistingCoordinates();
        });
    }

    setupClearButton() {
        this.ui.clearBtn.on('click', (e) => {
            e.preventDefault();
            this.drawnLayer.clearLayers();
            this.resetToExistingCoordinates();
        });
    }

    setupSearch() {
        let searchTimeout;
        
        this.ui.searchInput.on('input', () => {
            clearTimeout(searchTimeout);
            const query = this.ui.searchInput.val().trim();

            if (query.length < 2) {
                this.ui.searchResults.hide().empty();
                return;
            }

            searchTimeout = setTimeout(() => {
                this.fetchPhotonResults(query)
                    .done((response) => this.renderSearchResults(response))
                    .fail((_, textStatus) => {
                        const message = textStatus === 'timeout'
                            ? 'Search timed out. Please try again in a moment.'
                            : 'Error searching location. Please try again.';
                        this.showSearchMessage(message);
                    });
            }, ZoneMapConfig.search.delay);
        });
    }

    setupGlobalClick() {
        $(document).on('click', (e) => {
            if (!$(e.target).closest(this.ui.searchInput).length && 
                !$(e.target).closest(this.ui.searchResults).length) {
                this.ui.searchResults.hide();
            }
        });
    }

    renderZones() {
        this.renderCurrentZone();
        this.renderOtherZones();
    }

    renderCurrentZone() {
        if (!this.currentZoneCoords.length) return;

        const polygon = L.polygon(
            this.currentZoneCoords.map(coord => [coord.lat, coord.lng]),
            {
                color: ZoneMapConfig.colors.currentZone,
                fillOpacity: 0.3,
                weight: 2
            }
        );

        this.existingLayer.addLayer(polygon);
        this.map.fitBounds(polygon.getBounds(), { padding: [20, 20] });
    }

    renderOtherZones() {
        if (!this.otherZones.length) return;

        this.otherZones.forEach(zone => {
            if (!zone?.points?.length) return;

            const polygon = L.polygon(
                zone.points.map(coord => [coord.lat, coord.lng]),
                {
                    color: ZoneMapConfig.colors.otherZones,
                    fillOpacity: 0.2,
                    weight: 2
                }
            ).bindTooltip(zone.name, { sticky: true });

            this.otherZonesLayer.addLayer(polygon);
        });
    }

    loadInitialDrawing() {
        const initialDrawn = this.parseCoordinateInput(this.ui.coordinates.val());
        if (initialDrawn.length) {
            this.addDrawnPolygon(initialDrawn);
            this.storePolygon(initialDrawn);
        } else {
            this.ui.clearBtn.hide();
        }
    }

    fetchPhotonResults(query) {
        return $.ajax({
            url: ZoneMapConfig.search.url,
            data: {
                q: query,
                limit: ZoneMapConfig.search.limit,
                lang: ZoneMapConfig.search.lang
            },
            dataType: 'json',
            timeout: ZoneMapConfig.search.timeout
        });
    }

    renderSearchResults(response) {
        const features = response?.features || [];
        const items = features
            .filter(feature => this.isBangladeshFeature(feature))
            .map(feature => this.buildSearchResult(feature));

        if (!items.length) {
            this.showSearchMessage('No Bangladesh results found');
            return;
        }

        this.ui.searchResults.html(items.join('')).show();
        this.attachSearchResultHandlers();
    }

    attachSearchResultHandlers() {
        this.ui.searchResults.find('.search-result-item')
            .on('click', (e) => {
                const $item = $(e.currentTarget);
                const lat = parseFloat($item.data('lat'));
                const lon = parseFloat($item.data('lon'));
                const name = $item.find('strong').text();

                this.ui.searchInput.val(name);
                this.ui.searchResults.hide();
                this.map.setView([lat, lon], 15);
            })
            .on('mouseenter', (e) => {
                $(e.currentTarget).css('background-color', '#f5f5f5');
            })
            .on('mouseleave', (e) => {
                $(e.currentTarget).css('background-color', 'white');
            });
    }

    showSearchMessage(message) {
        this.ui.searchResults.html(
            `<div style="padding: 10px; text-align: center; color: #999;">${message}</div>`
        ).show();
    }

    isBangladeshFeature(feature) {
        if (!feature?.geometry?.coordinates) return false;
        
        const coords = feature.geometry.coordinates;
        const lon = parseFloat(coords[0]);
        const lat = parseFloat(coords[1]);
        
        // Check if coordinates are within Bangladesh bounds
        return !isNaN(lat) && !isNaN(lon) &&
               lat >= ZoneMapConfig.bounds.lat[0] && 
               lat <= ZoneMapConfig.bounds.lat[1] && 
               lon >= ZoneMapConfig.bounds.lng[0] && 
               lon <= ZoneMapConfig.bounds.lng[1];
    }

    buildSearchResult(feature) {
        const coords = feature.geometry.coordinates;
        const props = feature.properties;
        const lat = parseFloat(coords[1]);
        const lon = parseFloat(coords[0]);
        const title = props.name || props.street || props.city || 'Unnamed location';
        const subtitleParts = [props.city, props.state, props.postcode, props.country].filter(Boolean);
        const subtitle = subtitleParts.join(', ');

        return `<div class="search-result-item" style="padding: 10px; border-bottom: 1px solid #eee; cursor: pointer; transition: background 0.2s;" data-lat="${lat}" data-lon="${lon}">
            <strong>${title}</strong>
            ${subtitle ? `<br><small class="text-muted">${subtitle}</small>` : ''}
        </div>`;
    }

    normalizeLatLngs(layer) {
        const latLngs = layer.getLatLngs();
        return Array.isArray(latLngs[0]) ? latLngs[0] : latLngs;
    }

    stringifyLatLngs(latLngs) {
        return latLngs
            .map(coord => {
                const lat = typeof coord.lat === 'number' ? coord.lat : (Array.isArray(coord) ? coord[0] : null);
                const lng = typeof coord.lng === 'number' ? coord.lng : (Array.isArray(coord) ? coord[1] : null);
                return lat !== null && lng !== null ? `(${lat}, ${lng})` : null;
            })
            .filter(Boolean)
            .join(',');
    }

    storePolygon(latLngs) {
        if (!latLngs?.length) {
            this.ui.coordinates.val('');
            this.ui.clearBtn.hide();
            return;
        }

        const coordsString = this.stringifyLatLngs(latLngs);
        this.ui.coordinates.val(coordsString);
        this.ui.clearBtn.show();
    }

    parseCoordinateInput(value) {
        if (!value) return [];

        try {
            const parsed = JSON.parse(value);
            if (!Array.isArray(parsed)) return [];

            return parsed
                .filter(coord => coord && typeof coord.lat === 'number' && typeof coord.lng === 'number')
                .map(coord => ({ lat: coord.lat, lng: coord.lng }));
        } catch (error) {
            // Handle legacy string format: (lat,lng),(lat,lng)
            const legacy = value.toString().trim();
            if (!legacy) return [];

            const sanitized = legacy.replace(/^\(|\)$/g, '');
            if (!sanitized) return [];

            return sanitized.split(/\)\s*,\s*\(/)
                .map(pair => {
                    const parts = pair.split(',');
                    if (parts.length < 2) return null;

                    const lat = parseFloat(parts[0]);
                    const lng = parseFloat(parts[1]);

                    return isNaN(lat) || isNaN(lng) ? null : { lat, lng };
                })
                .filter(Boolean);
        }
    }

    addDrawnPolygon(latLngs) {
        const polygon = L.polygon(
            latLngs.map(coord => [coord.lat, coord.lng]),
            {
                color: ZoneMapConfig.colors.currentZone,
                fillOpacity: 0.3,
                weight: 2
            }
        );

        this.drawnLayer.clearLayers();
        this.drawnLayer.addLayer(polygon);
    }

    resetToExistingCoordinates() {
        this.ui.coordinates.val('');
        this.ui.clearBtn.hide();
    }
}
</script>
@endpush 