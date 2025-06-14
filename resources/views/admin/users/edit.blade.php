@extends('layouts.admin.app')
@section('title', translate('messages.edit_user'))

@push('css_or_js')
<style>
    .avatar-upload {
        position: relative;
        max-width: 205px;
        margin: 0 auto;
    }
    .avatar-upload .avatar-edit {
        position: absolute;
        right: 12px;
        z-index: 1;
        top: 10px;
    }
    .avatar-upload .avatar-edit input {
        display: none;
    }
    .avatar-upload .avatar-edit label {
        display: inline-block;
        width: 34px;
        height: 34px;
        margin-bottom: 0;
        border-radius: 100%;
        background: #FFFFFF;
        border: 1px solid transparent;
        box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.12);
        cursor: pointer;
        font-weight: normal;
        transition: all .2s ease-in-out;
    }
    .avatar-upload .avatar-edit label:hover {
        background: #f1f1f1;
        border-color: #d6d6d6;
    }
    .avatar-upload .avatar-edit label:after {
        content: "\f030";
        font-family: 'Font Awesome 5 Free';
        color: #757575;
        position: absolute;
        top: 7px;
        left: 0;
        right: 0;
        text-align: center;
        margin: auto;
    }
    .avatar-upload .avatar-preview {
        width: 192px;
        height: 192px;
        position: relative;
        border-radius: 100%;
        border: 6px solid #F8F8F8;
        box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1);
    }
    .avatar-upload .avatar-preview > div {
        width: 100%;
        height: 100%;
        border-radius: 100%;
        background-size: cover;
        background-repeat: no-repeat;
        background-position: center;
    }
    .password-toggle {
        cursor: pointer;
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        color: #858796;
    }
    .form-group.required label:after {
        content: " *";
        color: #e74a3b;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">{{translate('messages.edit_user')}}</h1>
    <p class="mb-4">{{translate('messages.edit_user_description')}}</p>

    <form action="{{route('admin.users.update', $user->id)}}" method="post" enctype="multipart/form-data" id="editUserForm">
        @method('PUT')
        @include('admin.users.partials._form', ['user' => $user])
    </form>
</div>
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

    $('#editUserForm').on('submit', function(e) {
        const password = $('#password').val();
        if (password) {
            const confirmPassword = $('#password_confirmation').val();
            if (password !== confirmPassword) {
                e.preventDefault();
                alert("{{translate('messages.passwords_do_not_match')}}");
            }
        }
    });
</script>
@endpush
