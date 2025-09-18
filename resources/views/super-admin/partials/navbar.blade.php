<!-- Navbar -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('super-admin.dashboard') }}" class="nav-link">{{ __('Dashboard') }}</a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('super-admin.tenants.index') }}" class="nav-link">{{ __('Tenants') }}</a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Language Toggle -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-globe"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a href="{{ route('locale', 'en') }}" class="dropdown-item">
                    <i class="fas fa-flag-usa mr-2"></i> English
                </a>
                <a href="{{ route('locale', 'ar') }}" class="dropdown-item">
                    <i class="fas fa-flag mr-2"></i> العربية
                </a>
            </div>
        </li>

        <!-- User Account -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user-circle"></i>
                {{ __('Super Admin') }}
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a href="#" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> {{ __('Profile') }}
                </a>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-cog mr-2"></i> {{ __('Settings') }}
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-sign-out-alt mr-2"></i> {{ __('Logout') }}
                </a>
            </div>
        </li>
    </ul>
</nav>
