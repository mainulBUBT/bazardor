@extends('layouts.admin.app')
@section('title', translate('messages.Business Rules'))
@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-2 text-gray-800">{{ translate('messages.Business Rules') }}</h1>
    <p class="mb-4">{{ translate('messages.Configure business rules and policies for your marketplace') }}</p>

    @include('admin.settings._partials.tabs')

    <!-- Market Settings Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Market Settings') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.settings.update', ['tab' => BUSINESS_RULES]) }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="marketUpdateFrequency">{{ translate('messages.Market Update Frequency') }}</label>
                        <select class="form-control" id="marketUpdateFrequency" name="market_update_frequency">
                            @php($marketUpdateFrequency = $settings['market_update_frequency']['value'] ?? 'daily')
                            <option value="daily" {{ $marketUpdateFrequency === 'daily' ? 'selected' : '' }}>{{ translate('messages.Daily') }}</option>
                            <option value="weekly" {{ $marketUpdateFrequency === 'weekly' ? 'selected' : '' }}>{{ translate('messages.Weekly') }}</option>
                            <option value="monthly" {{ $marketUpdateFrequency === 'monthly' ? 'selected' : '' }}>{{ translate('messages.Monthly') }}</option>
                        </select>
                        <small class="form-text text-muted">{{ translate('messages.How often markets should be updated with new information') }}</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="marketUpdateCutoffTime">{{ translate('messages.Market Update Cutoff Time') }}</label>
                        @php($marketUpdateCutoff = $settings['market_update_cutoff_time']['value'] ?? '17:00')
                        <input type="time" class="form-control" id="marketUpdateCutoffTime" name="market_update_cutoff_time" value="{{ $marketUpdateCutoff }}">
                        <small class="form-text text-muted">{{ translate('messages.Time when daily market updates are closed') }}</small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="productUpdateFrequency">{{ translate('messages.Product Update Frequency') }}</label>
                        <select class="form-control" id="productUpdateFrequency" name="product_update_frequency">
                            @php($productUpdateFrequency = $settings['product_update_frequency']['value'] ?? 'daily')
                            <option value="daily" {{ $productUpdateFrequency === 'daily' ? 'selected' : '' }}>{{ translate('messages.Daily') }}</option>
                            <option value="weekly" {{ $productUpdateFrequency === 'weekly' ? 'selected' : '' }}>{{ translate('messages.Weekly') }}</option>
                            <option value="monthly" {{ $productUpdateFrequency === 'monthly' ? 'selected' : '' }}>{{ translate('messages.Monthly') }}</option>
                        </select>
                        <small class="form-text text-muted">{{ translate('messages.How often product information should be refreshed') }}</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="productUpdateCutoffTime">{{ translate('messages.Product Update Cutoff Time') }}</label>
                        @php($productUpdateCutoff = $settings['product_update_cutoff_time']['value'] ?? '17:00')
                        <input type="time" class="form-control" id="productUpdateCutoffTime" name="product_update_cutoff_time" value="{{ $productUpdateCutoff }}">
                        <small class="form-text text-muted">{{ translate('messages.Time when daily product updates are closed') }}</small>
                    </div>
                </div>
                <div class="col-12 text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>{{ translate('messages.Save Market Settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
