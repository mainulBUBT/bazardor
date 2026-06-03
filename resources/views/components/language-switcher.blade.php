@props([])

@php
    $locales = get_enabled_locales();
    $languages = get_enabled_languages();
    $defaultLocale = get_default_locale();
    $isTranslatable = count($locales) > 1;
@endphp

@if($isTranslatable)
<div class="card shadow-sm mb-4 border-0 bg-light">
    <div class="card-body py-2 px-3">
        <div class="d-flex align-items-center">
            <span class="text-muted mr-3 font-weight-bold small text-uppercase">
                <i class="fas fa-language mr-1"></i>{{ translate('messages.Language') }}
            </span>
            <div class="btn-group btn-group-sm" id="languageSwitcher" role="group">
                @foreach($languages as $lang)
                    <button type="button"
                            class="btn btn-lang-switch {{ $loop->first ? 'btn-primary' : 'btn-outline-secondary' }}"
                            data-locale="{{ $lang['code'] }}"
                            data-direction="{{ $lang['direction'] }}">
                        {{ strtoupper($lang['code']) }}
                        <small class="ml-1">{{ $lang['name'] }}</small>
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
function initLanguageSwitcher() {
    $(document).on('click', '.btn-lang-switch', function() {
        var locale = $(this).data('locale');
        var direction = $(this).data('direction');

        $('.btn-lang-switch').removeClass('btn-primary').addClass('btn-outline-secondary');
        $(this).removeClass('btn-outline-secondary').addClass('btn-primary');

        $('.translatable-field .lang-input').hide();
        $('.translatable-field .lang-' + locale).show();

        $('.translatable-field .lang-' + locale + ' input, .translatable-field .lang-' + locale + ' textarea')
            .css('direction', direction);
    });
}

// jQuery loads at the bottom of the layout, so defer until ready
if (typeof $ === 'undefined') {
    document.addEventListener('DOMContentLoaded', function() {
        initLanguageSwitcher();
    });
} else {
    initLanguageSwitcher();
}
</script>
@endif
