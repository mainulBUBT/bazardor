@extends('layouts.admin.app')
@section('title', translate('messages.Units Management'))
@section('content')

   <!-- Unit Form Card -->
   <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Update Unit') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.units.update', $unit->id) }}" class="row" method="POST">
                @csrf
                @method('PUT')
                <div class="col-md-4 mb-3">
                    <label for="unitName">{{ translate('messages.Unit Name') }}</label>
                    <input type="text" class="form-control" name="name" id="unitName" value="{{ $unit->name }}" placeholder="{{ translate('messages.Enter unit name') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="unitSymbol">{{ translate('messages.Symbol') }}</label>
                    <input type="text" class="form-control" name="symbol" id="unitSymbol" value="{{ $unit->symbol }}" placeholder="{{ translate('messages.e.g. kg') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="unitType">{{ translate('messages.Type') }}</label>
                    <select class="form-control" name="unit_type" id="unitType">
                        <option selected value="">{{ translate('messages.Select unit type') }}</option>
                        <option value="weight" {{ $unit->unit_type == 'weight' ? 'selected' : '' }}>{{ translate('messages.Weight') }}</option>
                        <option value="volume" {{ $unit->unit_type == 'volume' ? 'selected' : '' }}>{{ translate('messages.Volume') }}</option>
                        <option value="length" {{ $unit->unit_type == 'length' ? 'selected' : '' }}>{{ translate('messages.Length') }}</option>
                        <option value="count" {{ $unit->unit_type == 'count' ? 'selected' : '' }}>{{ translate('messages.Count') }}</option>
                        <option value="other" {{ $unit->unit_type == 'other' ? 'selected' : '' }}>{{ translate('messages.Other') }}</option>
                    </select>
                </div>
                <div class="col-12 text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-edit mr-1"></i>{{ translate('messages.Update Unit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
@push('script')

@endpush    