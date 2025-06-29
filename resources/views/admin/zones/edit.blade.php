@extends('layouts.admin.app')

@section('title', translate('messages.edit_zone'))

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 mb-0 text-gray-800">{{translate('messages.edit_zone')}}</h1>
        <a href="{{route('admin.zones.index')}}" class="d-none d-sm-inline-block btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{translate('messages.back')}}
        </a>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{translate('messages.zone_form')}}</h5>
                </div>
<div class="card-body">
                    <form action="{{route('admin.zones.update', $zone->id)}}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="name">{{translate('messages.name')}} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="{{translate('messages.enter_zone_name')}}" value="{{ old('name', $zone->name) }}" required>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description">{{translate('messages.description')}}</label>
                            <textarea name="description" class="form-control" id="description" rows="3" placeholder="{{translate('messages.enter_zone_description')}}">{{ old('description', $zone->description) }}</textarea>
                            @error('description')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="markets">{{translate('messages.markets')}}</label>
                            <select name="markets[]" id="markets" class="form-control select2" multiple>
                                @foreach($markets as $market)
                                    <option value="{{ $market->id }}" {{ in_array($market->id, old('markets', $zoneMarketIds)) ? 'selected' : '' }}>
                                        {{ $market->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">{{translate('messages.select_markets_to_assign_to_this_zone')}}</small>
                            @error('markets')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', $zone->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">{{translate('messages.active')}}</label>
                            </div>
                            @error('is_active')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{translate('messages.update')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Markets in this Zone -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">{{translate('messages.markets_in_this_zone')}}</h5>
                </div>
                <div class="card-body">
                    @if(count($zone->markets) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>{{translate('messages.id')}}</th>
                                        <th>{{translate('messages.name')}}</th>
                                        <th>{{translate('messages.address')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($zone->markets as $market)
                                        <tr>
                                            <td>{{$market->id}}</td>
                                            <td>{{$market->name}}</td>
                                            <td>{{$market->address}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            {{translate('messages.no_markets_assigned_to_this_zone')}}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize select2
        $('.select2').select2();
    });
</script>
@endpush 