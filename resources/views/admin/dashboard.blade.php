@extends('layouts.admin.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ translate('messages.Dashboard') }}</h1>
        <a href="{{ route('admin.reports.contributions') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-chart-bar fa-sm text-white-50"></i> {{ translate('messages.View Reports') }}
        </a>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Total Markets -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                {{ translate('messages.Total Markets') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_markets']) }}</div>
                            <div class="small text-muted">{{ number_format($stats['active_markets']) }} {{ translate('messages.active') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-store fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Products -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                {{ translate('messages.Total Products') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_products']) }}</div>
                            <div class="small text-muted">{{ number_format($stats['total_prices']) }} {{ translate('messages.prices tracked') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Contributions -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                {{ translate('messages.Pending Contributions') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['pending_contributions']) }}</div>
                            <div class="small text-muted">{{ number_format($stats['todays_contributions']) }} {{ translate('messages.today') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Users & Volunteers -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                {{ translate('messages.Total Users') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_users']) }}</div>
                            <div class="small text-muted">{{ number_format($stats['total_volunteers']) }} {{ translate('messages.volunteers') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Row -->
    <div class="row">
        <!-- Pending Contributions (Needs Action) -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-exclamation-circle mr-1"></i> {{ translate('messages.Pending Contributions - Needs Review') }}
                    </h6>
                    <a href="{{ route('admin.contributions.index', ['status' => 'pending']) }}" class="btn btn-sm btn-warning">
                        {{ translate('messages.Review All') }}
                    </a>
                </div>
                <div class="card-body">
                    @if($pendingContributions->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                            <p class="mb-0">{{ translate('messages.All caught up! No pending contributions.') }}</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ translate('messages.Contributor') }}</th>
                                        <th>{{ translate('messages.Product') }}</th>
                                        <th>{{ translate('messages.Market') }}</th>
                                        <th class="text-right">{{ translate('messages.Price') }}</th>
                                        <th>{{ translate('messages.Submitted') }}</th>
                                        <th class="text-center">{{ translate('messages.Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pendingContributions as $contribution)
                                        <tr>
                                            <td>{{ Str::limit($contribution->user?->name ?? 'Anonymous', 15) }}</td>
                                            <td>{{ Str::limit($contribution->product?->name ?? 'N/A', 20) }}</td>
                                            <td>{{ Str::limit($contribution->market?->name ?? 'N/A', 20) }}</td>
                                            <td class="text-right">{{ number_format($contribution->submitted_price, 2) }}</td>
                                            <td>{{ $contribution->created_at?->diffForHumans() }}</td>
                                            <td class="text-center">
                                                <form action="{{ route('admin.contributions.approve', $contribution) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-xs btn-success" title="Approve">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.contributions.reject', $contribution) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-xs btn-danger" title="Reject">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Incomplete Markets (Needs Attention) -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-danger">
                        <i class="fas fa-exclamation-triangle mr-1"></i> {{ translate('messages.Incomplete Markets') }}
                    </h6>
                    <a href="{{ route('admin.reports.data-quality') }}" class="btn btn-sm btn-danger btn-sm">
                        {{ translate('messages.View All') }}
                    </a>
                </div>
                <div class="card-body">
                    @if($incompleteMarkets->isEmpty())
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                            <p class="small mb-0">{{ translate('messages.All markets have complete information!') }}</p>
                        </div>
                    @else
                        <ul class="list-group list-group-flush">
                            @foreach ($incompleteMarkets as $market)
                                <li class="list-group-item px-0 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ Str::limit($market->name, 25) }}</strong>
                                            <div class="small text-muted">
                                                @if(!$market->phone) <span class="badge badge-warning badge-sm">Phone</span> @endif
                                                @if(!$market->address) <span class="badge badge-warning badge-sm">Address</span> @endif
                                                @if(!$market->latitude) <span class="badge badge-warning badge-sm">Location</span> @endif
                                            </div>
                                        </div>
                                        <a href="{{ route('admin.markets.edit', $market->id) }}" class="btn btn-xs btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
