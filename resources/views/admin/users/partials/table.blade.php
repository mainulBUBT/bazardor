<!-- Users Table Card -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">{{ $role == 'user' ? translate('messages.users_list') : ($role == 'volunteer' ? translate('messages.volunteers_list') : translate('messages.moderators_list')) }}</h6>
        <div class="d-flex">
            @if($role == 'user')
                <a href="{{ route('admin.users.create', ['role' => 'user']) }}" class="btn btn-sm btn-primary mr-2">
                    <i class="fas fa-user-plus fa-sm"></i> {{ translate('messages.add_user') }}
                </a>
            @elseif($role == 'volunteer')
                <a href="" class="btn btn-sm btn-primary mr-2">
                    <i class="fas fa-user-plus fa-sm"></i> {{ translate('messages.add_volunteer') }}
                </a>
            @elseif($role == 'moderator')
                <a href="" class="btn btn-sm btn-primary mr-2">
                    <i class="fas fa-user-plus fa-sm"></i> {{ translate('messages.add_moderator') }}
                </a>
            @endif
            <a href="#" class="btn btn-sm btn-success mr-2">
                <i class="fas fa-file-import fa-sm"></i> {{ translate('messages.import') }}
            </a>
            <div class="dropdown mr-2">
                <button class="btn btn-sm btn-info dropdown-toggle" type="button" id="exportDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-file-export fa-sm"></i> {{ translate('messages.export') }}
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="exportDropdown">
                    <a class="dropdown-item" href="#" id="exportCSV">
                        <i class="fas fa-file-csv fa-sm fa-fw text-gray-400"></i> {{ translate('messages.csv') }}
                    </a>
                    <a class="dropdown-item" href="#" id="exportPDF">
                        <i class="fas fa-file-pdf fa-sm fa-fw text-gray-400"></i> {{ translate('messages.pdf') }}
                    </a>
                </div>
            </div>
            <div class="dropdown mr-2">
                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-filter fa-sm"></i> {{ translate('messages.filter') }}
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="filterDropdown">
                    <div class="dropdown-header">{{ translate('messages.filter_by') }}:</div>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-user-tag fa-sm fa-fw text-gray-400"></i> {{ translate('messages.role') }}
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-toggle-on fa-sm fa-fw text-gray-400"></i> {{ translate('messages.status') }}
                    </a>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-check-circle fa-sm fa-fw text-gray-400"></i> {{ translate('messages.verification') }}
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-undo fa-sm fa-fw text-gray-400"></i> {{ translate('messages.reset_filters') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="usersTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>{{ translate('messages.user') }}</th>
                        <th>{{ translate('messages.role') }}</th>
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
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $user->image_path ? asset('storage/app/public/user/' . $user->image_path) : asset('assets/admin/img/undraw_profile.svg') }}"
                                         onerror="this.src='{{ asset('assets/admin/img/undraw_profile.svg') }}'"
                                         class="user-avatar mr-2" alt="User Image">
                                    <div>
                                        <div class="font-weight-bold">{{ $user->first_name }} {{ $user->last_name }}</div>
                                        <div class="small text-muted">@{{ $user->username ?? Str::slug($user->first_name . $user->last_name) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($user->role->value === 'volunteer')
                                    <span class="badge badge-volunteer">{{ translate('messages.volunteer') }}</span>
                                    <div class="small mt-1">
                                        <span class="points-badge">
                                            <i class="fas fa-star fa-sm"></i> {{ $user->points ?? 0 }} pts
                                        </span>
                                    </div>
                                @elseif($user->role->value === 'admin')
                                    <span class="badge badge-admin">{{ translate('messages.admin') }}</span>
                                @else
                                    <span class="badge badge-user">{{ translate('messages.user') }}</span>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>
                                <span class="badge badge-{{ $user->status == 'active' ? 'success' : ($user->status == 'pending' ? 'warning' : 'secondary') }}">
                                    {{ translate('messages.' . $user->status) }}
                                </span>
                            </td>
                            <td>{{ $user->created_at->format('Y-m-d') }}</td>
                            <td>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-circle btn-sm" title="{{ translate('messages.edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-info btn-circle btn-sm" title="{{ translate('messages.view_profile') }}">
                                    <i class="fas fa-user"></i>
                                </a>
                                @if($user->status == 'pending')
                                    <a href="#" class="btn btn-success btn-circle btn-sm" title="{{ translate('messages.verify') }}">
                                        <i class="fas fa-check"></i>
                                    </a>
                                @else
                                    <a href="#" class="btn btn-danger btn-circle btn-sm" title="{{ translate('messages.block') }}">
                                        <i class="fas fa-ban"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">{{ translate('messages.no_users_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>