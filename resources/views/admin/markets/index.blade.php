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
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in p-3" aria-labelledby="filterDropdown" style="min-width: 300px;">
                        <h6 class="dropdown-header">{{ translate('messages.Filter Markets') }}</h6>
                        <form id="filterFormDropdown">
                            <div class="mb-2">
                                <label for="filterLocationDropdown" class="form-label small">{{ translate('messages.Location') }}</label>
                                <select class="form-control form-control-sm" id="filterLocationDropdown">
                                    <option value="" selected>{{ translate('messages.All Locations') }}</option>
                                    <option>{{ translate('messages.Dhaka') }}</option>
                                    <option>{{ translate('messages.Chittagong') }}</option>
                                    <option>{{ translate('messages.Sylhet') }}</option>
                                    <option>{{ translate('messages.Rajshahi') }}</option>
                                    <option>{{ translate('messages.Khulna') }}</option>
                                    <option>{{ translate('messages.Barisal') }}</option>
                                    <option>{{ translate('messages.Rangpur') }}</option>
                                    <option>{{ translate('messages.Mymensingh') }}</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="filterTypeDropdown" class="form-label small">{{ translate('messages.Market Type') }}</label>
                                <select class="form-control form-control-sm" id="filterTypeDropdown">
                                    <option value="" selected>{{ translate('messages.All Types') }}</option>
                                    <option>{{ translate('messages.Retail Market') }}</option>
                                    <option>{{ translate('messages.Wholesale Market') }}</option>
                                    <option>{{ translate('messages.Farmers Market') }}</option>
                                    <option>{{ translate('messages.Supermarket') }}</option>
                                    <option>{{ translate('messages.Local Shop') }}</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="filterStatusDropdown" class="form-label small">{{ translate('messages.Status') }}</label>
                                <select class="form-control form-control-sm" id="filterStatusDropdown">
                                    <option value="" selected>{{ translate('messages.All Status') }}</option>
                                    <option value="Active">{{ translate('messages.Active') }}</option>
                                    <option value="Inactive">{{ translate('messages.Inactive') }}</option>
                                    <option value="Pending">{{ translate('messages.Under Review') }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="filterSortDropdown" class="form-label small">{{ translate('messages.Sort By') }}</label>
                                <select class="form-control form-control-sm" id="filterSortDropdown">
                                    <option value="latest" selected>{{ translate('messages.Latest') }}</option>
                                    <option value="name_asc">{{ translate('messages.Name: A to Z') }}</option>
                                    <option value="name_desc">{{ translate('messages.Name: Z to A') }}</option>
                                    <option value="rating_high">{{ translate('messages.Rating: High to Low') }}</option>
                                    <option value="rating_low">{{ translate('messages.Rating: Low to High') }}</option>
                                </select>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-sm btn-secondary mr-2" id="resetFiltersDropdownBtn">
                                    <i class="fas fa-undo fa-sm"></i> {{ translate('messages.Reset') }}
                                </button>
                                <button type="button" class="btn btn-sm btn-primary" id="applyFiltersDropdownBtn">
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
                        <td>
                            @if($market->image)
                                <img src="{{ asset('public/storage/markets/' . $market->image_path) }}" alt="{{ $market->name }}" class="img-fluid" width="100" height="100">
                            @else
                                <img src="{{ asset('public/storage/markets/default.png') }}" alt="{{ $market->name }}" class="img-fluid">
                            @endif
                        </td>
                        <td>{{ $market->name }}</td>
                        <td>{{ Str::limit($market->address, 20) }}</td>
                        <td>{{ $market->type }}</td>
                                                    <td>
                                {{ $market->rating }} <br>
                                <span class="text-muted">({{ $market->rating_count ?? 0 }} {{ Str::plural('review', $market->rating_count ?? 0) }})</span>
                            </td>
                        <td>
                            @if($market->is_active === 'active')
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