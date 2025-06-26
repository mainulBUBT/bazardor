@extends('layouts.admin.app')
@section('title', translate('messages.Users Management'))

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Users Management</h1>
    <p class="mb-4">Manage all your users in one place. View, edit, or manage user accounts as needed.</p>

    {{-- Stats Cards --}}
    @include('admin.users.partials.cards', ['stats' => $userStats])

    <!-- Tabs (as links) -->
    <ul class="nav nav-tabs mb-3" id="userTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ $role === App\Enums\UserType::USER->value ? 'active' : '' }}" href="{{ route('admin.users.index', ['role' => App\Enums\UserType::USER->value]) }}">Users</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $role === App\Enums\UserType::VOLUNTEER->value ? 'active' : '' }}" href="{{ route('admin.users.index', ['role' => App\Enums\UserType::VOLUNTEER->value]) }}">Volunteers</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $role === App\Enums\UserType::MODERATOR->value ? 'active' : '' }}" href="{{ route('admin.users.index', ['role' => App\Enums\UserType::MODERATOR->value]) }}">Moderators</a>
        </li>
    </ul>

    
    @include('admin.users.partials.table', ['users' => $users, 'type' => $role])
</div>
@endsection