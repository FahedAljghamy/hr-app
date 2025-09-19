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

    <!-- Nav Item - User Management -->
    @canany(['users.view', 'roles.view', 'permissions.view', 'dashboard.view'])
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUserManagement" aria-expanded="true" aria-controls="collapseUserManagement">
            <i class="fas fa-fw fa-users-cog"></i>
            <span>{{ trans('messages.User Management') }}</span>
        </a>
        <div id="collapseUserManagement" class="collapse" aria-labelledby="headingUserManagement" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">{{ trans('messages.Management Options') }}:</h6>
                @can('dashboard.view')
                <a class="collapse-item" href="{{ route('tenant-dashboard.index') }}">{{ trans('messages.Dashboard') }}</a>
                @endcan
                @can('users.view')
                <a class="collapse-item" href="{{ route('user-management.index') }}">{{ trans('messages.Users') }}</a>
                @endcan
                @can('roles.view')
                <a class="collapse-item" href="{{ route('roles.index') }}">{{ trans('messages.Roles') }}</a>
                @endcan
                @can('permissions.view')
                <a class="collapse-item" href="{{ route('permissions.index') }}">{{ trans('messages.Permissions') }}</a>
                @endcan
                @can('roles.view')
                <a class="collapse-item" href="{{ route('roles.permissions-map') }}">{{ trans('messages.Roles & Permissions Map') }}</a>
                @endcan
            </div>
        </div>
    </li>
        @endcanany

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Nav Item - Employee Management -->
        @canany(['employees.view', 'payrolls.view'])
        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseEmployeeManagement" aria-expanded="true" aria-controls="collapseEmployeeManagement">
                <i class="fas fa-fw fa-users"></i>
                <span>{{ trans('messages.Employee Management') }}</span>
            </a>
            <div id="collapseEmployeeManagement" class="collapse" aria-labelledby="headingEmployeeManagement" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">{{ trans('messages.Employee Options') }}:</h6>
                    @can('employees.view')
                    <a class="collapse-item" href="{{ route('employees.index') }}">{{ trans('messages.All Employees') }}</a>
                    @endcan
                    @can('employees.create')
                    <a class="collapse-item" href="{{ route('employees.create') }}">{{ trans('messages.Add Employee') }}</a>
                    @endcan
                    @can('payrolls.view')
                    <a class="collapse-item" href="{{ route('payrolls.index') }}">{{ trans('messages.Payroll Management') }}</a>
                    @endcan
                    @can('payrolls.create')
                    <a class="collapse-item" href="{{ route('payrolls.create') }}">{{ trans('messages.Create Payroll') }}</a>
                    @endcan
                    @can('leaves.view')
                    <a class="collapse-item" href="{{ route('leaves.index') }}">{{ trans('messages.Leave Management') }}</a>
                    @endcan
                    @can('leaves.create')
                    <a class="collapse-item" href="{{ route('leaves.create') }}">{{ trans('messages.Request Leave') }}</a>
                    @endcan
                </div>
            </div>
        </li>
        @endcanany

        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Nav Item - Company Management -->
    @canany(['branches.view', 'company.settings.view'])
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCompanyManagement" aria-expanded="true" aria-controls="collapseCompanyManagement">
            <i class="fas fa-fw fa-building"></i>
            <span>{{ trans('messages.Company Management') }}</span>
        </a>
        <div id="collapseCompanyManagement" class="collapse" aria-labelledby="headingCompanyManagement" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">{{ trans('messages.Company Options') }}:</h6>
                @can('branches.view')
                <a class="collapse-item" href="{{ route('branches.index') }}">{{ trans('messages.Branches') }}</a>
                @endcan
                @can('company.settings.view')
                <a class="collapse-item" href="{{ route('company-settings.index') }}">{{ trans('messages.Company Settings') }}</a>
                @endcan
                @can('legal.documents.view')
                <a class="collapse-item" href="{{ route('legal-documents.index') }}">{{ trans('messages.Legal Documents') }}</a>
                @endcan
            </div>
        </div>
    </li>
    @endcanany

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        {{ trans('messages.Reports') }}
    </div>

    <!-- Nav Item - Reports -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseReports" aria-expanded="true" aria-controls="collapseReports">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>{{ trans('messages.Reports') }}</span>
        </a>
        <div id="collapseReports" class="collapse" aria-labelledby="headingReports" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">{{ trans('messages.Report Categories') }}:</h6>
                <a class="collapse-item" href="#">{{ trans('messages.Employee Reports') }}</a>
                <a class="collapse-item" href="#">{{ trans('messages.Department Reports') }}</a>
                <a class="collapse-item" href="#">{{ trans('messages.Salary Reports') }}</a>
            </div>
        </div>
    </li>

        <!-- Employee Dashboard (for regular employees) -->
        @if(auth()->user()->user_type === 'employee')
        <!-- Divider -->
        <hr class="sidebar-divider">

        <!-- Heading -->
        <div class="sidebar-heading">
            {{ trans('messages.My Dashboard') }}
        </div>

        <!-- Nav Item - Employee Dashboard -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('employee-dashboard.index') }}">
                <i class="fas fa-fw fa-tachometer-alt"></i>
                <span>{{ trans('messages.My Dashboard') }}</span>
            </a>
        </li>

        <!-- Nav Item - My Profile -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('employee-dashboard.profile') }}">
                <i class="fas fa-fw fa-user"></i>
                <span>{{ trans('messages.My Profile') }}</span>
            </a>
        </li>

        <!-- Nav Item - My Payrolls -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('employee-dashboard.payrolls') }}">
                <i class="fas fa-fw fa-money-bill-wave"></i>
                <span>{{ trans('messages.My Payrolls') }}</span>
            </a>
        </li>

        <!-- Nav Item - My Documents -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('employee-dashboard.documents') }}">
                <i class="fas fa-fw fa-file-alt"></i>
                <span>{{ trans('messages.My Documents') }}</span>
            </a>
        </li>

        <!-- Nav Item - Leave Balance -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('employee-dashboard.leave-balance') }}">
                <i class="fas fa-fw fa-calendar-check"></i>
                <span>{{ trans('messages.Leave Balance') }}</span>
            </a>
        </li>

        <!-- Nav Item - My Leaves -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('leaves.index') }}">
                <i class="fas fa-fw fa-calendar-plus"></i>
                <span>{{ trans('messages.My Leaves') }}</span>
            </a>
        </li>

        <!-- Nav Item - Request Leave -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('leaves.create') }}">
                <i class="fas fa-fw fa-plus-circle"></i>
                <span>{{ trans('messages.Request Leave') }}</span>
            </a>
        </li>

        <!-- Nav Item - Certificates -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('employee-dashboard.certificates.index') }}">
                <i class="fas fa-fw fa-certificate"></i>
                <span>{{ trans('messages.My Certificates') }}</span>
            </a>
        </li>

        <!-- Nav Item - Request Certificate -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('employee-dashboard.certificates.create') }}">
                <i class="fas fa-fw fa-file-medical"></i>
                <span>{{ trans('messages.Request Certificate') }}</span>
            </a>
        </li>

        <!-- Nav Item - Notifications -->
        <li class="nav-item">
            <a class="nav-link" href="{{ route('employee-dashboard.notifications') }}">
                <i class="fas fa-fw fa-bell"></i>
                <span>{{ trans('messages.My Notifications') }}</span>
                @if(auth()->user()->user_type === 'employee')
                    @php
                        $employee = App\Models\Employee::where('user_id', auth()->id())->first();
                        $unreadCount = $employee ? $employee->getUnreadNotificationsCount() : 0;
                    @endphp
                    @if($unreadCount > 0)
                        <span class="badge badge-danger badge-counter">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                    @endif
                @endif
            </a>
        </li>
        @endif

        <!-- Divider -->
        <hr class="sidebar-divider d-none d-md-block">

        <!-- Sidebar Toggler (Sidebar) -->
        <div class="text-center d-none d-md-inline">
            <button class="rounded-circle border-0" id="sidebarToggle"></button>
        </div>

</ul>
<!-- End of Sidebar -->
