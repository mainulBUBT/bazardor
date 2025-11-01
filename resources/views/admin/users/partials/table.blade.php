<!-- Users Table Card (Consistent with Markets Table) -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">
            {{ translate('messages.users_list') }}
        </h6>
        <div class="d-flex">
            <a href="{{ route('admin.users.create', ['userType' => 'user']) }}" class="btn btn-sm btn-primary mr-2">
                <i class="fas fa-user-plus fa-sm"></i> {{ translate('messages.add_user') }}
            </a>
            <a href="#" class="btn btn-sm btn-success mr-2">
                <i class="fas fa-file-import fa-sm"></i> {{ translate('messages.import') }}
            </a>
            <div class="dropdown mr-2">
                <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-file-export fa-sm"></i> {{ translate('messages.export') }}
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="exportDropdown">
                    <a class="dropdown-item" href="#" id="exportCSV">
                        <i class="fas fa-file-csv fa-sm fa-fw mr-2 text-gray-400"></i> {{ translate('messages.csv') }}
                    </a>
                    <a class="dropdown-item" href="#" id="exportPDF">
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
                            <label for="filterRoleDropdown" class="form-label small">{{ translate('messages.role') }}</label>
                            <select class="form-control form-control-sm" id="filterRoleDropdown">
                                <option value="" selected>{{ translate('messages.all_roles') }}</option>
                                <option value="user">{{ translate('messages.user') }}</option>
                                <option value="volunteer">{{ translate('messages.volunteer') }}</option>
                                <option value="moderator">{{ translate('messages.moderator') }}</option>
                                <option value="admin">{{ translate('messages.admin') }}</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="filterStatusDropdown" class="form-label small">{{ translate('messages.status') }}</label>
                            <select class="form-control form-control-sm" id="filterStatusDropdown">
                                <option value="" selected>{{ translate('messages.all_status') }}</option>
                                <option value="active">{{ translate('messages.active') }}</option>
                                <option value="pending">{{ translate('messages.pending') }}</option>
                                <option value="blocked">{{ translate('messages.blocked') }}</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label for="filterVerificationDropdown" class="form-label small">{{ translate('messages.verification') }}</label>
                            <select class="form-control form-control-sm" id="filterVerificationDropdown">
                                <option value="" selected>{{ translate('messages.all_verification') }}</option>
                                <option value="verified">{{ translate('messages.verified') }}</option>
                                <option value="unverified">{{ translate('messages.unverified') }}</option>
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