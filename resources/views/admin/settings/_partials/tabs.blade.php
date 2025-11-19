<!-- Settings Navigation -->
<div class="settings-nav">
    <a href="{{ route('admin.settings.index', ['tab' => 'general']) }}" class="settings-nav-item {{ request()->query('tab') == 'general' || !request()->query('tab') ? 'active' : '' }}" data-target="general">
        <i class="fas fa-cog mr-1"></i> {{ translate('messages.General') }}
    </a>
    <a href="{{ route('admin.settings.index', ['tab' => 'business']) }}" class="settings-nav-item {{ request()->query('tab') == 'business' ? 'active' : '' }}" data-target="business">
        <i class="fas fa-chart-line mr-1"></i> {{ translate('messages.Business Rules') }}
    </a>
    <a href="{{ route('admin.settings.index', ['tab' => 'notifications']) }}" class="settings-nav-item {{ request()->query('tab') == 'notifications' ? 'active' : '' }}" data-target="notifications">
        <i class="fas fa-bell mr-1"></i> {{ translate('messages.Notifications') }}
    </a>
    <a href="{{ route('admin.settings.index', ['tab' => 'mail']) }}" class="settings-nav-item {{ request()->query('tab') == 'mail' ? 'active' : '' }}" data-target="mail">
        <i class="fas fa-envelope mr-1"></i> {{ translate('messages.Mail') }}
    </a>
    <a href="{{ route('admin.settings.index', ['tab' => SOCIAL_SETTINGS]) }}" class="settings-nav-item {{ request()->query('tab') == SOCIAL_SETTINGS ? 'active' : '' }}" data-target="{{ SOCIAL_SETTINGS }}">
        <i class="fas fa-user-friends mr-1"></i> {{ translate('messages.Social Connect') }}
    </a>
    <a href="{{ route('admin.settings.index', ['tab' => 'app']) }}" class="settings-nav-item {{ request()->query('tab') == 'app' ? 'active' : '' }}" data-target="app">
        <i class="fas fa-mobile-alt mr-1"></i> {{ translate('messages.App Settings') }}
    </a>
    <a href="{{ route('admin.settings.index', ['tab' => 'backup']) }}" class="settings-nav-item {{ request()->query('tab') == 'backup' ? 'active' : '' }}" data-target="backup">
        <i class="fas fa-database mr-1"></i> {{ translate('messages.Backup & Maintenance') }}
    </a>
    <a href="{{ route('admin.settings.index', ['tab' => OTHER_SETTINGS]) }}" class="settings-nav-item {{ request()->query('tab') == OTHER_SETTINGS ? 'active' : '' }}" data-target="{{ OTHER_SETTINGS }}">
        <i class="fas fa-sliders-h mr-1"></i> {{ translate('messages.Others') }}
    </a>
</div>