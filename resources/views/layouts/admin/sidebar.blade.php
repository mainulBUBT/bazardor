<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion d-flex flex-column" id="accordionSidebar" style="min-height: 100vh;">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{url('/admin')}}">
        <div class="sidebar-brand-icon">
            <i class="fas fa-store"></i>
        </div>
        <div class="sidebar-brand-text mx-3">{{translate('messages.BazarDor Admin') }}</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Heading -->
    <div class="sidebar-heading">
        {{translate('messages.Main') }}
    </div>

    <!-- Dashboard -->
    <li class="nav-item {{ request()->is('admin') ? 'active' : '' }}">
        <a class="nav-link" href="{{url('/admin/dashboard')}}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>{{translate('messages.Dashboard') }}</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        {{translate('messages.Catalog') }}
    </div>

    <!-- Units -->
    <li class="nav-item {{ request()->is('admin/units*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.units.index') }}">
            <i class="fas fa-fw fa-ruler-combined"></i>
            <span>{{translate('messages.Units') }}</span>   
        </a>
    </li>

    <!-- Zones -->
    <li class="nav-item {{ request()->is('admin/zones*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.zones.index') }}">
            <i class="fas fa-fw fa-map-marked-alt"></i>
            <span>{{translate('messages.Zones') }}</span>   
        </a>
    </li>

    <!-- Categories -->
    <li class="nav-item {{ request()->is('admin/categories*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCategories" aria-expanded="{{ request()->is('admin/categories*') ? 'true' : 'false' }}" aria-controls="collapseCategories">
            <i class="fas fa-fw fa-images"></i>
            <span>{{translate('messages.Categories') }}</span>
        </a>
        <div id="collapseCategories" class="collapse{{ request()->is('admin/categories*') ? ' show' : '' }}" aria-labelledby="headingCategories" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('admin/categories') ? 'active' : '' }}" href="{{route('admin.categories.index')}}">{{translate('messages.All Categories') }}</a>
                <a class="collapse-item {{ request()->is('admin/categories/create') ? 'active' : '' }}" href="{{route('admin.categories.create')}}">{{translate('messages.Add Category') }}</a>
            </div>
        </div>
    </li>

    <!-- Products -->
    <li class="nav-item {{ request()->is('admin/products*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseProducts" aria-expanded="{{ request()->is('admin/products*') ? 'true' : 'false' }}" aria-controls="collapseProducts">
            <i class="fas fa-fw fa-box-open"></i>
            <span>{{translate('messages.Products') }}</span>
        </a>
        <div id="collapseProducts" class="collapse{{ request()->is('admin/products*') ? ' show' : '' }}" aria-labelledby="headingProducts" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('admin/products') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">{{translate('messages.All Products') }}</a>
                <a class="collapse-item {{ request()->is('admin/products/create') ? 'active' : '' }}" href="{{ route('admin.products.create') }}">{{translate('messages.Add Product') }}</a>
            </div>
        </div>
    </li>

    <!-- Markets -->
    <li class="nav-item {{ request()->is('admin/markets*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMarkets" aria-expanded="{{ request()->is('admin/markets*') ? 'true' : 'false' }}" aria-controls="collapseMarkets">
            <i class="fas fa-fw fa-images"></i>
            <span>{{translate('messages.Markets') }}</span>
        </a>
        <div id="collapseMarkets" class="collapse{{ request()->is('admin/markets*') ? ' show' : '' }}" aria-labelledby="headingMarkets" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('admin/markets') ? 'active' : '' }}" href="{{route('admin.markets.index')}}">{{translate('messages.All Markets') }}</a>
                <a class="collapse-item {{ request()->is('admin/markets/create') ? 'active' : '' }}" href="{{route('admin.markets.create')}}">{{translate('messages.Add Market') }}</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        {{translate('messages.Users Management') }}
    </div>

    <!-- Users Management Collapsible Section -->
    <li class="nav-item {{ request()->is('admin/users*') || request()->is('admin/volunteers*') || request()->is('admin/moderators*') || request()->is('admin/roles*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUsers" aria-expanded="{{ request()->is('admin/users*') || request()->is('admin/volunteers*') || request()->is('admin/moderators*') || request()->is('admin/roles*') ? 'true' : 'false' }}" aria-controls="collapseUsers">
            <i class="fas fa-fw fa-users"></i>
            <span>{{translate('messages.Users Management') }}</span>
        </a>
        <div id="collapseUsers" class="collapse{{ request()->is('admin/users*') || request()->is('admin/volunteers*') || request()->is('admin/moderators*') || request()->is('admin/roles*') ? ' show' : '' }}" aria-labelledby="headingUsers" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('admin/users*') && !request()->is('admin/users/pending') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">{{translate('messages.Users')}}</a>
                <a class="collapse-item {{ request()->is('admin/users/pending') ? 'active' : '' }}" href="{{ route('admin.users.pending') }}">{{translate('messages.Pending Users')}}</a>
                <a class="collapse-item {{ request()->is('admin/roles*') ? 'active' : '' }}" href="{{ route('admin.roles.index') }}">{{translate('messages.Roles & Permissions')}}</a>
            </div>
        </div>
    </li>

    <!-- Contributions -->
    <li class="nav-item {{ request()->is('admin/contributions*') ? 'active' : '' }}">
        <a class="nav-link" href="{{url('/admin/contributions')}}">
            <i class="fas fa-fw fa-chart-line"></i>
            <span>{{translate('messages.Contributions') }}</span>
        </a>
    </li>

    <!-- Points Management -->
    <li class="nav-item {{ request()->is('admin/points*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePoints" aria-expanded="false" aria-controls="collapsePoints">
            <i class="fas fa-fw fa-award"></i>
            <span>{{translate('messages.Points Management') }}</span>
        </a>
        <div id="collapsePoints" class="collapse" aria-labelledby="headingPoints" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{url('/admin/points/manage')}}">{{translate('messages.Manage Points') }}</a>
                <a class="collapse-item" href="{{url('/admin/points/redemptions')}}">{{translate('messages.Redemptions') }}</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        {{translate('messages.Marketing') }}
    </div>

    <!-- Push Notifications -->
    <li class="nav-item {{ request()->is('admin/notifications*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePushNotifications" aria-expanded="false" aria-controls="collapsePushNotifications">
            <i class="fas fa-fw fa-bell"></i>
            <span>{{translate('messages.Push Notifications') }}</span>
        </a>
        <div id="collapsePushNotifications" class="collapse" aria-labelledby="headingPushNotifications" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{url('/admin/notifications')}}">{{translate('messages.All Notifications') }}</a>
                <a class="collapse-item" href="{{url('/admin/notifications/create')}}">{{translate('messages.Send Notification') }}</a>
            </div>
        </div>
    </li>

    <!-- Banners -->
    <li class="nav-item {{ request()->is('admin/banners*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseBanners" aria-expanded="{{ request()->is('admin/banners*') ? 'true' : 'false' }}" aria-controls="collapseBanners">
            <i class="fas fa-fw fa-images"></i>
            <span>{{translate('messages.Banners') }}</span>
        </a>
        <div id="collapseBanners" class="collapse{{ request()->is('admin/banners*') ? ' show' : '' }}" aria-labelledby="headingBanners" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item {{ request()->is('admin/banners') ? 'active' : '' }}" href="{{url('/admin/banners')}}">{{translate('messages.All Banners') }}</a>
                <a class="collapse-item {{ request()->is('admin/banners/create') ? 'active' : '' }}" href="{{url('/admin/banners/create')}}">{{translate('messages.Add Banner') }}</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        {{translate('messages.Reports') }}
    </div>

    <!-- Market Reports -->
    <li class="nav-item {{ request()->is('admin/reports/markets*') ? 'active' : '' }}">
        <a class="nav-link" href="{{url('/admin/reports/markets')}}">
            <i class="fas fa-fw fa-store-alt"></i>
            <span>{{translate('messages.Market Reports') }}</span>
        </a>
    </li>

    <!-- Product Reports -->
    <li class="nav-item {{ request()->is('admin/reports/products*') ? 'active' : '' }}">
        <a class="nav-link" href="{{url('/admin/reports/products')}}">
            <i class="fas fa-fw fa-box"></i>
            <span>{{translate('messages.Product Reports') }}</span>
        </a>
    </li>

    <!-- Price Analytics -->
    <li class="nav-item {{ request()->is('admin/reports/prices*') ? 'active' : '' }}">
        <a class="nav-link" href="{{url('/admin/reports/prices')}}">
            <i class="fas fa-fw fa-chart-line"></i>
            <span>{{translate('messages.Price Analytics') }}</span>
        </a>
    </li>

    <!-- User Analytics -->
    <li class="nav-item {{ request()->is('admin/reports/users*') ? 'active' : '' }}">
        <a class="nav-link" href="{{url('/admin/reports/users')}}">
            <i class="fas fa-fw fa-user-chart"></i>
            <span>{{translate('messages.User Analytics') }}</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        {{translate('messages.Configuration') }}
    </div>

    <!-- Settings -->
    <li class="nav-item {{ request()->is('admin/settings*') ? 'active' : '' }}">
        <a class="nav-link" href="{{route('admin.settings.index')}}">
            <i class="fas fa-fw fa-cog"></i>
            <span>{{translate('messages.Settings') }}</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Spacer to push toggle button to bottom -->
    <div class="flex-grow-1"></div>

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline mt-auto p-3">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
</ul>
<!-- End of Sidebar -->
