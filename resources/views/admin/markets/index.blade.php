@extends('layouts.admin.app')
@section('title', translate('messages.Markets Management'))
@section('content')

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">{{ translate('messages.Markets') }}</h1>
    <p class="mb-4">{{ translate('messages.Manage all markets available on the platform.') }}</p>

    <!-- Markets DataTable -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Markets List') }}</h6>
            <div class="d-flex">
                <a href="{{ route('admin.markets.create') }}" class="btn btn-sm btn-primary mr-2">
                    <i class="fas fa-plus fa-sm"></i> {{ translate('messages.Add New Market') }}
                </a>
                <div class="dropdown mr-2">
                    <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-download fa-sm"></i> {{ translate('messages.Export') }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="exportDropdown">
                        <a class="dropdown-item" href="#" id="exportCSV">
                            <i class="fas fa-file-csv fa-sm fa-fw mr-2 text-gray-400"></i> {{ translate('messages.CSV') }}
                        </a>
                        <a class="dropdown-item" href="#" id="exportPDF">
                            <i class="fas fa-file-pdf fa-sm fa-fw mr-2 text-gray-400"></i> {{ translate('messages.PDF') }}
                        </a>
                    </div>
                </div>
                <!-- CHANGED Filter Button to Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-filter fa-sm"></i> {{ translate('messages.Filter') }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in p-3" aria-labelledby="filterDropdown" style="min-width: 280px;">
                        <form id="filterForm">
                            <div class="mb-2">
                                <label for="filterDivision" class="form-label small">{{ translate('messages.Division') }}</label>
                                <select class="form-control form-control-sm" id="filterDivision" name="division">
                                    <option value="">{{ translate('messages.All Divisions') }}</option>
                                    <option value="Dhaka" {{ request('division') == 'Dhaka' ? 'selected' : '' }}>Dhaka</option>
                                    <option value="Chattogram" {{ request('division') == 'Chattogram' ? 'selected' : '' }}>Chattogram</option>
                                    <option value="Rajshahi" {{ request('division') == 'Rajshahi' ? 'selected' : '' }}>Rajshahi</option>
                                    <option value="Khulna" {{ request('division') == 'Khulna' ? 'selected' : '' }}>Khulna</option>
                                    <option value="Barishal" {{ request('division') == 'Barishal' ? 'selected' : '' }}>Barishal</option>
                                    <option value="Sylhet" {{ request('division') == 'Sylhet' ? 'selected' : '' }}>Sylhet</option>
                                    <option value="Rangpur" {{ request('division') == 'Rangpur' ? 'selected' : '' }}>Rangpur</option>
                                    <option value="Mymensingh" {{ request('division') == 'Mymensingh' ? 'selected' : '' }}>Mymensingh</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="filterType" class="form-label small">{{ translate('messages.Type') }}</label>
                                <select class="form-control form-control-sm" id="filterType" name="type">
                                    <option value="">{{ translate('messages.All Types') }}</option>
                                    <option value="Wholesale Market" {{ request('type') == 'Wholesale Market' ? 'selected' : '' }}>{{ translate('messages.Wholesale Market') }}</option>
                                    <option value="Retail Market" {{ request('type') == 'Retail Market' ? 'selected' : '' }}>{{ translate('messages.Retail Market') }}</option>
                                    <option value="Supermarket" {{ request('type') == 'Supermarket' ? 'selected' : '' }}>{{ translate('messages.Supermarket') }}</option>
                                    <option value="Local Shop" {{ request('type') == 'Local Shop' ? 'selected' : '' }}>{{ translate('messages.Local Shop') }}</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="filterStatus" class="form-label small">{{ translate('messages.Status') }}</label>
                                <select class="form-control form-control-sm" id="filterStatus" name="is_active">
                                    <option value="">{{ translate('messages.All Status') }}</option>
                                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>{{ translate('messages.Active') }}</option>
                                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>{{ translate('messages.Inactive') }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="filterSort" class="form-label small">{{ translate('messages.Sort By') }}</label>
                                <select class="form-control form-control-sm" id="filterSort" name="sort">
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>{{ translate('messages.Latest') }}</option>
                                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>{{ translate('messages.Name: A to Z') }}</option>
                                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>{{ translate('messages.Name: Z to A') }}</option>
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
                            <th>{{ translate('messages.Image') }}</th>
                            <th>{{ translate('messages.Market Name') }}</th>
                            <th>{{ translate('messages.Location') }}</th>
                            <th>{{ translate('messages.Zone') }}</th>
                            <th>{{ translate('messages.Type') }}</th>
                            <th>{{ translate('messages.Rating') }}</th>
                            <th>{{ translate('messages.Status') }}</th>
                            <th>{{ translate('messages.Actions') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                       @foreach($markets as $market)
                       <tr>
                        <td>{{ $market->id }}</td>
                        <td class="text-center">
                            <div class="market-thumbnail">
                                @if($market->image_path)
                                    <img src="{{ asset('public/storage/markets/' . $market->image_path) }}" alt="{{ $market->name }}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                @else
                                    <img src="{{ asset('public/storage/markets/default.png') }}" alt="{{ $market->name }}" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                @endif
                            </div>
                        </td>
                        <td>{{ $market->name }}</td>
                        <td>{{ Str::limit($market->address, 20) }}</td>
                        <td>{{ $market->zone ? $market->zone->name : translate('messages.No Zone') }}</td>
                        <td>{{ $market->type }}</td>
                        <td>
                            {{ $market->rating }} <br>
                            <span class="text-muted">({{ $market->rating_count ?? 0 }} {{ Str::plural('review', $market->rating_count ?? 0) }})</span>
                        </td>
                        <td>
                            @if($market->is_active == '1')
                                <span class="badge badge-success">{{ translate('messages.Active') }}</span>
                            @else
                                <span class="badge badge-danger">{{ translate('messages.Inactive') }}</span>
                            @endif
                        </td>   
                        <td>
                            <a href="{{ route('admin.markets.show', $market->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                            </a>    
                            <a href="{{ route('admin.markets.edit', $market->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>        
                            <form action="{{ route('admin.markets.destroy', $market->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                       </tr>
                       @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        // Apply filters button
        $('#applyFiltersBtn').on('click', function() {
            const division = $('#filterDivision').val();
            const type = $('#filterType').val();
            const isActive = $('#filterStatus').val();
            const sort = $('#filterSort').val();
            
            const params = new URLSearchParams();
            
            if (division) {
                params.set('division', division);
            }
            
            if (type) {
                params.set('type', type);
            }
            
            if (isActive !== '') {
                params.set('is_active', isActive);
            }
            
            if (sort && sort !== 'latest') {
                params.set('sort', sort);
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