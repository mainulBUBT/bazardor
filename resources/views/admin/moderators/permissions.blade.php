@extends('layouts.admin.app')
@section('title', translate('messages.Banner Management'))

@section('content')
<div class="container">
    <h1>Moderators Permissions</h1>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Permissions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($moderators as $moderator)
                <tr>
                    <td>{{ $moderator->name }}</td>
                    <td>{{ $moderator->email }}</td>
                    <td>
                        <ul>
                            @foreach(config('roles')[\App\Enums\Role::MODERATOR->value] as $perm)
                                <li>{{ $perm }}</li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 