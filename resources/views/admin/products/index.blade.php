@extends('layouts.admin.app')
@section('title', translate('messages.Products Management'))
@section('content')

    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">{{ translate('messages.Products') }}</h1>
    <p class="mb-4">{{ translate('messages.Manage all products available on the platform.') }}</p>

    <!-- Products DataTable -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Products List') }}</h6>
            <div class="d-flex">
                <a href="{{ route('admin.products.create') }}" class="btn btn-sm btn-primary mr-2">
                    <i class="fas fa-plus fa-sm"></i> {{ translate('messages.Add New Product') }}
                </a>
                <a href="{{ route('admin.products.bulk-import') }}" class="btn btn-sm btn-success mr-2">
                    <i class="fas fa-file-import fa-sm"></i> {{ translate('messages.Import') }}
                </a>
                <div class="dropdown mr-2">
                    <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-file-export fa-sm"></i> {{ translate('messages.Export') }}
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
                <div class="dropdown mr-2">
                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-filter fa-sm"></i> {{ translate('messages.Filter') }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="filterDropdown">
                        <div class="dropdown-header">{{ translate('messages.Filter By:') }}</div>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-tag fa-sm fa-fw text-gray-400"></i> {{ translate('messages.Category') }}
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-dollar-sign fa-sm fa-fw text-gray-400"></i> {{ translate('messages.Price') }}
                        </a>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-warehouse fa-sm fa-fw text-gray-400"></i> {{ translate('messages.Stock Status') }}
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">
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
                            <th>{{ translate('messages.Image') }}</th>
                            <th>{{ translate('messages.Product Name') }}</th>
                            <th>{{ translate('messages.Category') }}</th>
                            <th>{{ translate('messages.Price') }}</th>
                            <th>{{ translate('messages.Status') }}</th>
                            <th>{{ translate('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>
                                    @if($product->image_path)
                                        <img src="{{ asset('public/storage/products/'.$product->image_path) }}" alt="{{ $product->name }}" class="img-fluid img-thumbnail" width="60">
                                    @else
                                        <img src="{{ asset('adminpanel/img/product-placeholder.png') }}" class="img-fluid img-thumbnail" width="60" alt="placeholder">
                                    @endif
                                </td>
                                <td>{{ $product->name }}</td>
                                <td>{{ optional($product->category)->name }}</td>
                                <td>{{ $product->base_price !== null ? number_format($product->base_price, 2) : '-' }}</td>
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
                                    <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-info btn-circle btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-primary btn-circle btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form id="delete-product-{{ $product->id }}" action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-danger btn-circle btn-sm delete-product" data-form-id="delete-product-{{ $product->id }}" data-message="{{ translate('messages.Want to delete this product?') }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $products->links() }}
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
    });
</script>
@endpush