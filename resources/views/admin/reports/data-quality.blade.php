@extends('layouts.admin.app')
@section('title', translate('messages.Data Quality Report'))

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ translate('messages.Data Quality Report') }}</h1>
            <p class="mb-0 text-muted">{{ translate('messages.Identify incomplete or outdated data') }}</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Incomplete Markets</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['incomplete_markets']) }}</div>
                            <div class="small text-muted">{{ number_format($stats['incomplete_markets'] / max($stats['total_markets'], 1) * 100, 1) }}% of total</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-store-slash fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Products Without Prices</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['products_without_prices']) }}</div>
                            <div class="small text-muted">{{ number_format($stats['products_without_prices'] / max($stats['total_products'], 1) * 100, 1) }}% of total</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tag fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Outdated Prices</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['outdated_prices_count']) }}</div>
                            <div class="small text-muted">&gt;7 days old</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-times fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Contributors</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['active_contributors']) }}</div>
                            <div class="small text-muted">Last 30 days</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Incomplete Markets -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Markets Missing Information') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ translate('messages.Market Name') }}</th>
                            <th>{{ translate('messages.Zone') }}</th>
                            <th>{{ translate('messages.Missing Fields') }}</th>
                            <th>{{ translate('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($incompleteMarkets as $market)
                            <tr>
                                <td>{{ $market->name }}</td>
                                <td>{{ $market->zone?->name ?? translate('messages.No Zone') }}</td>
                                <td>
                                    @if(!$market->phone) <span class="badge badge-warning">Phone</span> @endif
                                    @if(!$market->address) <span class="badge badge-warning">Address</span> @endif
                                    @if(!$market->latitude || !$market->longitude) <span class="badge badge-warning">Location</span> @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.markets.edit', $market->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i> {{ translate('messages.Edit') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">{{ translate('messages.All markets have complete information') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $incompleteMarkets->appends(['products_page' => request('products_page'), 'prices_page' => request('prices_page')])->links() }}
            </div>
        </div>
    </div>

    <!-- Products Without Recent Prices -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Products Without Recent Prices') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ translate('messages.Product Name') }}</th>
                            <th>{{ translate('messages.Category') }}</th>
                            <th>{{ translate('messages.Last Updated') }}</th>
                            <th>{{ translate('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($productsWithoutPrices as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category?->name ?? translate('messages.Uncategorized') }}</td>
                                <td>
                                    @if($product->marketPrices()->exists())
                                        {{ $product->marketPrices()->latest()->first()?->updated_at?->diffForHumans() }}
                                    @else
                                        <span class="text-danger">{{ translate('messages.Never') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> {{ translate('messages.View') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-3">{{ translate('messages.All products have recent prices') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $productsWithoutPrices->appends(['markets_page' => request('markets_page'), 'prices_page' => request('prices_page')])->links() }}
            </div>
        </div>
    </div>
@endsection
