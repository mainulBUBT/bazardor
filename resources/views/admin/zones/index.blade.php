@extends('layouts.admin.app')

@section('title', translate('messages.zones'))

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 mb-0 text-gray-800">{{translate('messages.zones')}}</h1>
        <a href="{{route('admin.zones.create')}}" class="d-none d-sm-inline-block btn btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> {{translate('messages.add_zone')}}
        </a>
    </div>

    <!-- Content Row -->
    <div class="card">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{translate('messages.zone_list')}}</h6>
            <form action="{{route('admin.zones.index')}}" method="GET" class="mt-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="{{translate('messages.search_by_name')}}" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>{{translate('messages.id')}}</th>
                            <th>{{translate('messages.name')}}</th>
                            <th>{{translate('messages.markets')}}</th>
                            <th>{{translate('messages.status')}}</th>
                            <th>{{translate('messages.actions')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($zones as $key=>$zone)
                            <tr>
                                <td>{{$zone->id}}</td>
                                <td>{{$zone->name}}</td>
                                <td>
                                    <span class="badge badge-soft-info">
                                        {{$zone->markets->count()}} {{translate('messages.markets')}}
                                    </span>
                                </td>
                                <td>
                                    <label class="toggle-switch toggle-switch-sm">
                                        <input type="checkbox" class="toggle-switch-input" 
                                            onclick="location.href='{{route('admin.zones.toggle-status', $zone->id)}}'" 
                                            {{$zone->is_active ? 'checked' : ''}}>
                                            
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{route('admin.zones.edit', $zone->id)}}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{route('admin.zones.destroy', $zone->id)}}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                onclick="return confirm('{{translate('messages.are_you_sure_to_delete_this_zone')}}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center">
                {{ $zones->links() }}
            </div>
            
            @if(count($zones) === 0)
            <div class="empty--data">
                <img src="{{asset('assets/admin/img/empty.png')}}" alt="{{translate('messages.no_data_found')}}">
                <h5>{{translate('messages.no_zones_found')}}</h5>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection 