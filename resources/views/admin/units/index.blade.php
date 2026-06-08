@extends('layouts.admin.app')
@section('title', translate('messages.Units Management'))
@section('content')
@php
    $locales = get_enabled_locales();
    $languages = get_enabled_languages();
    $defaultLocale = get_default_locale();
@endphp

 <!-- Page Heading -->
 <h1 class="h3 mb-2 text-gray-800">{{ translate('messages.Units') }}</h1>
    <p class="mb-4">{{ translate('messages.Manage measurement units for products in your catalog.') }}</p>

    <!-- Unit Form Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Add Unit') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.units.store') }}" method="POST">
                @csrf

                @if(count($locales) > 1)
                <!-- Language Tabs -->
                <ul class="nav nav-tabs mb-3" role="tablist">
                    @foreach($languages as $lang)
                    <li class="nav-item">
                        <a class="nav-link {{ $loop->first ? 'active' : '' }}"
                           data-toggle="tab" href="#lang-{{ $lang['code'] }}" role="tab">
                            {{ strtoupper($lang['code']) }}
                            <small class="text-muted">{{ $lang['code'] === $defaultLocale ? '(Default)' : $lang['name'] }}</small>
                        </a>
                    </li>
                    @endforeach
                </ul>
                <div class="tab-content">
                    @foreach($languages as $lang)
                        @php
                            $locale = $lang['code'];
                            $isDefault = $locale === $defaultLocale;
                            $isActive = $loop->first;
                            $fieldName = $isDefault ? 'name' : "name_{$locale}";
                            $fieldSymbol = $isDefault ? 'symbol' : "symbol_{$locale}";
                        @endphp
                        <div class="tab-pane fade {{ $isActive ? 'show active' : '' }}" id="lang-{{ $locale }}" role="tabpanel">
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label>{{ translate('messages.Unit Name') }} ({{ $lang['name'] }}) @if($isDefault) <span class="text-danger">*</span> @endif</label>
                                    <input type="text" class="form-control" name="{{ $fieldName }}"
                                           {{ $isDefault ? 'required' : '' }}
                                           value="{{ old($fieldName) }}" placeholder="{{ translate('messages.Enter unit name') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>{{ translate('messages.Symbol') }} ({{ $lang['name'] }}) @if($isDefault) <span class="text-danger">*</span> @endif</label>
                                    <input type="text" class="form-control" name="{{ $fieldSymbol }}"
                                           {{ $isDefault ? 'required' : '' }}
                                           value="{{ old($fieldSymbol) }}" placeholder="{{ translate('messages.e.g. kg') }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @else
                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="unitName">{{ translate('messages.Unit Name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="unitName" value="{{ old('name') }}" placeholder="{{ translate('messages.Enter unit name') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="unitSymbol">{{ translate('messages.Symbol') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="symbol" id="unitSymbol" value="{{ old('symbol') }}" placeholder="{{ translate('messages.e.g. kg') }}" required>
                    </div>
                </div>
                @endif

                <div class="form-row">
                    <div class="col-md-4 mb-3">
                        <label for="unitType">{{ translate('messages.Type') }}</label>
                        <select class="form-control select2" name="unit_type" id="unitType" data-placeholder="{{ translate('messages.Select unit type') }}">
                            <option value="">{{ translate('messages.Select unit type') }}</option>
                            <option value="weight">{{ translate('messages.Weight') }}</option>
                            <option value="volume">{{ translate('messages.Volume') }}</option>
                            <option value="length">{{ translate('messages.Length') }}</option>
                            <option value="count">{{ translate('messages.Count') }}</option>
                            <option value="other">{{ translate('messages.Other') }}</option>
                        </select>
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus-circle mr-1"></i>{{ translate('messages.Add Unit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Units DataTable -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.All Units') }}</h6>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <div class="w-auto" style="min-width: 180px;">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                            </div>
                            <input type="text" class="form-control" id="searchInput" placeholder="{{ translate('messages.Search by name...') }}" value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="d-flex flex-nowrap align-items-center gap-1">
                        <a href="{{ route('admin.units.import-export') }}" class="btn btn-sm btn-success" title="{{ translate('messages.Import') }}">
                            <i class="fas fa-file-import"></i>
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="{{ translate('messages.Export') }}">
                                <i class="fas fa-file-export"></i>
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
                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="{{ translate('messages.Filter') }}">
                                <i class="fas fa-filter"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in p-3" aria-labelledby="filterDropdown" style="min-width: 250px;">
                                <form id="filterForm">
                                    <div class="mb-2">
                                        <label for="filterType" class="form-label small">{{ translate('messages.Type') }}</label>
                                        <select class="form-control form-control-sm select2" id="filterType" name="unit_type" data-placeholder="{{ translate('messages.All Types') }}">
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
                                        <select class="form-control form-control-sm select2" id="filterStatus" name="is_active" data-placeholder="{{ translate('messages.All Status') }}">
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
                                    <div class="d-flex flex-nowrap align-items-center">
                                        <a href="{{ route('admin.units.edit', $unit->id) }}" class="btn btn-primary btn-circle btn-sm mr-1" title="{{ translate('messages.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form id="delete-unit-{{ $unit->id }}" action="{{ route('admin.units.destroy', $unit->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-circle btn-sm delete-unit" data-form-id="delete-unit-{{ $unit->id }}" data-message="{{ translate('messages.Want to delete this unit?') }}" title="{{ translate('messages.delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
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

        // Search input with Enter key support
        $('#searchInput').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                applyFilters();
            }
        });

        // Apply filters function
        function applyFilters() {
            const search = $('#searchInput').val();
            const unitType = $('#filterType').val();
            const isActive = $('#filterStatus').val();
            
            const params = new URLSearchParams();
            
            if (search) {
                params.set('search', search);
            }
            
            if (unitType) {
                params.set('unit_type', unitType);
            }
            
            if (isActive !== '') {
                params.set('is_active', isActive);
            }
            
            window.location.href = '?' + params.toString();
        }

        // Apply filters button
        $('#applyFiltersBtn').on('click', function() {
            applyFilters();
        });
        
        // Reset filters button
        $('#resetFiltersBtn').on('click', function() {
            window.location.href = window.location.pathname;
        });
    });
</script>
@endpush