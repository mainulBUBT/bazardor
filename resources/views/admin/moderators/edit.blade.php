@extends('layouts.admin.app')
@section('title', translate('messages.Banner Management'))

@section('content')
<div class="container">
    <h1>Edit Moderator</h1>
    <form action="{{ route('admin.moderators.update', $moderator) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $moderator->name) }}" required>
            @error('name')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $moderator->email) }}" required>
            @error('email')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password (leave blank to keep current)</label>
            <input type="password" name="password" id="password" class="form-control">
            @error('password')<div class="text-danger">{{ $message }}</div>@enderror
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.moderators.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection 