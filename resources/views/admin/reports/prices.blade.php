@extends('layouts.admin.app')
@section('title', translate('messages.Price Analytics'))

@section('content')
    <h1 class="h3 mb-3 text-gray-800">{{ translate('messages.Price Analytics') }}</h1>
    <p class="mb-4">{{ translate('messages.Track price trends and market comparisons') }}</p>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Prices</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_prices']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Updated Today</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['updated_today']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Updated This Week</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['updated_this_week']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Products Tracked</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['products_tracked']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Most Expensive Markets -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Most Expensive Markets') }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ translate('messages.Market') }}</th>
                                    <th class="text-right">{{ translate('messages.Avg Price') }}</th>
                                    <th class="text-center">{{ translate('messages.Products') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($expensiveMarkets as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->market?->name ?? translate('messages.Unknown') }}</td>
                                        <td class="text-right">{{ number_format($item->avg_price, 2) }}</td>
                                        <td class="text-center">{{ $item->product_count }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">{{ translate('messages.No data available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Most Volatile Products -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Most Volatile Products') }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ translate('messages.Product') }}</th>
                                    <th class="text-right">{{ translate('messages.Avg Price') }}</th>
                                    <th class="text-right">{{ translate('messages.Std Dev') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($volatileProducts as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->product?->name ?? translate('messages.Unknown') }}</td>
                                        <td class="text-right">{{ number_format($item->avg_price, 2) }}</td>
                                        <td class="text-right">
                                            <span class="badge badge-warning">{{ number_format($item->price_stddev, 2) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">{{ translate('messages.No data available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Price Trends (Last 30 Days) -->
    @if($priceTrends->isNotEmpty())
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Price Trends (Last 30 Days)') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ translate('messages.Date') }}</th>
                            <th class="text-right">{{ translate('messages.Min Price') }}</th>
                            <th class="text-right">{{ translate('messages.Avg Price') }}</th>
                            <th class="text-right">{{ translate('messages.Max Price') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($priceTrends as $trend)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($trend->date)->format('M d, Y') }}</td>
                                <td class="text-right">{{ number_format($trend->min_price, 2) }}</td>
                                <td class="text-right">{{ number_format($trend->avg_price, 2) }}</td>
                                <td class="text-right">{{ number_format($trend->max_price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
@endsection
