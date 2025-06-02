@extends('layouts.admin.app')
@section('title', translate('messages.Categories Management'))
@section('content')

<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ translate('messages.Categories') }}</h1>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> {{ translate('messages.Add New Category') }}
    </a>
</div>
<p class="mb-4">{{ translate('messages.Manage product categories in your catalog.') }}</p>

<!-- Categories DataTable -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.All Categories') }}</h6>
        <div class="d-flex">
            <a href="#" class="btn btn-sm btn-success mr-2" data-toggle="modal" data-target="#importCategoryModal">
                <i class="fas fa-file-import fa-sm"></i> {{ translate('messages.Import') }}
            </a>
            <div class="dropdown mr-2">
                <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-file-export fa-sm"></i> {{ translate('messages.Export') }}
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="exportDropdown">
                    <a class="dropdown-item" href="#" id="exportCSV">
                        <i class="fas fa-file-csv fa-sm fa-fw text-gray-400"></i> {{ translate('messages.CSV') }}
                    </a>
                    <a class="dropdown-item" href="#" id="exportPDF">
                        <i class="fas fa-file-pdf fa-sm fa-fw text-gray-400"></i> {{ translate('messages.PDF') }}
                    </a>
                </div>
            </div>
            <div class="dropdown">
                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-filter fa-sm"></i> {{ translate('messages.Filter') }}
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="filterDropdown">
                    <div class="dropdown-header">{{ translate('messages.Filter By:') }}</div>
                    <a class="dropdown-item" href="#" data-filter="parent">
                        <i class="fas fa-sitemap fa-sm fa-fw text-gray-400"></i> {{ translate('messages.Parent') }}
                    </a>
                    <a class="dropdown-item" href="#" data-filter="status">
                        <i class="fas fa-toggle-on fa-sm fa-fw text-gray-400"></i> {{ translate('messages.Status') }}
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" id="resetFilters">
                        <i class="fas fa-undo fa-sm fa-fw text-gray-400"></i> {{ translate('messages.Reset Filters') }}
                    </a>
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
                        <th>{{ translate('messages.Icon') }}</th>
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
                                <i class="{{ $category->icon }}"></i>
                            </td>
                            <td>
                                {{ $category->parent ? $category->parent->name : translate('messages.None') }}
                            </td>
                            <td>
                                @if($category->status === 'active')
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
        {{ $categories->links() }}
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
    });
</script>
@endpush
