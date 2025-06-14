@extends('layouts.admin.app')
@section('title', translate('messages.Add Product'))
@section('content')
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ translate('messages.Add New Product') }}</h1>
    <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ translate('messages.Back to Products') }}
    </a>
</div>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Product Information') }}</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="productName">{{ translate('messages.Product Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="productName" class="form-control" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="productCategory">{{ translate('messages.Category') }} <span class="text-danger">*</span></label>
                            <select name="category_id" id="productCategory" class="form-control select2" required>
                                <option value="">{{ translate('messages.Select Category') }}</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="productUnit">{{ translate('messages.Unit') }} <span class="text-danger">*</span></label>
                            <select name="unit_id" id="productUnit" class="form-control select2" required>
                                <option value="">{{ translate('messages.Select Unit') }}</option>
                                @foreach($units as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->symbol }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="productDescription">{{ translate('messages.Description') }}</label>
                        <textarea name="description" id="productDescription" class="form-control" rows="4"></textarea>
                    </div>
                    <!-- TAGS -->
                    <div class="form-group">
                        <label>{{ translate('messages.Tags') }}</label>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" id="productTagInput" placeholder="{{ translate('messages.Add a tag and press Enter') }}">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="addTagBtn">
                                    <i class="fas fa-plus"></i> {{ translate('messages.Add') }}
                                </button>
                            </div>
                        </div>
                        <div class="product-tags" id="tagContainer"></div>
                        <input type="hidden" name="tags" id="tagsHidden">
                    </div>
                </div>
            </div>
            <!-- MARKET PRICING -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Market Pricing') }}</h6>
                    <button class="btn btn-sm btn-primary" type="button" id="addMarketPriceBtn">
                        <i class="fas fa-plus fa-sm"></i> {{ translate('messages.Add Market Price') }}
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="marketPriceTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>{{ translate('messages.Market') }}</th>
                                    <th>{{ translate('messages.Price') }}</th>
                                    <th>{{ translate('messages.Date') }}</th>
                                    <th>{{ translate('messages.Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="marketPriceRows">
                                <!-- Market price rows will be added here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- sidebar -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Status & Visibility') }}</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="productStatus">{{ translate('messages.Status') }}</label>
                        <select name="status" id="productStatus" class="form-control">
                            <option value="active" selected>{{ translate('messages.Active') }}</option>
                            <option value="inactive">{{ translate('messages.Inactive') }}</option>
                            <option value="draft">{{ translate('messages.Draft') }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="productVisibility">{{ translate('messages.Visibility') }}</label>
                        <select name="is_visible" id="productVisibility" class="form-control">
                            <option value="1" selected>{{ translate('messages.Public') }}</option>
                            <option value="0">{{ translate('messages.Private') }}</option>
                        </select>
                    </div>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="featuredSwitch" name="is_featured" value="1">
                        <label class="custom-control-label" for="featuredSwitch">{{ translate('messages.Featured Product') }}</label>
                    </div>
                </div>
            </div>
            <!-- IMAGE CARD -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Product Image') }}</h6>
                </div>
                <div class="card-body">
                    <div class="image-preview-container mb-3" onclick="document.getElementById('productImageInput').click();">
                        <div class="image-preview" id="imagePreview">
                            <i class="fas fa-camera"></i>
                            <span>{{ translate('messages.Click to Upload Image') }}</span>
                        </div>
                    </div>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input d-none" id="productImageInput" name="image" accept="image/*">
                        <label class="custom-file-label" for="productImageInput" id="productImageLabel">{{ translate('messages.Choose file...') }}</label>
                    </div>
                    <div class="small text-muted mt-2">
                        <p>{{ translate('messages.Recommended size: 800x800px, max file size: 2MB') }}</p>
                    </div>
                </div>
            </div>
            <!-- ADDITIONAL INFO CARD -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Additional Information') }}</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="productSKU">{{ translate('messages.SKU') }}</label>
                        <input type="text" class="form-control" id="productSKU" name="sku" placeholder="SKU-001">
                    </div>
                    <div class="form-group">
                        <label for="productBarcode">{{ translate('messages.Barcode') }}</label>
                        <input type="text" class="form-control" id="productBarcode" name="barcode" placeholder="Optional barcode">
                    </div>
                    <div class="form-group">
                        <label for="productBrand">{{ translate('messages.Brand') }}</label>
                        <input type="text" class="form-control" id="productBrand" name="brand" placeholder="Brand name">
                    </div>
                    <div class="form-group mb-0">
                        <label for="productCountry">{{ translate('messages.Country of Origin') }}</label>
                        <select class="form-control select2" id="productCountry" name="country_of_origin">
                            <option value="">{{ translate('messages.Select Country') }}</option>
                            <option value="local">{{ translate('messages.Local') }}</option>
                            <option value="imported">{{ translate('messages.Imported') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12 d-flex justify-content-end mb-4">
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary mr-2">{{ translate('messages.Cancel') }}</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> {{ translate('messages.Save Product') }}</button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // --- Image Preview & File Input Handling (match market create) ---
    const productImageInput = document.getElementById('productImageInput');
    const imagePreview = document.getElementById('imagePreview');
    const imageLabel = document.getElementById('productImageLabel');
    $(productImageInput).on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.innerHTML = '<img src="' + e.target.result + '" alt="Product Preview">';
            }
            reader.readAsDataURL(file);
            imageLabel.textContent = file.name;
        } else {
            imagePreview.innerHTML = '<i class="fas fa-camera"></i><span>{{ translate('messages.Click to Upload Image') }}</span>';
            imageLabel.textContent = '{{ translate('messages.Choose file...') }}';
        }
    });

    // --- Tag Management ---
    const tagInput = document.getElementById('productTagInput');
    const tagContainer = document.getElementById('tagContainer');
    const addTagBtn = document.getElementById('addTagBtn');
    const tagsHidden = document.getElementById('tagsHidden');
    let tags = [];

    function renderTags() {
        tagContainer.innerHTML = '';
        tags.forEach(function(tag, idx) {
            const tagElem = document.createElement('span');
            tagElem.className = 'badge badge-primary mr-1 mb-1 p-2';
            tagElem.innerHTML = `${tag} <span class="remove-tag" role="button" title="Remove tag">&times;</span>`;
            tagElem.querySelector('.remove-tag').addEventListener('click', function() {
                tags.splice(idx, 1);
                renderTags();
            });
            tagContainer.appendChild(tagElem);
        });
        tagsHidden.value = tags.join(',');
    }

    addTagBtn.addEventListener('click', function() {
        const tagText = tagInput.value.trim();
        if (tagText && !tags.includes(tagText)) {
            tags.push(tagText);
            renderTags();
        }
        tagInput.value = '';
    });

    tagInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            const tagText = this.value.trim();
            if (tagText && !tags.includes(tagText)) {
                tags.push(tagText);
                renderTags();
            }
            this.value = '';
        }
    });

    // --- Market Price Row Management ---
    const addMarketRowBtn = document.getElementById('addMarketPriceBtn');
    const marketPriceRows = document.getElementById('marketPriceRows');
    let marketRowCount = 0;
    const markets = @json(\App\Models\Market::select('id','name')->orderBy('name')->get());

    function createMarketRow() {
        marketRowCount++;
        const tr = document.createElement('tr');
        tr.id = `marketRow-${marketRowCount}`;
        let marketOptions = `<option value="">{{ translate('messages.Select Market') }}</option>`;
        markets.forEach(function(m) {
            marketOptions += `<option value="${m.id}">${m.name}</option>`;
        });
        tr.innerHTML = `
            <td>
                <select class="form-control select2 market-select" name="market_prices[${marketRowCount}][market_id]">
                    ${marketOptions}
                </select>
            </td>
            <td>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">$</span>
                    </div>
                    <input type="number" class="form-control" name="market_prices[${marketRowCount}][price]" placeholder="0.00" step="0.01">
                </div>
            </td>
            <td>
                <input type="date" class="form-control" name="market_prices[${marketRowCount}][price_date]" value="${new Date().toISOString().split('T')[0]}">
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-market-row" data-row-id="${marketRowCount}">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tr.querySelector('.remove-market-row').addEventListener('click', function() {
            tr.remove();
        });
        marketPriceRows.appendChild(tr);
    }
    // Add initial row on page load
    createMarketRow();
    addMarketRowBtn.addEventListener('click', function() {
        createMarketRow();
    });
});
</script>
@endpush 