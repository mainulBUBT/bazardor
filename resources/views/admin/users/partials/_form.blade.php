@php
    $imageUrl = (isset($user) && $user->image_path)
        ? asset('storage/app/public/user/' . $user->image_path)
        : asset('assets/admin/img/undraw_profile.svg');
@endphp
@csrf
<div class="row">
    <div class="col-lg-8">
        <!-- Basic Information Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{translate('messages.basic_information')}}</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group required">
                            <label for="first_name">{{translate('messages.first_name')}}</label>
                            <input type="text" class="form-control" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name ?? '') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group required">
                            <label for="last_name">{{translate('messages.last_name')}}</label>
                            <input type="text" class="form-control" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name ?? '') }}" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group required">
                            <label for="email">{{translate('messages.email_address')}}</label>
                            <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group required">
                            <label for="phone">{{translate('messages.phone_number')}}</label>
                            <input type="tel" class="form-control" name="phone" id="phone" value="{{ old('phone', $user->phone ?? '') }}" required>
                            <small class="form-text text-muted">{{translate('messages.phone_format')}}</small>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group required">
                            <label for="role">{{translate('messages.user_role')}}</label>
                            <select class="form-control" name="role" id="role" required>
                                @foreach(App\Enums\Role::cases() as $role)
                                    <option value="{{ $role->value }}" {{ isset($user) && $user->role->value == $role->value ? 'selected' : '' }}>
                                        {{ translate('messages.' . $role->value) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ isset($user) ? '' : 'required' }}">
                            <label for="password">{{translate('messages.password')}}</label>
                            <div class="position-relative">
                                <input type="password" class="form-control" name="password" id="password" {{ isset($user) ? '' : 'required' }}>
                                <span class="password-toggle" onclick="togglePassword('password')">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                            <small class="form-text text-muted">{{translate('messages.minimum_8_characters')}}</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group {{ isset($user) ? '' : 'required' }}">
                            <label for="password_confirmation">{{translate('messages.confirm_password')}}</label>
                            <div class="position-relative">
                                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" {{ isset($user) ? '' : 'required' }}>
                                <span class="password-toggle" onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Profile Picture Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{translate('messages.profile_picture')}}</h6>
            </div>
            <div class="card-body">
                <div class="avatar-upload">
                    <div class="avatar-edit">
                        <input type='file' name="image" id="imageUpload" accept=".png, .jpg, .jpeg" />
                        <label for="imageUpload"></label>
                    </div>
                    <div class="avatar-preview">
                        <div id="imagePreview" style="background-image: url('{{ $imageUrl }}');"></div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <small class="text-muted">{{translate('messages.image_upload_note')}}</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="row mb-4">
    <div class="col-lg-8">
        <a href="{{route('admin.users.index')}}" class="btn btn-secondary">{{translate('messages.cancel')}}</a>
        <button type="submit" class="btn btn-primary">
            @if(isset($user))
                <i class="fas fa-save"></i> {{translate('messages.update_user')}}
            @else
                <i class="fas fa-user-plus"></i> {{translate('messages.create_user')}}
            @endif
        </button>
    </div>
</div>
