@extends('layouts.admin.app')
@section('title', translate('messages.Product Overview'))
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ $product->name }}</h1>
    <div>
        <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-secondary shadow-sm mr-2">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ translate('messages.Back to Products') }}
        </a>
        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-edit fa-sm"></i> {{ translate('messages.Edit Product') }}
        </a>
    </div>
</div>
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Product Information') }}</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">{{ translate('messages.Product Name') }}</dt>
                    <dd class="col-sm-8">{{ $product->name }}</dd>

                    <dt class="col-sm-4">{{ translate('messages.Category') }}</dt>
                    <dd class="col-sm-8">{{ optional($product->category)->name }}</dd>

                    <dt class="col-sm-4">{{ translate('messages.Unit') }}</dt>
                    <dd class="col-sm-8">{{ optional($product->unit)->name }} @if($product->unit) ({{ $product->unit->symbol }}) @endif</dd>

                    <dt class="col-sm-4">{{ translate('messages.Description') }}</dt>
                    <dd class="col-sm-8">{!! nl2br(e($product->description)) !!}</dd>

                    <dt class="col-sm-4">{{ translate('messages.Tags') }}</dt>
                    <dd class="col-sm-8">
                        @foreach($product->tags as $tag)
                            <span class="badge badge-primary mr-1">{{ $tag->tag }}</span>
                        @endforeach
                    </dd>
                </dl>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Market Pricing') }}</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>{{ translate('messages.Market') }}</th>
                                <th>{{ translate('messages.Price') }}</th>
                                <th>{{ translate('messages.Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($product->marketPrices as $mp)
                                <tr>
                                    <td>{{ optional($mp->market)->name }}</td>
                                    <td>${{ number_format($mp->price, 2) }}</td>
                                    <td>{{ $mp->price_date ? $mp->price_date->format('Y-m-d') : '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted">{{ translate('messages.No market prices found.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Product Image') }}</h6>
            </div>
            <div class="card-body text-center">
                @if($product->image_path)
                    <img src="{{ asset('public/storage/products/'.$product->image_path) }}" alt="Product Image" class="img-fluid img-thumbnail mb-2" style="max-width: 220px; max-height: 220px;">
                @else
                    <img src="{{ asset('adminpanel/img/product-placeholder.png') }}" class="img-fluid img-thumbnail mb-2" style="max-width: 220px; max-height: 220px;" alt="placeholder">
                @endif
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Additional Information') }}</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-5">{{ translate('messages.SKU') }}</dt>
                    <dd class="col-sm-7">{{ $product->sku ?: '-' }}</dd>

                    <dt class="col-sm-5">{{ translate('messages.Barcode') }}</dt>
                    <dd class="col-sm-7">{{ $product->barcode ?: '-' }}</dd>

                    <dt class="col-sm-5">{{ translate('messages.Brand') }}</dt>
                    <dd class="col-sm-7">{{ $product->brand ?: '-' }}</dd>

                    <dt class="col-sm-5">{{ translate('messages.Country of Origin') }}</dt>
                    <dd class="col-sm-7">
                        @if($product->country_of_origin == 'local')
                            {{ translate('messages.Local') }}
                        @elseif($product->country_of_origin == 'imported')
                            {{ translate('messages.Imported') }}
                        @else
                            -
                        @endif
                    </dd>

                    <dt class="col-sm-5">{{ translate('messages.Status') }}</dt>
                    <dd class="col-sm-7">
                        @if($product->status === 'active')
                            <span class="badge badge-success">{{ translate('messages.Active') }}</span>
                        @elseif($product->status === 'inactive')
                            <span class="badge badge-danger">{{ translate('messages.Inactive') }}</span>
                        @else
                            <span class="badge badge-secondary">{{ translate('messages.Draft') }}</span>
                        @endif
                    </dd>

                    <dt class="col-sm-5">{{ translate('messages.Visibility') }}</dt>
                    <dd class="col-sm-7">
                        @if($product->is_visible)
                            <span class="badge badge-primary">{{ translate('messages.Public') }}</span>
                        @else
                            <span class="badge badge-secondary">{{ translate('messages.Private') }}</span>
                        @endif
                    </dd>

                    <dt class="col-sm-5">{{ translate('messages.Featured') }}</dt>
                    <dd class="col-sm-7">
                        @if($product->is_featured)
                            <span class="badge badge-warning">{{ translate('messages.Yes') }}</span>
                        @else
                            <span class="badge badge-secondary">{{ translate('messages.No') }}</span>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection 