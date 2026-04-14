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
                    <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-file-export fa-sm"></i> {{ translate('messages.Export') }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="exportDropdown">
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
                    <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-filter fa-sm"></i> {{ translate('messages.Filter') }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in p-3" aria-labelledby="filterDropdown" style="min-width: 280px;">
                        <form id="filterForm">
                            <div class="mb-2">
                                <label for="filterType" class="form-label small">{{ translate('messages.Type') }}</label>
                                <select class="form-control form-control-sm" id="filterType" name="type">
                                    <option value="">{{ translate('messages.All Types') }}</option>
                                    <option value="featured" {{ request('type') === 'featured' ? 'selected' : '' }}>{{ translate('messages.Featured') }}</option>
                                    <option value="general" {{ request('type') === 'general' ? 'selected' : '' }}>{{ translate('messages.General') }}</option>
                                </select>
                            </div>
                            <div class="mb-2">
                                <label for="filterStatus" class="form-label small">{{ translate('messages.Status') }}</label>
                                <select class="form-control form-control-sm" id="filterStatus" name="is_active">
                                    <option value="">{{ translate('messages.All Status') }}</option>
                                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>{{ translate('messages.Active') }}</option>
                                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>{{ translate('messages.Inactive') }}</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="filterSort" class="form-label small">{{ translate('messages.Sort By') }}</label>
                                <select class="form-control form-control-sm" id="filterSort" name="sort">
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>{{ translate('messages.Latest') }}</option>
                                    <option value="title_asc" {{ request('sort') == 'title_asc' ? 'selected' : '' }}>{{ translate('messages.Title: A to Z') }}</option>
                                    <option value="title_desc" {{ request('sort') == 'title_desc' ? 'selected' : '' }}>{{ translate('messages.Title: Z to A') }}</option>
                                    <option value="position_asc" {{ request('sort') == 'position_asc' ? 'selected' : '' }}>{{ translate('messages.Position: Low to High') }}</option>
                                    <option value="position_desc" {{ request('sort') == 'position_desc' ? 'selected' : '' }}>{{ translate('messages.Position: High to Low') }}</option>
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
                            <th>{{ translate('messages.Type') }}</th>
                            <th>{{ translate('messages.URL') }}</th>
                            <th>{{ translate('messages.Status') }}</th>
                            <th>{{ translate('messages.Start Date') }}</th>
                            <th>{{ translate('messages.End Date') }}</th>
                            <th>{{ translate('messages.Badge') }}</th>
                            <th>{{ translate('messages.Background') }}</th>
                            <th>{{ translate('messages.Button Text') }}</th>
                            <th>{{ translate('messages.Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($banners as $banner)
                        <tr>
                            <td>{{ $banner->id }}</td>
                            <td><img src="{{ asset('storage/' . $banner->image_path) }}" alt="Banner" class="banner-image"></td>
                            <td>{{ $banner->title }}</td>
                            <td>
                                @if($banner->type === 'featured')
                                    <span class="badge badge-warning">{{ translate('messages.Featured') }}</span>
                                @else
                                    <span class="badge badge-info">{{ translate('messages.Banner') }}</span>
                                @endif
                            </td>
                            <td><a href="{{ $banner->url }}" target="_blank">{{ Str::limit($banner->url, 15) }}</a></td>
                            <td>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" onclick='statusAlert(this)' data-url="{{ route('admin.banners.status', $banner->id) }}" class="custom-control-input toggle-status" id="bannerStatus-{{ $banner->id }}" name="{{ $banner->id }}" {{ $banner->is_active ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="bannerStatus-{{ $banner->id }}"></label>
                                </div>
                            </td>
                            <td>{{ $banner->start_date ? Carbon\Carbon::parse($banner->start_date)->format('d-m-Y') : '-' }}</td>
                            <td>{{ $banner->end_date ? Carbon\Carbon::parse($banner->end_date)->format('d-m-Y') : '-' }}</td>
                            <td>
                                @if($banner->type === 'featured')
                                    @if($banner->badge_text)
                                        <span class="badge badge-{{ $banner->badge_color ?? 'primary' }}">{{ $banner->badge_text }}</span>
                                    @endif
                                    @if($banner->badge_icon)
                                        <i class="{{ $banner->badge_icon }} ml-1"></i>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($banner->type === 'featured')
                                    {{ $banner->badge_background_color ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($banner->type === 'featured')
                                    {{ $banner->button_text ?? '-' }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center">{{ translate('messages.No banners found') }}</td>
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
    $(document).ready(function() {
        // Apply filters button
        $('#applyFiltersBtn').on('click', function() {
            const type = $('#filterType').val();
            const isActive = $('#filterStatus').val();
            const sort = $('#filterSort').val();
            
            const params = new URLSearchParams();
            
            if (type) {
                params.set('type', type);
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
        $('#resetFiltersBtn').on('click', function() {
            window.location.href = window.location.pathname;
        });
    });
</script>
@endpush
