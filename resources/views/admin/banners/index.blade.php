@extends('layouts.admin.app')

@section('title', translate('messages.Banner Management'))

@section('content')
    <!-- Page Heading -->                                       
    <h1 class="h3 mb-2 text-gray-800">{{ translate('messages.Banners') }}</h1>
    <p class="mb-4">{{ translate('messages.Manage all banners and promotional content displayed on the platform.') }}</p>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.All Banners') }}</h6>
            <div class="d-flex">
                <a href="{{ route('admin.banners.create') }}" class="btn btn-sm btn-primary mr-2">
                    <i class="fas fa-plus fa-sm"></i> {{ translate('messages.Add New Banner') }}
                </a>
                <a href="#" class="btn btn-sm btn-success mr-2" id="exportBannersBtn">
                    <i class="fas fa-download fa-sm"></i> {{ translate('messages.Export') }}
                </a>
                <div class="dropdown">
                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-filter fa-sm"></i> {{ translate('messages.Filter') }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="filterDropdown">
                        <a class="dropdown-item" href="#" data-filter="status" data-value="active">{{ translate('messages.By Status: Active') }}</a>
                        <a class="dropdown-item" href="#" data-filter="status" data-value="inactive">{{ translate('messages.By Status: Inactive') }}</a>
                        <a class="dropdown-item" href="#" data-filter="status" data-value="scheduled">{{ translate('messages.By Status: Scheduled') }}</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" data-filter="clear">{{ translate('messages.Clear Filters') }}</a>
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
                            <th>{{ translate('messages.Title') }}</th>
                            <th>{{ translate('messages.URL') }}</th>
                            <th>{{ translate('messages.Status') }}</th>
                            <th>{{ translate('messages.Start Date') }}</th>
                            <th>{{ translate('messages.End Date') }}</th>
                            <th>{{ translate('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($banners as $banner)
                        <tr>
                            <td>{{ $banner->id }}</td>
                            <td><img src="{{ $banner->image_path }}" alt="Banner 1" class="banner-image"></td>
                            <td>{{ $banner->title }}</td>
                            <td><a href="{{ $banner->url }}" target="_blank">{{ Str::limit($banner->url, 15) }}</a></td>
                            <td>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" onclick='statusAlert(this)' data-url="{{ route('admin.banners.status', $banner->id) }}" class="custom-control-input toggle-status" id="bannerStatus-{{ $banner->id }}" name="{{ $banner->id }}" {{ $banner->is_active ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="bannerStatus-{{ $banner->id }}"></label>
                                </div>
                            </td>
                            <td>{{ Carbon\Carbon::parse($banner->start_date)->format('d-m-Y') }}</td>
                            <td>{{ Carbon\Carbon::parse($banner->end_date)->format('d-m-Y') }}</td>
                            <td>
                                <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-info btn-circle btn-sm" title="{{ translate('messages.Edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.banners.destroy', $banner->id) }}" class="btn btn-danger btn-circle btn-sm" title="{{ translate('messages.Delete') }}">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">{{ translate('messages.No banners found') }}</td>
                            </tr>   
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')

@endpush
