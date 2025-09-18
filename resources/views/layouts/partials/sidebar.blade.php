<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-users"></i>
        </div>
        <div class="sidebar-brand-text mx-3">{{ __('HR System') }}</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>{{ __('Dashboard') }}</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        {{ __('Management') }}
    </div>

    <!-- Nav Item - Employees -->
    <li class="nav-item {{ request()->routeIs('employees.*') ? 'active' : '' }}">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-users"></i>
            <span>{{ __('Employees') }}</span>
        </a>
    </li>

    <!-- Nav Item - Departments -->
    <li class="nav-item {{ request()->routeIs('departments.*') ? 'active' : '' }}">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-building"></i>
            <span>{{ __('Departments') }}</span>
        </a>
    </li>

    <!-- Nav Item - Positions -->
    <li class="nav-item {{ request()->routeIs('positions.*') ? 'active' : '' }}">
        <a class="nav-link" href="#">
            <i class="fas fa-fw fa-briefcase"></i>
            <span>{{ __('Positions') }}</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        {{ __('Reports') }}
    </div>

    <!-- Nav Item - Reports -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReports" aria-expanded="true" aria-controls="collapseReports">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>{{ __('Reports') }}</span>
        </a>
        <div id="collapseReports" class="collapse" aria-labelledby="headingReports" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">{{ __('Report Categories') }}:</h6>
                <a class="collapse-item" href="#">{{ __('Employee Reports') }}</a>
                <a class="collapse-item" href="#">{{ __('Department Reports') }}</a>
                <a class="collapse-item" href="#">{{ __('Salary Reports') }}</a>
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->
