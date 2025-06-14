@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Moderators</h1>
    <a href="{{ route('admin.moderators.create') }}" class="btn btn-primary mb-3">Create Moderator</a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($moderators as $moderator)
                <tr>
                    <td>{{ $moderator->name }}</td>
                    <td>{{ $moderator->email }}</td>
                    <td>
                        <a href="{{ route('admin.moderators.edit', $moderator) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.moderators.destroy', $moderator) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection 