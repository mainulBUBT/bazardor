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

    <!-- Price Settings Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Price Settings') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.settings.update', ['tab' => BUSINESS_RULES]) }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="priceTolerance">{{ translate('messages.Price Tolerance') }}</label>
                        @php($priceTolerance = $settings['price_tolerance']['value'] ?? 0.50)
                        <input type="number" class="form-control" id="priceTolerance" name="price_tolerance" value="{{ $priceTolerance }}" step="0.05" min="0.05" max="1.00">
                        <small class="form-text text-muted">{{ translate('messages.Allowed deviation from reference price (0.50 = ±50%). Higher values accept wider price variation.') }}</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="rateLimitMinutes">{{ translate('messages.Rate Limit (minutes)') }}</label>
                        @php($rateLimitMinutes = $settings['rate_limit_minutes']['value'] ?? 60)
                        <input type="number" class="form-control" id="rateLimitMinutes" name="rate_limit_minutes" value="{{ $rateLimitMinutes }}" min="5" max="1440">
                        <small class="form-text text-muted">{{ translate('messages.Minimum wait between submissions for the same product and market per user') }}</small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="contributionWindowHours">{{ translate('messages.Contribution Window (hours)') }}</label>
                        @php($contributionWindow = $settings['contribution_window_hours']['value'] ?? 24)
                        <input type="number" class="form-control" id="contributionWindowHours" name="contribution_window_hours" value="{{ $contributionWindow }}" min="1" max="168">
                        <small class="form-text text-muted">{{ translate('messages.How many hours of recent contributions to consider when calculating the median price') }}</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="stalePriceDays">{{ translate('messages.Outdated Price Threshold (days)') }}</label>
                        @php($outdatedPriceDays = $settings['outdated_price_days']['value'] ?? 30)
                        <input type="number" class="form-control" id="outdatedPriceDays" name="outdated_price_days" value="{{ $outdatedPriceDays }}" min="1" max="365">
                        <small class="form-text text-muted">{{ translate('messages.Prices older than this many days are flagged as outdated') }}</small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="minSubmissionsForMedian">{{ translate('messages.Min Submissions for Median') }}</label>
                        @php($minSubmissions = $settings['min_submissions_for_median']['value'] ?? 1)
                        <input type="number" class="form-control" id="minSubmissionsForMedian" name="min_submissions_for_median" value="{{ $minSubmissions }}" min="1" max="10">
                        <small class="form-text text-muted">{{ translate('messages.Minimum number of contributions needed before a price is published') }}</small>
                    </div>
                </div>
                <div class="col-12 text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>{{ translate('messages.Save Price Settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- System Settings Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.System Settings') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.settings.update', ['tab' => BUSINESS_RULES]) }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="timezone">{{ translate('messages.Time Zone') }}</label>
                        @php($timezone = $settings['timezone']['value'] ?? 'UTC')
                        <input type="text" class="form-control @error('timezone') is-invalid @enderror" id="timezone" name="timezone" value="{{ $timezone }}" placeholder="e.g., UTC, Asia/Dhaka">
                        @error('timezone')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group col-md-6">
                        <label for="timeFormat">{{ translate('messages.Time Format') }}</label>
                        @php($timeFormat = $settings['time_format']['value'] ?? 'H:i')
                        <select class="form-control @error('time_format') is-invalid @enderror" id="timeFormat" name="time_format">
                            <option value="H:i" {{ $timeFormat === 'H:i' ? 'selected' : '' }}>24-hour (H:i)</option>
                            <option value="h:i A" {{ $timeFormat === 'h:i A' ? 'selected' : '' }}>12-hour (h:i A)</option>
                        </select>
                        @error('time_format')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="decimalPlaces">{{ translate('messages.Digit After Decimal Point') }}</label>
                        @php($decimalPlaces = $settings['decimal_places']['value'] ?? '2')
                        <input type="number" class="form-control @error('decimal_places') is-invalid @enderror" id="decimalPlaces" name="decimal_places" value="{{ $decimalPlaces }}" min="0" max="10">
                        @error('decimal_places')
                            <span class="invalid-feedback" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <label for="copyrightText">{{ translate('messages.Copyright Text') }}</label>
                    @php($copyrightText = $settings['copyright_text']['value'] ?? '')
                    <textarea class="form-control @error('copyright_text') is-invalid @enderror" id="copyrightText" name="copyright_text" rows="2" placeholder="e.g., © 2025 Bazar-dor. All rights reserved.">{{ $copyrightText }}</textarea>
                    @error('copyright_text')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="cookiesText">{{ translate('messages.Cookies Text') }}</label>
                    @php($cookiesText = $settings['cookies_text']['value'] ?? '')
                    <textarea class="form-control @error('cookies_text') is-invalid @enderror" id="cookiesText" name="cookies_text" rows="2" placeholder="Cookies policy text...">{{ $cookiesText }}</textarea>
                    @error('cookies_text')
                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-12 text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>{{ translate('messages.Save System Settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
