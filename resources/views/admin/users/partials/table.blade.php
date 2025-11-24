<!-- Users Table Card (Consistent with Markets Table) -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            {{ translate('messages.users_list') }}
        </h6>
        <div class="d-flex align-items-center">
            <div class="mr-2" style="min-width: 250px;">
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" class="form-control" id="searchInput" placeholder="{{ translate('messages.Search by name, email...') }}" value="{{ request('search') }}">
                </div>
            </div>
            <a href="{{ route('admin.users.create', ['userType' => $type]) }}" class="btn btn-sm btn-primary mr-2">
                <i class="fas fa-user-plus fa-sm"></i> {{ translate('messages.add_user') }}
            </a>
            <div class="dropdown mr-2">
                <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-file-export fa-sm"></i> {{ translate('messages.export') }}
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="exportDropdown">
                    <a class="dropdown-item" href="{{ route('admin.users.export', array_merge(['format' => 'csv', 'user_type' => $type], request()->query())) }}">
                        <i class="fas fa-file-csv fa-sm fa-fw mr-2 text-gray-400"></i> {{ translate('messages.csv') }}
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.users.export', array_merge(['format' => 'xlsx', 'user_type' => $type], request()->query())) }}">
                        <i class="fas fa-file-excel fa-sm fa-fw mr-2 text-gray-400"></i> {{ translate('messages.excel') }}
                    </a>
                    <a class="dropdown-item" href="{{ route('admin.users.export', array_merge(['format' => 'pdf', 'user_type' => $type], request()->query())) }}">
                        <i class="fas fa-file-pdf fa-sm fa-fw mr-2 text-gray-400"></i> {{ translate('messages.pdf') }}
                    </a>
                </div>
            </div>
            <div class="dropdown">
                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-filter fa-sm"></i> {{ translate('messages.filter') }}
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in p-3" aria-labelledby="filterDropdown" style="min-width: 300px;">
                    <h6 class="dropdown-header">{{ translate('messages.filter_users') }}</h6>
                    <form id="filterFormDropdown">
                        <div class="mb-2">
                            <label for="filterStatusDropdown" class="form-label small">{{ translate('messages.status') }}</label>
                            <select class="form-control form-control-sm" id="filterStatusDropdown" name="status">
                                <option value="">{{ translate('messages.all_status') }}</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ translate('messages.active') }}</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>{{ translate('messages.pending') }}</option>
                                <option value="blocked" {{ request('status') === 'blocked' ? 'selected' : '' }}>{{ translate('messages.blocked') }}</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="filterVerificationDropdown" class="form-label small">{{ translate('messages.verification') }}</label>
                            <select class="form-control form-control-sm" id="filterVerificationDropdown" name="is_verified">
                                <option value="">{{ translate('messages.all_verification') }}</option>
                                <option value="verified" {{ request('is_verified') === 'verified' ? 'selected' : '' }}>{{ translate('messages.verified') }}</option>
                                <option value="unverified" {{ request('is_verified') === 'unverified' ? 'selected' : '' }}>{{ translate('messages.unverified') }}</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="filterSortDropdown" class="form-label small">{{ translate('messages.Sort By') }}</label>
                            <select class="form-control form-control-sm" id="filterSortDropdown" name="sort">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>{{ translate('messages.Latest') }}</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>{{ translate('messages.Name: A to Z') }}</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>{{ translate('messages.Name: Z to A') }}</option>
                                <option value="joined_asc" {{ request('sort') == 'joined_asc' ? 'selected' : '' }}>{{ translate('messages.Joined: Oldest First') }}</option>
                                <option value="joined_desc" {{ request('sort') == 'joined_desc' ? 'selected' : '' }}>{{ translate('messages.Joined: Newest First') }}</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-sm btn-secondary mr-2" id="resetFiltersDropdownBtn">
                                <i class="fas fa-undo fa-sm"></i> {{ translate('messages.reset') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-primary" id="applyFiltersDropdownBtn">
                                <i class="fas fa-filter fa-sm"></i> {{ translate('messages.apply') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="usersTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>{{ translate('messages.id') }}</th>
                        <th>{{ translate('messages.image') }}</th>
                        <th>{{ translate('messages.user') }}</th>
                        <th>{{ translate('messages.email') }}</th>
                        <th>{{ translate('messages.phone') }}</th>
                        <th>{{ translate('messages.status') }}</th>
                        <th>{{ translate('messages.joined_date') }}</th>
                        <th>{{ translate('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td class="text-center">
                                <div class="user-thumbnail">
                                    @if($user->image_path)
                                        <img src="{{ asset('storage/app/public/users/' . $user->image_path) }}" alt="{{ $user->first_name }}" class="img-thumbnail" style="width: 48px; height: 48px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('assets/admin/img/undraw_profile.svg') }}" alt="{{ $user->first_name }}" class="img-thumbnail" style="width: 48px; height: 48px; object-fit: cover;">
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="font-weight-bold">{{ $user->first_name }} {{ $user->last_name }}</div>
                                <div class="small text-muted">{{'@'.$user->username ?? translate('messages.not_created') }}</div>
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>
                                @if($user->is_active == 0)
                                    <span class="badge badge-success">{{ translate('messages.active') }}</span>
                                @elseif($user->is_active == 1)
                                    <span class="badge badge-warning">{{ translate('messages.pending') }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ translate('messages.unknown') }}</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-sm btn-info" title="{{ translate('messages.view_profile') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-primary" title="{{ translate('messages.edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="{{ translate('messages.delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">{{ translate('messages.no_users_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(isset($users) && method_exists($users, 'links'))
    <div class="d-flex justify-content-end">
        {{ $users->appends(request()->query())->links() }}
    </div>
@endif

@push('scripts')
<script>
    $(document).ready(function() {
        const userType = '{{ $type }}';
        
        // Search input with Enter key support
        $('#searchInput').on('keypress', function(e) {
            if (e.which === 13) { // Enter key
                applyFilters();
            }
        });

        // Apply filters function
        function applyFilters() {
            const search = $('#searchInput').val();
            const status = $('#filterStatusDropdown').val();
            const isVerified = $('#filterVerificationDropdown').val();
            const sort = $('#filterSortDropdown').val();
            
            const params = new URLSearchParams();
            
            // Always preserve user_type
            params.set('user_type', userType);
            
            if (search) {
                params.set('search', search);
            }
            
            if (status) {
                params.set('status', status);
            }
            
            if (isVerified) {
                params.set('is_verified', isVerified);
            }
            
            if (sort && sort !== 'latest') {
                params.set('sort', sort);
            }
            
            window.location.href = '?' + params.toString();
        }

        // Apply filters button
        $('#applyFiltersDropdownBtn').on('click', function() {
            applyFilters();
        });
        
        // Reset filters button
        $('#resetFiltersDropdownBtn').on('click', function() {
            window.location.href = '{{ route('admin.users.index') }}?user_type=' + userType;
        });
    });
</script>
@endpush