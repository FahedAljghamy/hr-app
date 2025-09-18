<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('super-admin.dashboard') }}" class="brand-link">
        <i class="fas fa-crown brand-image"></i>
        <span class="brand-text font-weight-light">{{ __('Super Admin') }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                
                <!-- Dashboard -->
                <li class="nav-item {{ request()->routeIs('super-admin.dashboard') ? 'menu-open' : '' }}">
                    <a href="{{ route('super-admin.dashboard') }}" class="nav-link {{ request()->routeIs('super-admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>{{ __('Dashboard') }}</p>
                    </a>
                </li>

                <!-- Tenants Management -->
                <li class="nav-item {{ request()->routeIs('super-admin.tenants.*') ? 'menu-open' : '' }}">
                    <a href="#" class="nav-link {{ request()->routeIs('super-admin.tenants.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-building"></i>
                        <p>
                            {{ __('Tenants') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('super-admin.tenants.index') }}" class="nav-link {{ request()->routeIs('super-admin.tenants.index') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('All Tenants') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('super-admin.tenants.create') }}" class="nav-link {{ request()->routeIs('super-admin.tenants.create') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('Add Tenant') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Users Management -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            {{ __('Users') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('All Users') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('Super Admins') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Reports -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            {{ __('Reports') }}
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('Revenue Report') }}</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>{{ __('Usage Statistics') }}</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Settings -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>{{ __('System Settings') }}</p>
                    </a>
                </li>

                <li class="nav-header">{{ __('Quick Actions') }}</li>
                
                <li class="nav-item">
                    <a href="{{ route('super-admin.tenants.create') }}" class="nav-link">
                        <i class="nav-icon fas fa-plus text-success"></i>
                        <p>{{ __('Add New Tenant') }}</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-download text-info"></i>
                        <p>{{ __('Export Data') }}</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>
