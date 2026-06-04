@extends('layouts.admin.app')
@section('title', translate('messages.Units Management'))
@section('content')
@php
    $locales = get_enabled_locales();
    $languages = get_enabled_languages();
    $defaultLocale = get_default_locale();
@endphp

   <!-- Unit Form Card -->
   <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ translate('messages.Update Unit') }}</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.units.update', $unit->id) }}" method="POST">
                @csrf
                @method('PUT')

                @if(count($locales) > 1)
                <!-- Language Tabs -->
                <ul class="nav nav-tabs mb-3" role="tablist">
                    @foreach($languages as $lang)
                    <li class="nav-item">
                        <a class="nav-link {{ $loop->first ? 'active' : '' }}"
                           data-toggle="tab" href="#lang-{{ $lang['code'] }}" role="tab">
                            {{ strtoupper($lang['code']) }}
                            <small class="text-muted">{{ $lang['code'] === $defaultLocale ? '(Default)' : $lang['name'] }}</small>
                        </a>
                    </li>
                    @endforeach
                </ul>
                <div class="tab-content">
                    @foreach($languages as $lang)
                        @php
                            $locale = $lang['code'];
                            $isDefault = $locale === $defaultLocale;
                            $isActive = $loop->first;
                            $fieldName = $isDefault ? 'name' : "name_{$locale}";
                            $fieldSymbol = $isDefault ? 'symbol' : "symbol_{$locale}";
                            $nameValue = old($fieldName, $unit->getTranslation($locale, false)?->name ?? ($isDefault ? ($unit->getRawOriginal('name') ?? '') : ''));
                            $symbolValue = old($fieldSymbol, $unit->getTranslation($locale, false)?->symbol ?? ($isDefault ? ($unit->getRawOriginal('symbol') ?? '') : ''));
                        @endphp
                        <div class="tab-pane fade {{ $isActive ? 'show active' : '' }}" id="lang-{{ $locale }}" role="tabpanel">
                            <div class="form-row">
                                <div class="col-md-6 mb-3">
                                    <label>{{ translate('messages.Unit Name') }} ({{ $lang['name'] }}) @if($isDefault) <span class="text-danger">*</span> @endif</label>
                                    <input type="text" class="form-control" name="{{ $fieldName }}"
                                           {{ $isDefault ? 'required' : '' }}
                                           value="{{ $nameValue }}" placeholder="{{ translate('messages.Enter unit name') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>{{ translate('messages.Symbol') }} ({{ $lang['name'] }}) @if($isDefault) <span class="text-danger">*</span> @endif</label>
                                    <input type="text" class="form-control" name="{{ $fieldSymbol }}"
                                           {{ $isDefault ? 'required' : '' }}
                                           value="{{ $symbolValue }}" placeholder="{{ translate('messages.e.g. kg') }}">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @else
                <div class="form-row">
                    <div class="col-md-6 mb-3">
                        <label for="unitName">{{ translate('messages.Unit Name') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" id="unitName" value="{{ old('name', $unit->name) }}" placeholder="{{ translate('messages.Enter unit name') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="unitSymbol">{{ translate('messages.Symbol') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="symbol" id="unitSymbol" value="{{ old('symbol', $unit->symbol) }}" placeholder="{{ translate('messages.e.g. kg') }}" required>
                    </div>
                </div>
                @endif

                <div class="form-row">
                    <div class="col-md-6 mb-3">
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
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-edit mr-1"></i>{{ translate('messages.Update Unit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
