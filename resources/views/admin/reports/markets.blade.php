@extends('layouts.admin.app')
@section('title', translate('messages.Market Analytics'))

@section('content')
    <h1 class="h3 mb-3 text-gray-800">{{ translate('messages.Market Analytics') }}</h1>
    <p class="mb-4">{{ translate('messages.Overview of market distribution and status') }}</p>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Markets</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-store fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Active Markets</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['active']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Inactive Markets</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['inactive']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">With Prices</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['with_prices']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Markets by Division -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Markets by Division') }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ translate('messages.Division') }}</th>
                                    <th class="text-right">{{ translate('messages.Count') }}</th>
                                    <th class="text-right">{{ translate('messages.Percentage') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($marketsByDivision as $item)
                                    <tr>
                                        <td>{{ $item->division ?? translate('messages.Not Specified') }}</td>
                                        <td class="text-right">{{ number_format($item->count) }}</td>
                                        <td class="text-right">{{ number_format($item->count / max($stats['total'], 1) * 100, 1) }}%</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">{{ translate('messages.No data available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Markets by Type -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Markets by Type') }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ translate('messages.Type') }}</th>
                                    <th class="text-right">{{ translate('messages.Count') }}</th>
                                    <th class="text-right">{{ translate('messages.Percentage') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($marketsByType as $item)
                                    <tr>
                                        <td>{{ $item->type ?? translate('messages.Not Specified') }}</td>
                                        <td class="text-right">{{ number_format($item->count) }}</td>
                                        <td class="text-right">{{ number_format($item->count / max($stats['total'], 1) * 100, 1) }}%</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">{{ translate('messages.No data available') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Markets -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Recently Added Markets') }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead>
                        <tr>
                            <th>{{ translate('messages.Market Name') }}</th>
                            <th>{{ translate('messages.Type') }}</th>
                            <th>{{ translate('messages.Zone') }}</th>
                            <th>{{ translate('messages.Status') }}</th>
                            <th>{{ translate('messages.Added') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentMarkets as $market)
                            <tr>
                                <td>{{ $market->name }}</td>
                                <td>{{ $market->type }}</td>
                                <td>{{ $market->zone?->name ?? translate('messages.No Zone') }}</td>
                                <td>
                                    <span class="badge badge-{{ $market->is_active ? 'success' : 'warning' }}">
                                        {{ $market->is_active ? translate('messages.Active') : translate('messages.Inactive') }}
                                    </span>
                                </td>
                                <td>{{ $market->created_at?->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">{{ translate('messages.No markets found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
