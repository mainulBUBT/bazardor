@extends('layouts.admin.app')
@section('title', translate('messages.Contribution Analytics'))

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">{{ translate('messages.Contribution Analytics') }}</h1>
            <p class="mb-0 text-muted">{{ translate('messages.Track volunteer and user submission trends') }}</p>
        </div>
        <div class="btn-group">
            <a href="?period=7" class="btn btn-sm btn-outline-primary {{ $period == 7 ? 'active' : '' }}">7 Days</a>
            <a href="?period=30" class="btn btn-sm btn-outline-primary {{ $period == 30 ? 'active' : '' }}">30 Days</a>
            <a href="?period=90" class="btn btn-sm btn-outline-primary {{ $period == 90 ? 'active' : '' }}">90 Days</a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Submissions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($statusBreakdown->sum()) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Review</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($statusBreakdown['pending'] ?? 0) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Approval Rate</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $statusBreakdown->sum() > 0 ? number_format(($statusBreakdown['approved'] ?? 0) / $statusBreakdown->sum() * 100, 1) : 0 }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Avg Approval Time</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($avgApprovalTime ?? 0, 1) }}h</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Contributors -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Top Contributors') }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ translate('messages.Contributor') }}</th>
                                    <th class="text-center">{{ translate('messages.Total') }}</th>
                                    <th class="text-center">{{ translate('messages.Approved') }}</th>
                                    <th class="text-center">{{ translate('messages.Rate') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($topContributors as $contributor)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $contributor->user?->name ?? translate('messages.Unknown') }}</strong>
                                            <div class="small text-muted">{{ $contributor->user?->email }}</div>
                                        </td>
                                        <td class="text-center">{{ $contributor->total_contributions }}</td>
                                        <td class="text-center">{{ $contributor->approved_count }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-success">
                                                {{ $contributor->total_contributions > 0 ? number_format($contributor->approved_count / $contributor->total_contributions * 100, 0) : 0 }}%
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">{{ translate('messages.No contributors found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Contributions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Recent Pending') }}</h6>
                    <a href="{{ route('admin.contributions.index', ['status' => 'pending']) }}" class="btn btn-sm btn-primary">
                        {{ translate('messages.View All') }}
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ translate('messages.Product') }}</th>
                                    <th>{{ translate('messages.Market') }}</th>
                                    <th>{{ translate('messages.Price') }}</th>
                                    <th>{{ translate('messages.Submitted') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pendingContributions as $contribution)
                                    <tr>
                                        <td>{{ Str::limit($contribution->product?->name ?? 'N/A', 20) }}</td>
                                        <td>{{ Str::limit($contribution->market?->name ?? 'N/A', 20) }}</td>
                                        <td>{{ number_format($contribution->submitted_price, 2) }}</td>
                                        <td>{{ $contribution->created_at?->diffForHumans() }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">{{ translate('messages.No pending contributions') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
