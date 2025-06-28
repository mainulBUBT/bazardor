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
                <div class="col-md-6 mb-3">
                    <label for="zoneName">{{ translate('messages.name') }}</label>
                    <input type="text" class="form-control" name="name" id="zoneName" placeholder="{{ translate('messages.enter_zone_name') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <div class="custom-control custom-switch" style="padding-top: 35px;">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" checked>
                            <label class="custom-control-label" for="is_active">{{translate('messages.active')}}</label>
                        </div>
                    </div>
                </div>
                <div class="col-12 text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus-circle mr-1"></i>{{ translate('messages.add_zone') }}
                    </button>
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
                            <th>{{ translate('messages.Markets') }}</th>
                            <th>{{ translate('messages.Status') }}</th>
                            <th>{{ translate('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($zones as $zone)
                            <tr>
                                <td>{{ $zone->id }}</td>
                                <td>{{ $zone->name }}</td>
                                <td>{{ $zone->markets->pluck('name')->implode(', ') }}</td>
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