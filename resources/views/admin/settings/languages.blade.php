@extends('layouts.admin.app')
@section('title', translate('messages.Language Settings'))

@section('content')
    <h1 class="h3 mb-2 text-gray-800">{{ translate('messages.Language Settings') }}</h1>
    <p class="mb-4">{{ translate('messages.Manage supported languages for content translation across the platform.') }}</p>

    @include('admin.settings._partials.tabs')

    @php
        $enabledLanguages = $settings['enabled_languages'] ?? [['code' => 'en', 'name' => 'English', 'direction' => 'ltr', 'native' => 'English']];
        $defaultLanguage = $settings['default_language'] ?? 'en';
        $enabledCodes = collect($enabledLanguages)->pluck('code')->toArray();

        $allLanguages = [
            ['code' => 'af', 'name' => 'Afrikaans', 'native' => 'Afrikaans', 'direction' => 'ltr'],
            ['code' => 'ar', 'name' => 'Arabic', 'native' => 'العربية', 'direction' => 'rtl'],
            ['code' => 'bn', 'name' => 'Bangla', 'native' => 'বাংলা', 'direction' => 'ltr'],
            ['code' => 'bg', 'name' => 'Bulgarian', 'native' => 'български', 'direction' => 'ltr'],
            ['code' => 'zh', 'name' => 'Chinese (Simplified)', 'native' => '中文', 'direction' => 'ltr'],
            ['code' => 'zh-TW', 'name' => 'Chinese (Traditional)', 'native' => '繁體中文', 'direction' => 'ltr'],
            ['code' => 'hr', 'name' => 'Croatian', 'native' => 'hrvatski', 'direction' => 'ltr'],
            ['code' => 'cs', 'name' => 'Czech', 'native' => 'čeština', 'direction' => 'ltr'],
            ['code' => 'da', 'name' => 'Danish', 'native' => 'dansk', 'direction' => 'ltr'],
            ['code' => 'nl', 'name' => 'Dutch', 'native' => 'Nederlands', 'direction' => 'ltr'],
            ['code' => 'en', 'name' => 'English', 'native' => 'English', 'direction' => 'ltr'],
            ['code' => 'et', 'name' => 'Estonian', 'native' => 'eesti', 'direction' => 'ltr'],
            ['code' => 'fa', 'name' => 'Persian', 'native' => 'فارسی', 'direction' => 'rtl'],
            ['code' => 'fi', 'name' => 'Finnish', 'native' => 'suomi', 'direction' => 'ltr'],
            ['code' => 'fr', 'name' => 'French', 'native' => 'Français', 'direction' => 'ltr'],
            ['code' => 'de', 'name' => 'German', 'native' => 'Deutsch', 'direction' => 'ltr'],
            ['code' => 'el', 'name' => 'Greek', 'native' => 'Ελληνικά', 'direction' => 'ltr'],
            ['code' => 'gu', 'name' => 'Gujarati', 'native' => 'ગુજરાતી', 'direction' => 'ltr'],
            ['code' => 'he', 'name' => 'Hebrew', 'native' => 'עברית', 'direction' => 'rtl'],
            ['code' => 'hi', 'name' => 'Hindi', 'native' => 'हिन्दी', 'direction' => 'ltr'],
            ['code' => 'hu', 'name' => 'Hungarian', 'native' => 'magyar', 'direction' => 'ltr'],
            ['code' => 'id', 'name' => 'Indonesian', 'native' => 'Bahasa Indonesia', 'direction' => 'ltr'],
            ['code' => 'it', 'name' => 'Italian', 'native' => 'Italiano', 'direction' => 'ltr'],
            ['code' => 'ja', 'name' => 'Japanese', 'native' => '日本語', 'direction' => 'ltr'],
            ['code' => 'km', 'name' => 'Khmer', 'native' => 'ខ្មែរ', 'direction' => 'ltr'],
            ['code' => 'ko', 'name' => 'Korean', 'native' => '한국어', 'direction' => 'ltr'],
            ['code' => 'lo', 'name' => 'Lao', 'native' => 'ລາວ', 'direction' => 'ltr'],
            ['code' => 'ms', 'name' => 'Malay', 'native' => 'Bahasa Melayu', 'direction' => 'ltr'],
            ['code' => 'ml', 'name' => 'Malayalam', 'native' => 'മലയാളം', 'direction' => 'ltr'],
            ['code' => 'my', 'name' => 'Myanmar (Burmese)', 'native' => 'ဗမာစာ', 'direction' => 'ltr'],
            ['code' => 'ne', 'name' => 'Nepali', 'native' => 'नेपाली', 'direction' => 'ltr'],
            ['code' => 'no', 'name' => 'Norwegian', 'native' => 'norsk', 'direction' => 'ltr'],
            ['code' => 'pa', 'name' => 'Punjabi', 'native' => 'ਪੰਜਾਬੀ', 'direction' => 'ltr'],
            ['code' => 'pl', 'name' => 'Polish', 'native' => 'polski', 'direction' => 'ltr'],
            ['code' => 'pt', 'name' => 'Portuguese', 'native' => 'Português', 'direction' => 'ltr'],
            ['code' => 'pt-BR', 'name' => 'Portuguese (Brazil)', 'native' => 'Português (Brasil)', 'direction' => 'ltr'],
            ['code' => 'ro', 'name' => 'Romanian', 'native' => 'română', 'direction' => 'ltr'],
            ['code' => 'ru', 'name' => 'Russian', 'native' => 'Русский', 'direction' => 'ltr'],
            ['code' => 'si', 'name' => 'Sinhala', 'native' => 'සිංහල', 'direction' => 'ltr'],
            ['code' => 'es', 'name' => 'Spanish', 'native' => 'Español', 'direction' => 'ltr'],
            ['code' => 'sv', 'name' => 'Swedish', 'native' => 'svenska', 'direction' => 'ltr'],
            ['code' => 'ta', 'name' => 'Tamil', 'native' => 'தமிழ்', 'direction' => 'ltr'],
            ['code' => 'te', 'name' => 'Telugu', 'native' => 'తెలుగు', 'direction' => 'ltr'],
            ['code' => 'th', 'name' => 'Thai', 'native' => 'ไทย', 'direction' => 'ltr'],
            ['code' => 'tr', 'name' => 'Turkish', 'native' => 'Türkçe', 'direction' => 'ltr'],
            ['code' => 'uk', 'name' => 'Ukrainian', 'native' => 'Українська', 'direction' => 'ltr'],
            ['code' => 'ur', 'name' => 'Urdu', 'native' => 'اردو', 'direction' => 'rtl'],
            ['code' => 'vi', 'name' => 'Vietnamese', 'native' => 'Tiếng Việt', 'direction' => 'ltr'],
        ];

        $availableLanguages = collect($allLanguages)->filter(function ($lang) use ($enabledCodes) {
            return ! in_array($lang['code'], $enabledCodes);
        })->values();
    @endphp

    <div id="settingsContainer">
        <div class="settings-section active" id="languages-settings">
            <form action="{{ route('admin.settings.update', ['tab' => LANGUAGE_SETTINGS]) }}" method="POST">
                @csrf

                {{-- Default Language --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-globe mr-2"></i>{{ translate('messages.Default Language') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="defaultLanguage">{{ translate('messages.Select Default Language') }}</label>
                                    <select name="default_language" id="defaultLanguage" class="form-control">
                                        @foreach($enabledLanguages as $lang)
                                            <option value="{{ $lang['code'] }}" {{ $defaultLanguage === $lang['code'] ? 'selected' : '' }}>
                                                {{ $lang['name'] }} — {{ $lang['native'] ?? '' }} ({{ strtoupper($lang['code']) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">{{ translate('messages.Content will fall back to this language when a translation is missing.') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Enabled Languages --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-language mr-2"></i>{{ translate('messages.Enabled Languages') }}
                        </h6>
                        <div id="langBadges" class="d-flex flex-wrap gap-1">
                            @foreach($enabledLanguages as $lang)
                                <span class="badge badge-outline badge-lang-{{ $lang['code'] }}
                                    @if($defaultLanguage === $lang['code'])
                                        badge-success
                                    @elseif(($lang['direction'] ?? 'ltr') === 'rtl')
                                        badge-warning
                                    @else
                                        badge-primary
                                    @endif
                                ">{{ strtoupper($lang['code']) }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ translate('messages.Language') }}</th>
                                    <th style="width: 100px;">{{ translate('messages.Code') }}</th>
                                    <th style="width: 100px;">{{ translate('messages.Direction') }}</th>
                                    <th style="width: 80px;">{{ translate('messages.Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody id="languageRows">
                                @foreach($enabledLanguages as $lang)
                                    <tr data-code="{{ $lang['code'] }}">
                                        <td class="align-middle">
                                            <strong>{{ $lang['name'] }}</strong>
                                            <span class="text-muted"> — {{ $lang['native'] ?? '' }}</span>
                                            <input type="hidden" name="enabled_languages[{{ $loop->index }}][code]" value="{{ $lang['code'] }}">
                                            <input type="hidden" name="enabled_languages[{{ $loop->index }}][name]" value="{{ $lang['name'] }}">
                                            <input type="hidden" name="enabled_languages[{{ $loop->index }}][direction]" value="{{ $lang['direction'] }}">
                                            <input type="hidden" name="enabled_languages[{{ $loop->index }}][native]" value="{{ $lang['native'] ?? $lang['name'] }}">
                                        </td>
                                        <td class="align-middle"><code>{{ strtoupper($lang['code']) }}</code></td>
                                        <td class="align-middle">
                                            @if(($lang['direction'] ?? 'ltr') === 'rtl')
                                                <span class="badge badge-warning"><i class="fas fa-arrow-left mr-1"></i>RTL</span>
                                            @else
                                                <span class="badge badge-light text-dark"><i class="fas fa-arrow-right mr-1"></i>LTR</span>
                                            @endif
                                        </td>
                                        <td class="text-center align-middle">
                                            @if($defaultLanguage === $lang['code'])
                                                <span class="text-muted" title="{{ translate('messages.Cannot remove default language') }}">
                                                    <i class="fas fa-lock"></i>
                                                </span>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-danger remove-lang-row" title="{{ translate('messages.Remove') }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Add Language --}}
                @if($availableLanguages->isNotEmpty())
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-plus-circle mr-2"></i>{{ translate('messages.Add Language') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-row align-items-end">
                            <div class="form-group col-md-8 mb-0">
                                <label for="newLanguageSelect">{{ translate('messages.Select a language to add') }}</label>
                                <select id="newLanguageSelect" class="form-control select2">
                                    <option value="">{{ translate('messages.Choose a language...') }}</option>
                                    @foreach($availableLanguages as $lang)
                                        <option value="{{ $lang['code'] }}"
                                                data-name="{{ $lang['name'] }}"
                                                data-native="{{ $lang['native'] }}"
                                                data-direction="{{ $lang['direction'] }}">
                                            {{ $lang['name'] }} — {{ $lang['native'] }} ({{ strtoupper($lang['code']) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-0">
                                <button type="button" class="btn btn-success btn-block" id="addLanguageBtn" disabled>
                                    <i class="fas fa-plus mr-1"></i> {{ translate('messages.Add Language') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-2"></i>{{ translate('messages.Save Settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let langIndex = {{ count($enabledLanguages) }};
    let currentDefault = '{{ $defaultLanguage }}';

    $('#newLanguageSelect').select2({
        width: '100%',
        placeholder: '{{ translate("messages.Choose a language...") }}',
        allowClear: true,
    });

    $('#newLanguageSelect').on('select2:select', function() {
        $('#addLanguageBtn').prop('disabled', false);
    }).on('select2:clear', function() {
        $('#addLanguageBtn').prop('disabled', true);
    });

    // When default language dropdown changes, lock/unlock rows accordingly
    $('#defaultLanguage').on('change', function() {
        currentDefault = $(this).val();
        refreshDefaultBadges();
        refreshRemoveButtons();
    });

    function refreshDefaultBadges() {
        $('#languageRows tr').each(function() {
            const code = $(this).data('code');
            const nameTd = $(this).find('td:first');
            // Remove old default badge
            nameTd.find('.badge-success').remove();
            // Add to the matching row
            if (code === currentDefault) {
                nameTd.find('.text-muted').after(' <span class="badge badge-success ml-1">{{ translate("messages.Default") }}</span>');
            }
        });
    }

    function refreshRemoveButtons() {
        const removeTitle = "{{ translate('messages.Remove') }}";
        const lockTitle = "{{ translate('messages.Cannot remove default language') }}";
        $('#languageRows tr').each(function() {
            const code = $(this).data('code');
            const actionTd = $(this).find('td:last');
            if (code === currentDefault) {
                actionTd.html('<span class="text-muted" title="' + lockTitle + '"><i class="fas fa-lock"></i></span>');
            } else {
                actionTd.html('<button type="button" class="btn btn-sm btn-outline-danger remove-lang-row" title="' + removeTitle + '"><i class="fas fa-trash-alt"></i></button>');
            }
        });
    }

    function refreshHeaderBadges() {
        const badges = [];
        $('#languageRows tr').each(function() {
            const code = $(this).data('code');
            const direction = $(this).find('input[name*="[direction]"]').val();
            let cls = 'badge-primary';
            if (code === currentDefault) cls = 'badge-success';
            else if (direction === 'rtl') cls = 'badge-warning';
            badges.push('<span class="badge ' + cls + ' badge-lang-' + code + '">' + code.toUpperCase() + '</span>');
        });
        $('#langBadges').html(badges.join(''));
    }

    // Add language
    $('#addLanguageBtn').on('click', function() {
        const select = $('#newLanguageSelect');
        const selected = select.find('option:selected');
        if (!selected.val()) return;

        const code = selected.val();
        const name = selected.data('name');
        const native = selected.data('native');
        const direction = selected.data('direction');
        const dirBadge = direction === 'rtl'
            ? '<span class="badge badge-warning"><i class="fas fa-arrow-left mr-1"></i>RTL</span>'
            : '<span class="badge badge-light text-dark"><i class="fas fa-arrow-right mr-1"></i>LTR</span>';

        const row = `
            <tr data-code="${code}">
                <td class="align-middle">
                    <strong>${name}</strong>
                    <span class="text-muted"> — ${native}</span>
                    <input type="hidden" name="enabled_languages[${langIndex}][code]" value="${code}">
                    <input type="hidden" name="enabled_languages[${langIndex}][name]" value="${name}">
                    <input type="hidden" name="enabled_languages[${langIndex}][direction]" value="${direction}">
                    <input type="hidden" name="enabled_languages[${langIndex}][native]" value="${native}">
                </td>
                <td class="align-middle"><code>${code.toUpperCase()}</code></td>
                <td class="align-middle">${dirBadge}</td>
                <td class="text-center align-middle">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-lang-row" title="{{ translate('messages.Remove') }}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        `;
        $('#languageRows').append(row);
        langIndex++;

        selected.remove();
        select.val(null).trigger('change');
        $('#addLanguageBtn').prop('disabled', true);

        updateDefaultLanguageOptions();
        refreshHeaderBadges();
        refreshDefaultBadges();
    });

    // Remove language — block if it is the current default
    $(document).on('click', '.remove-lang-row', function() {
        const row = $(this).closest('tr');
        const code = row.data('code');

        if (code === currentDefault) {
            Swal.fire({
                icon: 'warning',
                title: '{{ translate("messages.Cannot Remove") }}',
                text: '{{ translate("messages.Please change the default language before removing it.") }}',
            });
            return;
        }

        if ($('#languageRows tr').length <= 1) {
            Swal.fire({
                icon: 'warning',
                title: '{{ translate("messages.Cannot Remove") }}',
                text: '{{ translate("messages.At least one language must be enabled.") }}',
            });
            return;
        }

        const name = row.find('input[name*="[name]"]').val();
        const native = row.find('input[name*="[native]"]').val();
        const direction = row.find('input[name*="[direction]"]').val();

        row.remove();

        // Add back to dropdown
        const newOption = new Option(`${name} — ${native} (${code.toUpperCase()})`, code, false, false);
        $(newOption).data('name', name).data('native', native).data('direction', direction);
        $('#newLanguageSelect').append(newOption).trigger('change');

        updateDefaultLanguageOptions();
        refreshHeaderBadges();
    });

    function updateDefaultLanguageOptions() {
        const options = [];
        $('#languageRows tr').each(function() {
            const code = $(this).data('code');
            const name = $(this).find('input[name*="[name]"]').val();
            const native = $(this).find('input[name*="[native]"]').val();
            if (code) {
                options.push(`<option value="${code}" ${code === currentDefault ? 'selected' : ''}>${name} — ${native} (${code.toUpperCase()})</option>`);
            }
        });
        $('#defaultLanguage').html(options.join(''));
    }
});
</script>
@endpush
