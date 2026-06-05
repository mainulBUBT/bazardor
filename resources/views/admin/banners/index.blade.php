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
                <div class="dropdown mr-2">
                    <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="exportDropdown"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-file-export fa-sm"></i> {{ translate('messages.Export') }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                        aria-labelledby="exportDropdown">
                        <a class="dropdown-item" href="{{ route('admin.banners.export', ['format' => 'csv']) }}">
                            <i class="fas fa-file-csv fa-sm fa-fw text-gray-400"></i> {{ translate('messages.CSV') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.banners.export', ['format' => 'xlsx']) }}">
                            <i class="fas fa-file-excel fa-sm fa-fw text-gray-400"></i> {{ translate('messages.Excel') }}
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.banners.export', ['format' => 'pdf']) }}">
                            <i class="fas fa-file-pdf fa-sm fa-fw text-gray-400"></i> {{ translate('messages.PDF') }}
                        </a>
                    </div>
                </div>
                <div class="dropdown mr-2">
                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="filterDropdown"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-filter fa-sm"></i> {{ translate('messages.Filter') }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in p-3"
                        aria-labelledby="filterDropdown" style="min-width: 280px;">
                        <form id="filterForm">
                            <div class="mb-2">
                                <label for="filterFeatured" class="form-label small">{{ translate('messages.Featured') }}</label>
                                <select class="form-control form-control-sm" id="filterFeatured" name="is_featured">
                                    <option value="">{{ translate('messages.All') }}</option>
                                    <option value="1" {{ request('is_featured') === '1' ? 'selected' : '' }}>
                                        {{ translate('messages.Featured') }}</option>
                                    <option value="0" {{ request('is_featured') === '0' ? 'selected' : '' }}>
                                        {{ translate('messages.Not Featured') }}</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="filterStatus"
                                    class="form-label small">{{ translate('messages.Status') }}</label>
                                <select class="form-control form-control-sm" id="filterStatus" name="is_active">
                                    <option value="">{{ translate('messages.All Status') }}</option>
                                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>
                                        {{ translate('messages.Active') }}</option>
                                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>
                                        {{ translate('messages.Inactive') }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="filterSort" class="form-label small">{{ translate('messages.Sort By') }}</label>
                                <select class="form-control form-control-sm" id="filterSort" name="sort">
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>
                                        {{ translate('messages.Latest') }}</option>
                                    <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>
                                        {{ translate('messages.Title: A to Z') }}</option>
                                    <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>
                                        {{ translate('messages.Title: Z to A') }}</option>
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
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{ translate('messages.ID') }}</th>
                            <th>{{ translate('messages.Image') }}</th>
                            <th>{{ translate('messages.Title') }}</th>
                            <th>{{ translate('messages.Link') }}</th>
                            <th>{{ translate('messages.Status') }}</th>
                            <th>{{ translate('messages.Featured') }}</th>
                            <th>{{ translate('messages.Start Date') }}</th>
                            <th>{{ translate('messages.End Date') }}</th>
                            <th>{{ translate('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($banners as $banner)
                            <tr>
                                <td>{{ $banner->id }}</td>
                                <td><img src="{{ $banner->image_full_url }}" alt="Banner" class="banner-image"></td>
                                <td>{{ $banner->title }}</td>
                                <td>
                                    @if($banner->link)
                                        <a href="{{ $banner->link }}" target="_blank">{{ Str::limit($banner->link, 20) }}</a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" onclick='statusAlert(this)'
                                            data-url="{{ route('admin.banners.status', $banner->id) }}"
                                            class="custom-control-input toggle-status" id="bannerStatus-{{ $banner->id }}"
                                            name="{{ $banner->id }}" {{ $banner->is_active ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="bannerStatus-{{ $banner->id }}"></label>
                                    </div>
                                </td>
                                <td>
                                    @if($banner->is_featured)
                                        <span class="badge badge-warning">{{ translate('messages.Featured') }}</span>
                                    @else
                                        <span class="badge badge-light text-dark">{{ translate('messages.General') }}</span>
                                    @endif
                                </td>
                                <td>{{ $banner->start_date ? Carbon\Carbon::parse($banner->start_date)->format('d-m-Y') : '-' }}</td>
                                <td>{{ $banner->end_date ? Carbon\Carbon::parse($banner->end_date)->format('d-m-Y') : '-' }}</td>
                                <td>
                                    <div class="d-flex flex-nowrap align-items-center">
                                        <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-primary btn-circle btn-sm mr-1" title="{{ translate('messages.edit') }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-circle btn-sm"
                                                onclick="return confirm('Are you sure?')" title="{{ translate('messages.delete') }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">{{ translate('messages.No banners found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $banners->appends(request()->query())->links() }}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {
            // Apply filters button
            $('#applyFiltersBtn').on('click', function () {
                const isFeatured = $('#filterFeatured').val();
                const isActive = $('#filterStatus').val();
                const sort = $('#filterSort').val();

                const params = new URLSearchParams();

                if (isFeatured !== '') {
                    params.set('is_featured', isFeatured);
                }

                if (isActive !== '') {
                    params.set('is_active', isActive);
                }

                if (sort && sort !== 'latest') {
                    params.set('sort', sort);
                }

                window.location.href = '?' + params.toString();
            });

            // Reset filters button
            $('#resetFiltersBtn').on('click', function () {
                window.location.href = window.location.pathname;
            });
        });
    </script>
@endpush
