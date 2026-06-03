@props([
    'name',
    'label',
    'type' => 'text',
    'required' => false,
    'model' => null,
])

@php
    $locales = get_enabled_locales();
    $languages = get_enabled_languages();
    $defaultLocale = get_default_locale();
    $isTranslatable = count($locales) > 1;
@endphp

<div class="form-group translatable-field" data-field="{{ $name }}">
    <label>{!! $label !!} @if($required) <span class="text-danger">*</span>@endif</label>

    @if($isTranslatable)
        @foreach($languages as $lang)
            @php
                $locale = $lang['code'];
                $inputName = $locale === $defaultLocale ? $name : "{$name}_{$locale}";
                $isDefault = $locale === $defaultLocale;
                $isVisible = $loop->first;
                $oldValue = old($inputName);

                // For default locale, fall back to the plain model attribute
                if ($isDefault && $model) {
                    $modelValue = $model->getTranslation($name, $locale, false);
                    if (empty($modelValue)) {
                        $raw = $model->getRawOriginal($name);
                        // If raw value is JSON (new format), don't show the raw JSON string
                        $decoded = json_decode($raw, true);
                        $modelValue = is_array($decoded) ? ($decoded[$locale] ?? '') : $raw;
                    }
                } elseif ($model) {
                    $modelValue = $model->getTranslation($name, $locale, false);
                } else {
                    $modelValue = '';
                }

                $value = $oldValue !== null ? $oldValue : ($modelValue ?? '');
            @endphp
            <div class="lang-input lang-{{ $locale }}" @if(!$isVisible) style="display:none;" @endif>
                @if($type === 'textarea')
                    <textarea name="{{ $inputName }}"
                              class="form-control"
                              rows="3"
                              {{ ($isDefault && $required) ? 'required' : '' }}>{{ $value }}</textarea>
                @else
                    <input type="{{ $type }}"
                           name="{{ $inputName }}"
                           class="form-control"
                           value="{{ $value }}"
                           {{ ($isDefault && $required) ? 'required' : '' }}>
                @endif
            </div>
        @endforeach
    @else
        @php
            $oldValue = old($name);
            $modelValue = '';
            if ($model) {
                $modelValue = $model->getTranslation($name, $defaultLocale, false);
                if (empty($modelValue)) {
                    $raw = $model->getRawOriginal($name);
                    $decoded = json_decode($raw, true);
                    $modelValue = is_array($decoded) ? ($decoded[$defaultLocale] ?? '') : $raw;
                }
            }
            $value = $oldValue !== null ? $oldValue : ($modelValue ?? '');
        @endphp
        @if($type === 'textarea')
            <textarea name="{{ $name }}"
                      class="form-control"
                      rows="3"
                      {{ $required ? 'required' : '' }}>{{ $value }}</textarea>
        @else
            <input type="{{ $type }}"
                   name="{{ $name }}"
                   class="form-control"
                   value="{{ $value }}"
                   {{ $required ? 'required' : '' }}>
        @endif
    @endif
</div>
