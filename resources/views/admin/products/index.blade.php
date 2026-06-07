@extends('layouts.admin.app')
@section('title', translate('messages.Products Management'))
@section('content')

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">{{ translate('messages.Products') }}</h1>
    <p class="mb-4">{{ translate('messages.Manage all products available on the platform.') }}</p>

    <!-- Products DataTable -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
                <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Products List') }}</h6>
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
                        <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-primary" title="{{ translate('messages.Add New Product') }}">
                            <i class="fas fa-plus"></i>
                        </a>
                        <a href="{{ route('admin.products.bulk-import') }}" class="btn btn-sm btn-success" title="{{ translate('messages.Import') }}">
                            <i class="fas fa-file-import"></i>
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="{{ translate('messages.Export') }}">
                                <i class="fas fa-file-export"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="exportDropdown">
                                <a class="dropdown-item" href="{{ route('admin.products.export', ['format' => 'csv']) }}">
                                    <i class="fas fa-file-csv fa-sm fa-fw text-gray-400"></i> {{ translate('messages.CSV') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.products.export', ['format' => 'xlsx']) }}">
                                    <i class="fas fa-file-excel fa-sm fa-fw text-gray-400"></i> {{ translate('messages.Excel') }}
                                </a>
                                <a class="dropdown-item" href="{{ route('admin.products.export', ['format' => 'pdf']) }}">
                                    <i class="fas fa-file-pdf fa-sm fa-fw text-gray-400"></i> {{ translate('messages.PDF') }}
                                </a>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="{{ translate('messages.Filter') }}">
                                <i class="fas fa-filter"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in p-3" aria-labelledby="filterDropdown" style="min-width: 280px;">
                                <form id="filterForm">
                                    <div class="mb-2">
                                        <label for="filterCategory" class="form-label small">{{ translate('messages.Category') }}</label>
                                        <select class="form-control form-control-sm" id="filterCategory" name="category_id">
                                            <option value="">{{ translate('messages.All Categories') }}</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-2">
                                        <label for="filterStatus" class="form-label small">{{ translate('messages.Status') }}</label>
                                        <select class="form-control form-control-sm" id="filterStatus" name="status">
                                            <option value="">{{ translate('messages.All Status') }}</option>
                                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ translate('messages.Active') }}</option>
                                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ translate('messages.Inactive') }}</option>
                                            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>{{ translate('messages.Draft') }}</option>
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
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ translate('messages.ID') }}</th>
                            <th>{{ translate('messages.Image') }}</th>
                            <th>{{ translate('messages.Product Name') }}</th>
                            <th>{{ translate('messages.Category') }}</th>
                            <th>{{ translate('messages.Status') }}</th>
                            <th>{{ translate('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>
                                    <img src="{{ $product->image_full_url }}"
                                         onerror="this.onerror=null;this.src='{{ asset('assets/img/placeholder.png') }}';"
                                         alt="{{ $product->name }}"
                                         style="width:60px;height:60px;object-fit:cover;border-radius:4px;">
                                </td>
                                <td>{{ $product->name }}</td>
                                <td>{{ optional($product->category)->name }}</td>
                                <td>
                                    @if($product->status === 'active')
                                        <span class="badge badge-success">{{ translate('messages.Active') }}</span>
                                    @elseif($product->status === 'inactive')
                                        <span class="badge badge-danger">{{ translate('messages.Inactive') }}</span>
                                    @else
                                        <span class="badge badge-secondary">{{ translate('messages.Draft') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-nowrap align-items-center">
                                        <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-info btn-circle btn-sm mr-1" title="{{ translate('messages.view_details') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary btn-circle btn-sm mr-1" title="{{ translate('messages.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form id="delete-product-{{ $product->id }}" action="{{ route('admin.products.destroy', $product->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-danger btn-circle btn-sm delete-product" data-form-id="delete-product-{{ $product->id }}" data-message="{{ translate('messages.Want to delete this product?') }}" title="{{ translate('messages.delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $products->appends(request()->query())->links() }}
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Handle delete button clicks
        $('.delete-product').on('click', function() {
            const formId = $(this).data('form-id');
            const message = $(this).data('message');
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
            const categoryId = $('#filterCategory').val();
            const status = $('#filterStatus').val();
            const sort = $('#filterSort').val();
            
            const params = new URLSearchParams();
            
            if (search) {
                params.set('search', search);
            }
            
            if (categoryId) {
                params.set('category_id', categoryId);
            }
            
            if (status) {
                params.set('status', status);
            }
            
            if (sort && sort !== 'latest') {
                params.set('sort', sort);
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