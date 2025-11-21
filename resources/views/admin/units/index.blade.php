@extends('layouts.admin.app')
@section('title', translate('messages.Units Management'))
@section('content')

 <!-- Page Heading -->
 <h1 class="h3 mb-2 text-gray-800">{{ translate('messages.Units') }}</h1>
    <p class="mb-4">{{ translate('messages.Manage measurement units for products in your catalog.') }}</p>

    <!-- Unit Form Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Add Unit') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.units.store') }}" class="row" method="POST">
                @csrf
                <div class="col-md-4 mb-3">
                    <label for="unitName">{{ translate('messages.Unit Name') }}</label>
                    <input type="text" class="form-control" name="name" id="unitName" placeholder="{{ translate('messages.Enter unit name') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="unitSymbol">{{ translate('messages.Symbol') }}</label>
                    <input type="text" class="form-control" name="symbol" id="unitSymbol" placeholder="{{ translate('messages.e.g. kg') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="unitType">{{ translate('messages.Type') }}</label>
                    <select class="form-control" name="unit_type" id="unitType">
                        <option selected value="">{{ translate('messages.Select unit type') }}</option>
                        <option value="weight">{{ translate('messages.Weight') }}</option>
                        <option value="volume">{{ translate('messages.Volume') }}</option>
                        <option value="length">{{ translate('messages.Length') }}</option>
                        <option value="count">{{ translate('messages.Count') }}</option>
                        <option value="other">{{ translate('messages.Other') }}</option>
                    </select>
                </div>
                <div class="col-12 text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus-circle mr-1"></i>{{ translate('messages.Add Unit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Units DataTable -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.All Units') }}</h6>
            <div class="d-flex">
                <a href="{{ route('admin.units.import-export') }}" class="btn btn-sm btn-success mr-2">
                    <i class="fas fa-file-import fa-sm"></i> {{ translate('messages.Import') }}
                </a>
                <div class="dropdown mr-2">
                    <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-file-export fa-sm"></i> {{ translate('messages.Export') }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="exportDropdown">
                        <a class="dropdown-item" href="{{ route('admin.units.export', ['format' => 'csv']) }}">
                            <i class="fas fa-file-csv fa-sm fa-fw text-gray-400"></i> {{ translate('messages.CSV') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.units.export', ['format' => 'xlsx']) }}">
                            <i class="fas fa-file-excel fa-sm fa-fw text-gray-400"></i> {{ translate('messages.Excel') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.units.export', ['format' => 'pdf']) }}">
                            <i class="fas fa-file-pdf fa-sm fa-fw text-gray-400"></i> {{ translate('messages.PDF') }}
                        </a>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-filter fa-sm"></i> {{ translate('messages.Filter') }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in p-3" aria-labelledby="filterDropdown" style="min-width: 250px;">
                        <form id="filterForm">
                            <div class="mb-2">
                                <label for="filterType" class="form-label small">{{ translate('messages.Type') }}</label>
                                <select class="form-control form-control-sm" id="filterType" name="unit_type">
                                    <option value="">{{ translate('messages.All Types') }}</option>
                                    <option value="weight" {{ request('unit_type') == 'weight' ? 'selected' : '' }}>{{ translate('messages.Weight') }}</option>
                                    <option value="volume" {{ request('unit_type') == 'volume' ? 'selected' : '' }}>{{ translate('messages.Volume') }}</option>
                                    <option value="length" {{ request('unit_type') == 'length' ? 'selected' : '' }}>{{ translate('messages.Length') }}</option>
                                    <option value="count" {{ request('unit_type') == 'count' ? 'selected' : '' }}>{{ translate('messages.Count') }}</option>
                                    <option value="other" {{ request('unit_type') == 'other' ? 'selected' : '' }}>{{ translate('messages.Other') }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="filterStatus" class="form-label small">{{ translate('messages.Status') }}</label>
                                <select class="form-control form-control-sm" id="filterStatus" name="is_active">
                                    <option value="">{{ translate('messages.All Status') }}</option>
                                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>{{ translate('messages.Active') }}</option>
                                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>{{ translate('messages.Inactive') }}</option>
                                </select>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-sm btn-secondary mr-2" id="resetFiltersBtn">
                                    <i class="fas fa-undo fa-sm"></i> {{ translate('messages.Reset') }}
                                </button>
                                <button type="button" class="btn btn-sm btn-primary" id="applyFiltersBtn">
                                    <i class="fas fa-filter fa-sm"></i> {{ translate('messages.Apply') }}
                                </button>
                            </div>
                        </form>
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
                            <th>{{ translate('messages.Unit Name') }}</th>
                            <th>{{ translate('messages.Symbol') }}</th>
                            <th>{{ translate('messages.Type') }}</th>
                            <th>{{ translate('messages.Status') }}</th>
                            <th>{{ translate('messages.Created') }}</th>
                            <th>{{ translate('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($units as $unit)
                            <tr>
                                <td>{{ $unit->id }}</td>
                                <td>{{ $unit->name }}</td>
                                <td>{{ $unit->symbol }}</td>
                                <td>{{ ucfirst($unit->unit_type) }}</td>
                                <td>
                                    @if($unit->is_active == 1)
                                        <span class="badge badge-success">{{ translate('messages.Active') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ translate('messages.Inactive') }}</span>
                                    @endif
                                </td>
                                <td>{{ $unit->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <a href="{{ route('admin.units.edit', $unit->id) }}" class="btn btn-primary btn-circle btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form id="delete-unit-{{ $unit->id }}" action="{{ route('admin.units.destroy', $unit->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-circle btn-sm delete-unit" data-form-id="delete-unit-{{ $unit->id }}" data-message="{{ translate('messages.Want to delete this unit?') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">{{ translate('messages.No data found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if(method_exists($units, 'links'))
        <div class="d-flex justify-content-end">
            {{ $units->links() }}
        </div>
    @endif

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Delete unit handler
        $('.delete-unit').on('click', function() {
            let formId = $(this).data('form-id');
            let message = $(this).data('message');
            formAlert(formId, message);
        });

        // Apply filters button
        $('#applyFiltersBtn').on('click', function() {
            const unitType = $('#filterType').val();
            const isActive = $('#filterStatus').val();
            
            const params = new URLSearchParams();
            
            if (unitType) {
                params.set('unit_type', unitType);
            }
            
            if (isActive !== '') {
                params.set('is_active', isActive);
            }
            
            window.location.href = '?' + params.toString();
        });
        
        // Reset filters button
        $('#resetFiltersBtn').on('click', function() {
            window.location.href = window.location.pathname;
        });
    });
</script>
@endpush