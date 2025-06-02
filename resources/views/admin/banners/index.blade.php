@extends('layouts.admin.app')

@section('title', 'Banner Management')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Banner Management</h1>
        <a href="{{ route('admin.banners.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Add New Banner
        </a>
    </div>

    <!-- DataTables Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">All Banners</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                    aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Banner Actions:</div>
                    <a class="dropdown-item" href="{{ route('admin.banners.create') }}">
                        <i class="fas fa-plus fa-sm fa-fw mr-2 text-gray-400"></i>
                        Add New Banner
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" id="exportCSV">
                        <i class="fas fa-download fa-sm fa-fw mr-2 text-gray-400"></i>
                        Export to CSV
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Position</th>
                            <th>Duration</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($banners as $banner)
                        <tr>
                            <td>{{ $banner->id }}</td>
                            <td>
                                <img src="{{ asset('storage/' . $banner->image_path) }}" 
                                     alt="{{ $banner->title }}" 
                                     class="banner-image">
                            </td>
                            <td>{{ $banner->title }}</td>
                            <td>{{ ucfirst($banner->type) }}</td>
                            <td>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input toggle-status" 
                                           id="status_{{ $banner->id }}"
                                           data-id="{{ $banner->id }}"
                                           {{ $banner->is_active ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="status_{{ $banner->id }}"></label>
                                </div>
                            </td>
                            <td>{{ $banner->position }}</td>
                            <td>
                                @if($banner->start_date || $banner->end_date)
                                    {{ optional($banner->start_date)->format('Y-m-d') ?? 'Any' }}
                                    to
                                    {{ optional($banner->end_date)->format('Y-m-d') ?? 'Any' }}
                                @else
                                    Always
                                @endif
                            </td>
                            <td>
                                <div class="dropdown no-arrow">
                                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink_{{ $banner->id }}"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                                        aria-labelledby="dropdownMenuLink_{{ $banner->id }}">
                                        <a class="dropdown-item" href="{{ route('admin.banners.edit', $banner) }}">
                                            <i class="fas fa-edit fa-sm fa-fw mr-2 text-gray-400"></i>
                                            Edit
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger delete-banner" href="#" data-id="{{ $banner->id }}">
                                            <i class="fas fa-trash fa-sm fa-fw mr-2 text-gray-400"></i>
                                            Delete
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .banner-image {
        max-width: 150px;
        max-height: 50px;
        object-fit: contain;
    }
    .custom-switch .custom-control-input:checked ~ .custom-control-label::before {
        background-color: #1cc88a;
        border-color: #1cc88a;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable with custom options
        const table = $('#dataTable').DataTable({
            order: [[4, 'asc']], // Sort by position by default
            pageLength: 10,
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
        });

        // Handle status toggle
        $('.toggle-status').on('change', function() {
            const $switch = $(this);
            const id = $switch.data('id');
            const isActive = $switch.prop('checked');

            $.ajax({
                url: `/admin/banners/${id}/toggle-status`,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    is_active: isActive
                },
                success: function(response) {
                    toastr.success('Banner status updated successfully');
                },
                error: function() {
                    toastr.error('Error updating banner status');
                    $switch.prop('checked', !isActive);
                }
            });
        });

        // Handle banner deletion
        $('.delete-banner').on('click', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "This banner will be deleted. This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/admin/banners/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function() {
                            const row = table.row($(`[data-id="${id}"]`).closest('tr'));
                            row.remove().draw();
                            toastr.success('Banner deleted successfully');
                        },
                        error: function() {
                            toastr.error('Error deleting banner');
                        }
                    });
                }
            });
        });

        // Handle CSV export
        $('#exportCSV').on('click', function(e) {
            e.preventDefault();
            window.location.href = '';
        });
    });
</script>
@endpush
