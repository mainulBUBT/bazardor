<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row align-items-center">
            <div class="col-md-4">
                <h6 class="m-0 font-weight-bold text-primary">{{ ucfirst($type ?? 'Users') }} {{ translate('messages.list') }}</h6>
            </div>
            <div class="col-md-8">
                <div class="d-flex justify-content-end align-items-center">
                    <form action="{{ url()->current() }}" method="GET" class="mr-2">
                        <div class="input-group">
                            <input type="search" name="search" class="form-control form-control-sm" placeholder="{{ translate('messages.search_by_name_or_email') }}" value="{{ request('search') }}">
                            <input type="hidden" name="role" value="{{ $type ?? 'user' }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary btn-sm" type="submit">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> {{ translate('messages.add_new_user') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-borderless mb-0">
                <thead class="thead-light">
                    <tr>
                        <th class="border-0">{{ translate('messages.user') }}</th>
                        <th class="border-0">{{ translate('messages.contact_info') }}</th>
                        <th class="border-0">{{ translate('messages.role') }}</th>
                        <th class="border-0 text-center">{{ translate('messages.status') }}</th>
                        <th class="border-0 text-center">{{ translate('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $user->image_path ? asset('storage/app/public/user/' . $user->image_path) : asset('assets/admin/img/undraw_profile.svg') }}" 
                                         onerror="this.src='{{ asset('assets/admin/img/undraw_profile.svg') }}'" 
                                         class="img-profile rounded-circle mr-3" alt="User Image" width="50" height="50">
                                    <div>
                                        <span class="d-block font-weight-bold">{{ $user->first_name }} {{ $user->last_name }}</span>
                                        <small class="text-muted">{{ translate('messages.joined') }}: {{ $user->created_at->format('d M Y') }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div><i class="fas fa-envelope-open-text fa-fw mr-2 text-gray-400"></i>{{ $user->email }}</div>
                                <div><i class="fas fa-phone-alt fa-fw mr-2 text-gray-400"></i>{{ $user->phone }}</div>
                            </td>
                            <td>
                                <span class="badge badge-pill badge-info">{{ translate('messages.' . $user->role->value) }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge badge-pill badge-{{ $user->status == 'active' ? 'success' : 'warning' }}">{{ translate('messages.' . $user->status) }}</span>
                            </td>
                            <td class="text-center">
                                <a href="#" class="btn btn-info btn-sm" title="{{translate('messages.edit')}}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="#" class="btn btn-danger btn-sm" title="{{translate('messages.delete')}}">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">{{ translate('messages.no_users_found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-footer d-flex justify-content-end">
        {!! $users->links() !!}
    </div>
    @endif
</div>
