@extends('layouts.admin.app')
@section('title', translate('messages.Contributions'))

@section('content')
    <h1 class="h3 mb-3 text-gray-800">{{ translate('messages.Contributions') }}</h1>
    <p class="mb-4">{{ translate('messages.Review volunteer and user submissions across markets.') }}</p>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ translate('messages.Total Contributions') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ translate('messages.Pending Approval') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['pending']) }}</div>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ translate('messages.Approved Contributions') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['approved']) }}</div>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">{{ translate('messages.Active Contributors') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['contributors']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Recent Contributions') }}</h6>
            <div class="btn-group btn-group-sm">
                <a href="{{ route('admin.contributions.index') }}" class="btn btn-outline-secondary {{ request('status') ? '' : 'active' }}">
                    {{ translate('messages.All') }}
                </a>
                <a href="{{ route('admin.contributions.index', ['status' => 'pending']) }}" class="btn btn-outline-warning {{ request('status') === 'pending' ? 'active' : '' }}">
                    {{ translate('messages.Pending') }}
                </a>
                <a href="{{ route('admin.contributions.index', ['status' => 'approved']) }}" class="btn btn-outline-success {{ request('status') === 'approved' ? 'active' : '' }}">
                    {{ translate('messages.Approved') }}
                </a>
                <a href="{{ route('admin.contributions.index', ['status' => 'rejected']) }}" class="btn btn-outline-danger {{ request('status') === 'rejected' ? 'active' : '' }}">
                    {{ translate('messages.Rejected') }}
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ translate('messages.Contributor') }}</th>
                            <th>{{ translate('messages.Market') }}</th>
                            <th>{{ translate('messages.Product') }}</th>
                            <th>{{ translate('messages.Submitted Price') }}</th>
                            <th>{{ translate('messages.Status') }}</th>
                            <th>{{ translate('messages.Received') }}</th>
                            <th class="text-right">{{ translate('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($contributions as $contribution)
                            <tr>
                                <td>{{ $loop->iteration + ($contributions->currentPage() - 1) * $contributions->perPage() }}</td>
                                <td>
                                    <strong>{{ $contribution->user?->name ?? translate('messages.Anonymous') }}</strong>
                                    <div class="small text-muted">{{ $contribution->user?->email }}</div>
                                </td>
                                <td>{{ $contribution->market?->name ?? translate('messages.Unknown Market') }}</td>
                                <td>{{ $contribution->product?->name ?? translate('messages.Unknown Product') }}</td>
                                <td>{{ number_format($contribution->submitted_price, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $contribution->status === 'approved' ? 'success' : ($contribution->status === 'rejected' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($contribution->status) }}
                                    </span>
                                </td>
                                <td>{{ $contribution->created_at?->diffForHumans() }}</td>
                                <td class="text-right">
                                    @if ($contribution->status === 'pending')
                                        <form action="{{ route('admin.contributions.approve', $contribution) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">{{ translate('messages.Approve') }}</button>
                                        </form>
                                        <form action="{{ route('admin.contributions.reject', $contribution) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ translate('messages.Reject') }}</button>
                                        </form>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    {{ translate('messages.No contributions found for the selected filters.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $contributions->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection
