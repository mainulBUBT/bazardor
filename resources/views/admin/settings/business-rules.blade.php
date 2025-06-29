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
            <form action="{{ route('admin.settings.update', ['tab' => 'business_rules']) }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="marketUpdateFrequency">{{ translate('messages.Market Update Frequency') }}</label>
                        <select class="form-control" id="marketUpdateFrequency" name="market_update_frequency">
                            <option value="weekly">{{ translate('messages.Weekly') }}</option>
                            <option value="bi-weekly">{{ translate('messages.Bi-weekly') }}</option>
                            <option value="monthly">{{ translate('messages.Monthly') }}</option>
                        </select>
                        <small class="form-text text-muted">{{ translate('messages.How often markets should be updated with new information') }}</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="marketVerificationProcess">{{ translate('messages.Market Verification Process') }}</label>
                        <select class="form-control" id="marketVerificationProcess" name="market_verification_process">
                            <option value="admin_approval">{{ translate('messages.Admin Approval') }}</option>
                            <option value="community_voting">{{ translate('messages.Community Voting') }}</option>
                            <option value="hybrid">{{ translate('messages.Hybrid (Admin + Community)') }}</option>
                        </select>
                        <small class="form-text text-muted">{{ translate('messages.How new market submissions are verified') }}</small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="minimumMarketRating">{{ translate('messages.Minimum Market Rating') }}</label>
                        <select class="form-control" id="minimumMarketRating" name="minimum_market_rating">
                            <option value="1">{{ translate('messages.1 Star') }}</option>
                            <option value="2">{{ translate('messages.2 Stars') }}</option>
                            <option value="3">{{ translate('messages.3 Stars') }}</option>
                            <option value="4">{{ translate('messages.4 Stars') }}</option>
                        </select>
                        <small class="form-text text-muted">{{ translate('messages.Minimum rating for a market to be featured') }}</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="marketVisibilityRange">{{ translate('messages.Market Visibility Range (km)') }}</label>
                        <input type="number" class="form-control" id="marketVisibilityRange" name="market_visibility_range" value="10">
                        <small class="form-text text-muted">{{ translate('messages.Default radius for market search results') }}</small>
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

    <!-- Product Settings Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Product Settings') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.settings.update', ['tab' => 'product_rules']) }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="priceUpdateFrequency">{{ translate('messages.Price Update Frequency') }}</label>
                        <select class="form-control" id="priceUpdateFrequency" name="price_update_frequency">
                            <option value="hourly">{{ translate('messages.Hourly') }}</option>
                            <option value="daily">{{ translate('messages.Daily') }}</option>
                            <option value="weekly">{{ translate('messages.Weekly') }}</option>
                        </select>
                        <small class="form-text text-muted">{{ translate('messages.How often product prices should be updated') }}</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="priceChangeThreshold">{{ translate('messages.Price Change Notification Threshold (%)') }}</label>
                        <input type="number" class="form-control" id="priceChangeThreshold" name="price_change_threshold" value="10">
                        <small class="form-text text-muted">{{ translate('messages.Percentage change to trigger price alerts') }}</small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="productImageRequirement">{{ translate('messages.Product Image Requirement') }}</label>
                        <select class="form-control" id="productImageRequirement" name="product_image_requirement">
                            <option value="not_required">{{ translate('messages.Not Required') }}</option>
                            <option value="optional">{{ translate('messages.Optional') }}</option>
                            <option value="required">{{ translate('messages.Required') }}</option>
                        </select>
                        <small class="form-text text-muted">{{ translate('messages.Whether product images are required') }}</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="defaultSortOrder">{{ translate('messages.Default Sort Order for Products') }}</label>
                        <select class="form-control" id="defaultSortOrder" name="default_sort_order">
                            <option value="lowest_price">{{ translate('messages.Lowest Price') }}</option>
                            <option value="highest_price">{{ translate('messages.Highest Price') }}</option>
                            <option value="alphabetical">{{ translate('messages.Alphabetical') }}</option>
                            <option value="popularity">{{ translate('messages.Popularity') }}</option>
                        </select>
                        <small class="form-text text-muted">{{ translate('messages.Default sorting method for product lists') }}</small>
                    </div>
                </div>
                <div class="col-12 text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>{{ translate('messages.Save Product Settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Volunteer and Points Settings Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Volunteer & Points Settings') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.settings.update', ['tab' => 'points_rules']) }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="pointsForPriceUpdate">{{ translate('messages.Points for Price Update') }}</label>
                        <input type="number" class="form-control" id="pointsForPriceUpdate" name="points_for_price_update" value="5">
                        <small class="form-text text-muted">{{ translate('messages.Points awarded for updating a product price') }}</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="pointsForMarketUpdate">{{ translate('messages.Points for Market Update') }}</label>
                        <input type="number" class="form-control" id="pointsForMarketUpdate" name="points_for_market_update" value="10">
                        <small class="form-text text-muted">{{ translate('messages.Points awarded for updating market information') }}</small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="pointsForNewProduct">{{ translate('messages.Points for New Product') }}</label>
                        <input type="number" class="form-control" id="pointsForNewProduct" name="points_for_new_product" value="15">
                        <small class="form-text text-muted">{{ translate('messages.Points awarded for adding a new product') }}</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="pointsForNewMarket">{{ translate('messages.Points for New Market') }}</label>
                        <input type="number" class="form-control" id="pointsForNewMarket" name="points_for_new_market" value="25">
                        <small class="form-text text-muted">{{ translate('messages.Points awarded for adding a new market') }}</small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="volunteerApprovalThreshold">{{ translate('messages.Volunteer Approval Threshold') }}</label>
                        <input type="number" class="form-control" id="volunteerApprovalThreshold" name="volunteer_approval_threshold" value="100">
                        <small class="form-text text-muted">{{ translate('messages.Points needed to become an approved volunteer') }}</small>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="pointsExpiryPeriod">{{ translate('messages.Points Expiry Period (days)') }}</label>
                        <input type="number" class="form-control" id="pointsExpiryPeriod" name="points_expiry_period" value="365">
                        <small class="form-text text-muted">{{ translate('messages.Number of days before points expire (0 = never)') }}</small>
                    </div>
                </div>
                <div class="col-12 text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>{{ translate('messages.Save Points Settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Business Rules Toggle Settings -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Business Rules Options</h6>
        </div>
        <div class="card-body">
            <div class="toggle-item">
                <label class="toggle-label">
                    <i class="fas fa-money-bill-wave"></i> Show price comparison between markets
                </label>
                <label class="toggle-switch">
                    <input type="checkbox" id="enablePriceComparison" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="toggle-item">
                <label class="toggle-label">
                    <i class="fas fa-clock"></i> Enable price history tracking
                </label>
                <label class="toggle-switch">
                    <input type="checkbox" id="enablePriceHistory" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="toggle-item">
                <label class="toggle-label">
                    <i class="fas fa-chart-line"></i> Enable price trend indicators
                </label>
                <label class="toggle-switch">
                    <input type="checkbox" id="enablePriceTrends" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="toggle-item">
                <label class="toggle-label">
                    <i class="fas fa-star"></i> Enable market ratings
                </label>
                <label class="toggle-switch">
                    <input type="checkbox" id="enableMarketRatings" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
            <div class="toggle-item">
                <label class="toggle-label">
                    <i class="fas fa-award"></i> Enable volunteer points system
                </label>
                <label class="toggle-switch">
                    <input type="checkbox" id="enableVolunteerPoints" checked>
                    <span class="toggle-slider"></span>
                </label>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
function statusAlert(element) {
    // Handle toggle status updates
    const url = element.getAttribute('data-url');
    const name = element.getAttribute('name');
    const value = element.checked ? 1 : 0;
    
    // Add your AJAX call here to update the setting
    console.log('Updating setting:', name, 'to:', value);
}
</script>
@endpush
