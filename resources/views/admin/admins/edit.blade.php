@extends('layouts.admin.app')

@section('title', translate('messages.Edit Admin'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ translate('messages.Edit Admin') }}</h1>
        <p class="mb-0 text-muted">{{ translate('messages.Update administrator details and access.') }}</p>
    </div>
    <a href="{{ route('admin.admins.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="fas fa-arrow-left fa-sm"></i> {{ translate('messages.Back to list') }}
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Admin Details') }}</h6>
        <span class="badge badge-light">{{ translate('messages.Last updated') }}: {{ $admin->updated_at->format('M d, Y H:i') }}</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.admins.update', $admin->id) }}">
            @csrf
            @method('PUT')
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="name">{{ translate('messages.Name') }} <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $admin->name) }}" required>
                    @error('name')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="email">{{ translate('messages.Email') }} <span class="text-danger">*</span></label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $admin->email) }}" required>
                    @error('email')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="password">{{ translate('messages.Password') }} <small class="text-muted">({{ translate('messages.Leave blank to keep current') }})</small></label>
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror">
                    @error('password')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    <label for="password_confirmation">{{ translate('messages.Confirm Password') }}</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror">
                    @error('password_confirmation')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="role">{{ translate('messages.Role') }}</label>
                    <select id="role" name="role" class="form-control @error('role') is-invalid @enderror">
                        <option value="">{{ translate('messages.Select a role') }}</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->name }}" {{ old('role', $admin->getRoleNames()->first()) == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group col-md-6">
                    <div class="custom-control custom-switch mt-4 pt-2">
                        <input class="custom-control-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $admin->is_active) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">{{ translate('messages.Active') }}</label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <a href="{{ route('admin.admins.index') }}" class="btn btn-outline-secondary mr-2">{{ translate('messages.Cancel') }}</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> {{ translate('messages.Update') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

