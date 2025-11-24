@extends('layouts.admin.app')
@section('title', translate('messages.Categories Management'))
@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ translate('messages.Categories') }}</h1>
</div>
<p class="mb-4">{{ translate('messages.Manage product categories in your catalog.') }}</p>

<!-- Categories DataTable -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.All Categories') }}</h6>
        <div class="d-flex align-items-center">
            <div class="mr-2" style="min-width: 250px;">
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" class="form-control" id="searchInput" placeholder="{{ translate('messages.Search by name...') }}" value="{{ request('search') }}">
                </div>
            </div>
            <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-primary mr-2">
                <i class="fas fa-plus fa-sm"></i> {{ translate('messages.Add New Category') }}
            </a>          
            <div class="dropdown mr-2">
                <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-file-export fa-sm"></i> {{ translate('messages.Export') }}
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="exportDropdown">
                    <a class="dropdown-item" href="{{ route('admin.categories.export', ['format' => 'csv']) }}">
                        <i class="fas fa-file-csv fa-sm fa-fw text-gray-400"></i> {{ translate('messages.CSV') }}
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.categories.export', ['format' => 'xlsx']) }}">
                        <i class="fas fa-file-excel fa-sm fa-fw text-gray-400"></i> {{ translate('messages.Excel') }}
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.categories.export', ['format' => 'pdf']) }}">
                        <i class="fas fa-file-pdf fa-sm fa-fw text-gray-400"></i> {{ translate('messages.PDF') }}
                    </a>
                </div>
            </div>
            <div class="dropdown">
                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-filter fa-sm"></i> {{ translate('messages.Filter') }}
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in p-3" aria-labelledby="filterDropdown" style="min-width: 280px;">
                    <form id="filterForm">
                        <div class="mb-2">
                            <label for="filterStatus" class="form-label small">{{ translate('messages.Status') }}</label>
                            <select class="form-control form-control-sm" id="filterStatus" name="is_active">
                                <option value="">{{ translate('messages.All Status') }}</option>
                                <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>{{ translate('messages.Active') }}</option>
                                <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>{{ translate('messages.Inactive') }}</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="filterParent" class="form-label small">{{ translate('messages.Parent Category') }}</label>
                            <select class="form-control form-control-sm" id="filterParent" name="parent_id">
                                <option value="">{{ translate('messages.All Categories') }}</option>
                                <option value="root" {{ request('parent_id') === 'root' ? 'selected' : '' }}>{{ translate('messages.Root Categories') }}</option>
                                @foreach($parentCategories as $parent)
                                    <option value="{{ $parent->id }}" {{ request('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="filterSort" class="form-label small">{{ translate('messages.Sort By') }}</label>
                            <select class="form-control form-control-sm" id="filterSort" name="sort">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>{{ translate('messages.Latest') }}</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>{{ translate('messages.Name: A to Z') }}</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>{{ translate('messages.Name: Z to A') }}</option>
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
                        <th>{{ translate('messages.Name') }}</th>
                        <th>{{ translate('messages.Slug') }}</th>
                        <th>{{ translate('messages.Image') }}</th>
                        <th>{{ translate('messages.Parent') }}</th>
                        <th>{{ translate('messages.Status') }}</th>
                        <th>{{ translate('messages.Created') }}</th>
                        <th>{{ translate('messages.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories ?? [] as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->slug }}</td>
                            <td>
                                @if($category->image_path)
                                    <img src="{{ asset('public/storage/categories/' . $category->image_path) }}" alt="Category Image" class="img-thumbnail img-fluid" style="width: 80px; height: 40px; object-fit: cover;">
                                @else
                                    <img src="{{ asset('public/storage/categories/default.png') }}" alt="Category Image" class="img-thumbnail img-fluid" style="width: 80px; height: 40px; object-fit: cover;">
                                @endif
                            </td>   
                            <td>
                                {{ $category->parent ? $category->parent->name : translate('messages.None') }}
                            </td>
                            <td>
                                @if($category->is_active === true)
                                    <span class="badge badge-success">{{ translate('messages.Active') }}</span>
                                @else
                                    <span class="badge badge-danger">{{ translate('messages.Inactive') }}</span>
                                @endif
                            </td>
                            <td>{{ $category->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-primary btn-circle btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form id="delete-category-{{ $category->id }}" action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="formAlert('delete-category-{{ $category->id }}', '{{ translate('messages.Want to delete this category?') }}')" class="btn btn-danger btn-circle btn-sm delete-category">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">{{ translate('messages.No data found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(isset($categories) && method_exists($categories, 'links'))
    <div class="d-flex justify-content-end">
        {{ $categories->appends(request()->query())->links() }}
    </div>
@endif

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Auto-generate slug from name
        $('#categoryName').on('input', function() {
            const name = $(this).val();
            const slug = name.toLowerCase()
                .replace(/\s+/g, '-')           // Replace spaces with -
                .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                .replace(/^-+/, '')             // Trim - from start of text
                .replace(/-+$/, '');            // Trim - from end of text
            
            // Only update if slug field is empty or was auto-generated
            const currentSlug = $('#categorySlug').val();
            if (!currentSlug || currentSlug === generateSlug($('#categoryName').val().slice(0, -1))) {
                $('#categorySlug').val(slug);
            }
        });

        function generateSlug(text) {
            return text.toLowerCase()
                .replace(/\s+/g, '-')           // Replace spaces with -
                .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                .replace(/^-+/, '')             // Trim - from start of text
                .replace(/-+$/, '');            // Trim - from end of text
        }

        // Search input with Enter key support
        $('#searchInput').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                applyFilters();
            }
        });

        // Apply filters function
        function applyFilters() {
            const search = $('#searchInput').val();
            const isActive = $('#filterStatus').val();
            const parentId = $('#filterParent').val();
            const sort = $('#filterSort').val();
            
            const params = new URLSearchParams(window.location.search);
            
            if (search) {
                params.set('search', search);
            } else {
                params.delete('search');
            }
            
            if (isActive !== '') {
                params.set('is_active', isActive);
            } else {
                params.delete('is_active');
            }
            
            if (parentId) {
                params.set('parent_id', parentId);
            } else {
                params.delete('parent_id');
            }
            
            if (sort && sort !== 'latest') {
                params.set('sort', sort);
            } else {
                params.delete('sort');
            }
            
            // Reset page to 1 when applying new filters
            params.delete('page');

            window.location.href = '?' + params.toString();
        }

        // Apply filters button
        $('#applyFiltersBtn').on('click', function() {
            applyFilters();
        });
        
        // Reset filters button
        $('#resetFiltersBtn').on('click', function() {
            window.location.href = '{{ route('admin.categories.index') }}';
        });
    });
</script>
@endpush
