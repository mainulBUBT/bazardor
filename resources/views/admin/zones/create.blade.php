@extends('layouts.admin.app')

@section('title', translate('messages.add_zone'))

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-2">
        <h1 class="h3 mb-0 text-gray-800">{{translate('messages.add_zone')}}</h1>
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
                    <form action="{{route('admin.zones.store')}}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="name">{{translate('messages.name')}} <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" id="name" placeholder="{{translate('messages.enter_zone_name')}}" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">{{translate('messages.active')}}</label>
                            </div>
                            @error('is_active')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{translate('messages.save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 