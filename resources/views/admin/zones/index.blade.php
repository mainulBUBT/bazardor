@extends('layouts.admin.app')

@section('title', translate('messages.zones'))

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 mb-0 text-gray-800">{{translate('messages.zones')}}</h1>
    </div>

    <!-- Zone Form Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.add_zone') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.zones.store') }}" class="row" method="POST">
                @csrf
                <div class="col-lg-4 col-12 mb-3">
                    <div class="form-group">
                        <label for="zoneName">{{ translate('messages.name') }}</label>
                        <input type="text" class="form-control" name="name" id="zoneName" placeholder="{{ translate('messages.enter_zone_name') }}">
                    </div>
                    <div class="form-group mb-4">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                            <label class="custom-control-label" for="is_active">{{translate('messages.active')}}</label>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus-circle mr-1"></i>{{ translate('messages.add_zone') }}
                        </button>
                        <button type="button" id="clearPolygon" class="btn btn-warning" style="display: none;">
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
                        <div id="map" style="height: 100%; width: 100%; border-radius: 5px; position: relative;"></div>
                        <div style="position: absolute; top: 20px; left: 50%; transform: translateX(-50%); width: 95%; max-width: 460px; z-index: 999;">
                            <div class="input-group" style="box-shadow: 0 2px 8px rgba(0,0,0,0.15);">
                                <input type="text" id="searchBox" class="form-control" placeholder="Search location..." autocomplete="off">
                                <div class="input-group-append">
                                    <button type="button" id="startPolygon" class="btn btn-primary" title="Draw polygon">
                                        <i class="fas fa-draw-polygon"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="searchResults" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ddd; border-top: none; max-height: 200px; overflow-y: auto; display: none; z-index: 1000; margin-top: 2px; border-radius: 0 0 4px 4px;"></div>
                        </div>
                        <div style="position: absolute; bottom: 20px; left: 20px; z-index: 999; background: rgba(255,255,255,0.9); padding: 6px 12px; border-radius: 4px; font-size: 12px; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">
                            <span><span style="display:inline-block;width:12px;height:12px;background:#f97316;border-radius:2px;margin-right:6px;"></span>Other zones</span>
                            <span class="ml-3"><span style="display:inline-block;width:12px;height:12px;background:#2563eb;border-radius:2px;margin-right:6px;"></span>New drawing</span>
                        </div>
                    </div>
                    <input type="hidden" name="coordinates" id="coordinates">
                </div>
            </form>
        </div>
    </div>

    <!-- Zones DataTable -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.All Zones') }}</h6>
            <div class="d-flex">
                <a href="#" class="btn btn-sm btn-success mr-2" data-toggle="modal" data-target="#importUnitModal">
                    <i class="fas fa-file-import fa-sm"></i> {{ translate('messages.Import') }}
                </a>
                <div class="dropdown mr-2">
                    <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-file-export fa-sm"></i> {{ translate('messages.Export') }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="exportDropdown">
                        <a class="dropdown-item" href="#" id="exportCSV">
                            <i class="fas fa-file-csv fa-sm fa-fw text-gray-400"></i> {{ translate('messages.CSV') }}
                        </a>
                        <a class="dropdown-item" href="#" id="exportPDF">
                            <i class="fas fa-file-pdf fa-sm fa-fw text-gray-400"></i> {{ translate('messages.PDF') }}
                        </a>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-filter fa-sm"></i> {{ translate('messages.Filter') }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="filterDropdown">
                        <div class="dropdown-header">{{ translate('messages.Filter By:') }}</div>
                        <a class="dropdown-item" href="#" data-filter="type">
                            <i class="fas fa-tags fa-sm fa-fw text-gray-400"></i> {{ translate('messages.Type') }}
                        </a>
                        <a class="dropdown-item" href="#" data-filter="status">
                            <i class="fas fa-toggle-on fa-sm fa-fw text-gray-400"></i> {{ translate('messages.Status') }}
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" id="resetFilters">
                            <i class="fas fa-undo fa-sm fa-fw text-gray-400"></i> {{ translate('messages.Reset Filters') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ translate('messages.ID') }}</th>
                            <th>{{ translate('messages.Zone Name') }}</th>
                            <th>{{ translate('messages.total_markets') }}</th>
                            <th>{{ translate('messages.Status') }}</th>
                            <th>{{ translate('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($zones as $zone)
                            <tr>
                                <td>{{ $zone->id }}</td>
                                <td>{{ $zone->name }}</td>
                                <td>{{ $zone->markets->count() }}</td>
                                <td>
                                    @if($zone->is_active == 1)
                                        <span class="badge badge-success">{{ translate('messages.Active') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ translate('messages.Inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.zones.edit', $zone->id) }}" class="btn btn-primary btn-circle btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form id="delete-zone-{{ $zone->id }}" action="" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="formAlert('delete-zone-{{ $zone->id }}', '{{ translate('messages.Want to delete this zone?') }}')" class="btn btn-danger btn-circle btn-sm delete-zone">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">{{ translate('messages.No data found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(method_exists($zones, 'links'))
        <div class="d-flex justify-content-end">
            {{ $zones->links() }}
        </div>
    @endif
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
// Zone Map Configuration
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
        newPolygon: '#2563eb',
        otherZones: '#f97316'
    }
};

// Initialize zone map for create page
window.__otherZones = @json($otherZonesCoords ?? []);

$(function() {
    const zoneMap = new ZoneMapCreate('map', window.__otherZones);
    zoneMap.init();
});

/**
 * Zone Map for Create Page
 * Handles map initialization, polygon drawing, and zone visualization
 */
class ZoneMapCreate {
    constructor(mapId, otherZones = []) {
        this.mapId = mapId;
        this.otherZones = otherZones;
        this.map = null;
        this.drawnItems = null;
        this.otherZonesLayer = null;
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
        this.renderOtherZones();
    }

    getUiElements() {
        return {
            coordinates: $('#coordinates'),
            clearBtn: $('#clearPolygon'),
            searchInput: $('#searchBox'),
            searchResults: $('#searchResults'),
            startPolygon: $('#startPolygon')
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
        this.otherZonesLayer = new L.FeatureGroup();
        this.drawnItems = new L.FeatureGroup();
        this.map.addLayer(this.otherZonesLayer);
        this.map.addLayer(this.drawnItems);
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
                        color: ZoneMapConfig.colors.newPolygon,
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
                featureGroup: this.drawnItems,
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
            if (!this.drawControl?._toolbars?.draw?._modes?.polygon?.handler) {
                return;
            }
            this.drawnItems.clearLayers();
            this.drawControl._toolbars.draw._modes.polygon.handler.enable();
        });
    }

    registerDrawHandlers() {
        this.map.on(L.Draw.Event.CREATED, (event) => {
            this.drawnItems.clearLayers();
            this.drawnItems.addLayer(event.layer);
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
            this.storePolygon([]);
        });
    }

    setupClearButton() {
        this.ui.clearBtn.on('click', (e) => {
            e.preventDefault();
            this.drawnItems.clearLayers();
            this.storePolygon([]);
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

    renderOtherZones() {
        if (!this.otherZones.length) return;

        let bounds;
        
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

            if (!bounds) {
                bounds = polygon.getBounds();
            } else {
                bounds.extend(polygon.getBounds());
            }
        });

        if (bounds) {
            this.map.fitBounds(bounds, { padding: [20, 20] });
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
}
</script>
@endpush