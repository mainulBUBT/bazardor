@extends('layouts.admin.app')
@section('title', translate('messages.add_user'))

@section('content')

    <h1 class="h3 mb-2 text-gray-800">{{ translate('messages.add_user') }}</h1>
    <p class="mb-4">{{ translate('messages.Create a new user account by filling out the form below. All fields marked with an asterisk (*) are required.') }}</p>

    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <!-- Basic Information Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.basic_information') }}</h6>
                    </div>
                    <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group required">
                                        <label for="first_name">{{ translate('messages.first_name') }}</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group required">
                                        <label for="last_name">{{ translate('messages.last_name') }}</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group required">
                                        <label for="username">{{ translate('messages.username') }}</label>
                                        <input type="text" class="form-control" id="username" name="username" required>
                                        <small class="form-text text-muted">{{ translate('messages.must_be_unique_and_contain_only_letters_numbers_and_underscores') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group required">
                                        <label for="email">{{ translate('messages.email_address') }}</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group required">
                                        <label for="phone">{{ translate('messages.phone_number') }}</label>
                                        <input type="tel" class="form-control" id="phone" name="phone" required pattern="[0-9]{11}" placeholder="+880">
                                        <small class="form-text text-muted">{{ translate('messages.format_11_digits_number') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="role">{{ translate('messages.role') }}</label>
                                        <select class="form-control" name="role" id="role">
                                            <option value="{{ \App\Enums\UserType::USER->value }}" {{ old('role') == \App\Enums\UserType::USER->value ? 'selected' : '' }}>{{ translate('messages.user') }}</option>
                                            <option value="{{ \App\Enums\UserType::VOLUNTEER->value }}" {{ old('role') == \App\Enums\UserType::VOLUNTEER->value ? 'selected' : '' }}>{{ translate('messages.volunteer') }}</option>
                                            <option value="{{ \App\Enums\UserType::MODERATOR->value }}" {{ old('role') == \App\Enums\UserType::MODERATOR->value ? 'selected' : '' }}>{{ translate('messages.moderator') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="functional_roles">{{ translate('messages.functional_roles') }}</label>
                                        <select class="form-control select2" name="functional_roles[]" id="functional_roles" multiple>
                                            @foreach($functionalRoles as $functionalRole)
                                                <option value="{{ $functionalRole->id }}" {{ in_array($functionalRole->id, old('functional_roles', [])) ? 'selected' : '' }}>{{ $functionalRole->name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-muted">{{ translate('messages.functional_roles_help') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group required">
                                        <label for="password">{{ translate('messages.password') }}</label>
                                        <div class="position-relative">
                                            <input type="password" class="form-control" id="password" name="password" required minlength="8">
                                            <span class="password-toggle" onclick="togglePassword('password')">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                        <small class="form-text text-muted">{{ translate('messages.minimum_8_characters') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group required">
                                        <label for="confirmPassword">{{ translate('messages.confirm_password') }}</label>
                                        <div class="position-relative">
                                            <input type="password" class="form-control" id="confirmPassword" name="password_confirmation" required>
                                            <span class="password-toggle" onclick="togglePassword('confirmPassword')">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>

                <!-- Additional Information Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.additional_information') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dateOfBirth">{{ translate('messages.date_of_birth') }}</label>
                                    <input type="date" class="form-control" id="dateOfBirth" name="dob">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gender">{{ translate('messages.gender') }}</label>
                                    <select class="form-control select2" id="gender" name="gender">
                                        <option value="">{{ translate('messages.select_gender') }}</option>
                                        <option value="male">{{ translate('messages.male') }}</option>
                                        <option value="female">{{ translate('messages.female') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address">{{ translate('messages.address') }}</label>
                            <textarea class="form-control" id="address" name="address" rows="3"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="city">{{ translate('messages.city') }}</label>
                                    <input type="text" class="form-control" id="city" name="city">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="division">{{ translate('messages.division') }}</label>
                                    <select class="form-control select2" id="division" name="division">
                                        <option value="">{{ translate('messages.select_division') }}</option>
                                        @foreach(\App\Enums\Location::getDivisions() as $division)
                                            <option value="{{ $division }}">{{ $division }}</option>
                                        @endforeach
                                    </select>
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
                        <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.profile_picture') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="avatar-upload">
                            <div class="avatar-edit">
                                <i class="fas fa-camera"></i>
                                <input type='file' id="imageUpload" name="image" accept=".png, .jpg, .jpeg" />
                                <label for="imageUpload"></label>
                            </div>
                            <div class="avatar-preview">
                                <div id="imagePreview"></div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <small class="text-muted">{{ translate('messages.click_the_camera_icon_to_upload_a_profile_picture') }}<br>{{ translate('messages.maximum_file_size') }}: 2MB</small>
                        </div>
                    </div>
                </div>

                <!-- Settings Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.settings') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="custom-control custom-switch mb-3">
                            <input type="checkbox" class="custom-control-input" id="emailVerified" name="email_verified" value="1">
                            <label class="custom-control-label" for="emailVerified">{{ translate('messages.email_verified') }}</label>
                        </div>
                        <div class="custom-control custom-switch mb-3">
                            <input type="checkbox" class="custom-control-input" id="activeStatus" name="is_active" value="1" checked>
                            <label class="custom-control-label" for="activeStatus">{{ translate('messages.active_status') }}</label>
                        </div>
                        <div class="custom-control custom-switch mb-3">
                            <input type="checkbox" class="custom-control-input" id="newsletterSubscription" name="subscribed_to_newsletter" value="1" checked>
                            <label class="custom-control-label" for="newsletterSubscription">{{ translate('messages.newsletter_subscription') }}</label>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="referred_by">{{ translate('messages.referral_code') }}</label>
                            <input type="text" class="form-control" id="referred_by" name="referred_by" placeholder="{{ translate('messages.enter_referral_code') }}">
                            <small class="form-text text-muted">{{ translate('messages.optional_referral_code') }}</small>
                        </div>
                    </div>
                </div>
                <!-- Action Buttons -->
                <div class="row mb-4">
                    <div class="col d-flex justify-content-end">
                        <a href="{{ route('admin.users.index', ['role' => 'user']) }}" class="btn btn-secondary mr-2">
                            <i class="fas fa-times"></i> {{ translate('messages.cancel') }}
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ translate('messages.create_user') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection
@push('scripts')
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
                
            reader.onload = function(e) {
                $('#imagePreview').css('background-image', 'url(' + e.target.result + ')');
                $('#imagePreview').hide();
                $('#imagePreview').fadeIn(650);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#imageUpload").change(function() {
        readURL(this);
    });

    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = input.nextElementSibling.querySelector('i');
            
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }

        // Username validation
        $('#username').on('input', function() {
            this.value = this.value.replace(/[^a-zA-Z0-9_]/g, '');
        });

        // Phone number validation
        $('#phone').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
    });
</script>
@endpush