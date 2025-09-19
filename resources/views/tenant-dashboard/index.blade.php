{{-- 
Author: Eng.Fahed
Tenant Dashboard Index View - HR System
Tenant Admin Dashboard with comprehensive statistics for {{ trans('messages.Users') }} and {{ trans('messages.Roles') }}
--}}

@extends('layouts.app')

@section('title', trans('messages.User Management Dashboard'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.User Management Dashboard') }}</h1>
        <p class="text-muted">{{ trans('messages.Comprehensive overview of users, roles and permissions') }}</p>
    </div>
    <div>
        <a href="{{ route('tenant-dashboard.detailed-report') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm mr-2">
            <i class="fas fa-file-alt fa-sm text-white-50"></i> {{ trans('messages.Detailed Report') }}
        </a>
        <button class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm" onclick="exportReport()">
            <i class="fas fa-download fa-sm text-white-50"></i> {{ trans('messages.Export Data') }}
        </button>
    </div>
</div>

<!-- Content Row - Main Stats -->
<div class="row">
    <!-- Total Users Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            {{ trans('messages.Total Users') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Users Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            {{ trans('messages.Active Users') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeUsers }}</div>
                        <div class="text-xs text-muted">{{ trans('messages.Last 30 Days') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Roles Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">{{ trans('messages.Roles') }} {{ trans('messages.Available') }}</div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $totalRoles }}</div>
                            </div>
                            <div class="col">
                                <div class="progress progress-sm mr-2">
                                    <div class="progress-bar bg-info" role="progressbar" 
                                         style="width: {{ $totalRoles > 0 ? ($rolesWithUsers / $totalRoles) * 100 : 0 }}%" 
                                         aria-valuenow="{{ $rolesWithUsers }}" aria-valuemin="0" aria-valuemax="{{ $totalRoles }}"></div>
                                </div>
                            </div>
                        </div>
                        <div class="text-xs text-muted">{{ $rolesWithUsers }} {{ trans('messages.User') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Permissions Card -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            {{ trans('messages.Total Permissions') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalPermissions }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-key fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Content Row - Charts and Details -->
<div class="row">
    <!-- Users by Role Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">{{ trans('messages.Distribution') }} {{ trans('messages.User') }} {{ trans('messages.by') }} {{ trans('messages.Roles') }}</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                        <div class="dropdown-header">{{ trans('messages.Display Options') }}: </div>
                        <a class="dropdown-item" href="#" onclick="updateChart('pie')">{{ trans('messages.Pie Chart') }}</a>
                        <a class="dropdown-item" href="#" onclick="updateChart('bar')">{{ trans('messages.Bar Chart') }}</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="usersRoleChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Roles -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ trans('messages.Roles') }} {{ trans('messages.Most Used') }}</h6>
            </div>
            <div class="card-body">
                @forelse($popularRoles as $role)
                <div class="d-flex align-items-center mb-3">
                    <div class="mr-3">
                        <div class="icon-circle bg-primary">
                            <i class="fas fa-user-shield text-white"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">{{ $role->name }}</h6>
                        <small class="text-muted">{{ $role->permissions->count() }} {{ trans('messages.Permission') }}</small>
                        <div class="progress progress-sm mt-1">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 style="width: {{ $totalUsers > 0 ? ($role->users_count / $totalUsers) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="badge badge-primary badge-pill">{{ $role->users_count }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted">
                    <i class="fas fa-info-circle mb-2"></i>
                    <p>{{ trans('messages.No') }} {{ trans('messages.Roles') }} {{ trans('messages.User') }} {{ trans('messages.currently') }}</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Content Row - Recent Activity and Permissions -->
<div class="row">
    <!-- Recent Users -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ trans('messages.New Users') }}</h6>
            </div>
            <div class="card-body">
                @forelse($newUsers as $user)
                <div class="d-flex align-items-center mb-3">
                    <div class="mr-3">
                        <div class="icon-circle bg-success">
                            <span class="text-white font-weight-bold text-sm">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </span>
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">{{ $user->name }}</h6>
                        <small class="text-muted">{{ $user->email }}</small>
                        <div class="mt-1">
                            @foreach($user->roles as $role)
                                <span class="badge badge-primary badge-sm">{{ $role->name }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="text-right">
                        <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted">
                    <i class="fas fa-users mb-2"></i>
                    <p>{{ trans('messages.No') }} {{ trans('messages.new') }} {{ trans('messages.Users') }}</p>
                </div>
                @endforelse
                
                @if($newUsers->count() > 0)
                <div class="text-center mt-3">
                    <a href="{{ route('user-management.index') }}" class="btn btn-sm btn-outline-primary">
                        {{ trans('messages.View All') }} {{ trans('messages.User') }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Permission Stats -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ trans('messages.Permissions') }} {{ trans('messages.Most Used') }}</h6>
            </div>
            <div class="card-body">
                @forelse($permissionStats->take(8) as $permission)
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-key text-success mr-2"></i>
                        <span class="text-sm">{{ $permission->name }}</span>
                    </div>
                    <span class="badge badge-secondary">{{ $permission->roles_count }}</span>
                </div>
                @empty
                <div class="text-center text-muted">
                    <i class="fas fa-key mb-2"></i>
                    <p>{{ trans('messages.No') }} {{ trans('messages.Permissions') }} {{ trans('messages.User') }}</p>
                </div>
                @endforelse
                
                @if($permissionStats->count() > 0)
                <div class="text-center mt-3">
                    <a href="{{ route('permissions.index') }}" class="btn btn-sm btn-outline-primary">
                        {{ trans('messages.View All') }} {{ trans('messages.Permissions') }}
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- User Activity Chart -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ trans('messages.Addition Activity') }} {{ trans('messages.User') }} ({{ trans('messages.Last 30 Days') }})</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="userActivityChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ trans('messages.Quick Actions') }}</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @can('users.create')
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('user-management.create') }}" class="btn btn-primary btn-block">
                            <i class="fas fa-user-plus mb-2"></i><br>
                            {{ trans('messages.Add') }} {{ trans('messages.New User') }}
                        </a>
                    </div>
                    @endcan
                    
                    @can('roles.create')
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('roles.create') }}" class="btn btn-success btn-block">
                            <i class="fas fa-user-shield mb-2"></i><br>
                            {{ trans('messages.Add') }} {{ trans('messages.Role') }} {{ trans('messages.new') }}
                        </a>
                    </div>
                    @endcan
                    
                    @can('users.view')
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('user-management.index') }}" class="btn btn-info btn-block">
                            <i class="fas fa-users mb-2"></i><br>
                            {{ trans('messages.Manage') }} {{ trans('messages.User') }}
                        </a>
                    </div>
                    @endcan
                    
                    @can('roles.view')
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('roles.index') }}" class="btn btn-warning btn-block">
                            <i class="fas fa-cogs mb-2"></i><br>
                            {{ trans('messages.Manage') }} ˆ{{ trans('messages.Permissions') }}
                        </a>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Author: Eng.Fahed
// Dashboard Charts and Interactions

// Users by Role Chart
const usersRoleData = @json($usersByRole);
const usersRoleCtx = document.getElementById('usersRoleChart').getContext('2d');
let usersRoleChart = new Chart(usersRoleCtx, {
    type: 'doughnut',
    data: {
        labels: Object.keys(usersRoleData),
        datasets: [{
            data: Object.values(usersRoleData),
            backgroundColor: [
                '#4e73df',
                '#1cc88a',
                '#36b9cc',
                '#f6c23e',
                '#e74a3b',
                '#858796'
            ],
            hoverBackgroundColor: [
                '#2e59d9',
                '#17a673',
                '#2c9faf',
                '#dda20a',
                '#c23321',
                '#6c757d'
            ],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom'
            }
        },
        cutout: '80%',
    },
});

// User Activity Chart
const userActivity = @json($userActivity);
const userActivityCtx = document.getElementById('userActivityChart').getContext('2d');
const userActivityChart = new Chart(userActivityCtx, {
    type: 'line',
    data: {
        labels: userActivity.map(item => {
            const date = new Date(item.date);
            return date.getDate() + '/' + (date.getMonth() + 1);
        }),
        datasets: [{
            label: "{{ trans('messages.New Users') }}",
            lineTension: 0.3,
            backgroundColor: "rgba(78, 115, 223, 0.05)",
            borderColor: "rgba(78, 115, 223, 1)",
            pointRadius: 3,
            pointBackgroundColor: "rgba(78, 115, 223, 1)",
            pointBorderColor: "rgba(78, 115, 223, 1)",
            pointHoverRadius: 3,
            pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
            pointHoverBorderColor: "rgba(78, 115, 223, 1)",
            pointHitRadius: 10,
            pointBorderWidth: 2,
            data: userActivity.map(item => item.count),
        }],
    },
    options: {
        maintainAspectRatio: false,
        scales: {
            x: {
                grid: {
                    display: false,
                    drawBorder: false,
                }
            },
            y: {
                ticks: {
                    beginAtZero: true,
                    stepSize: 1
                },
                grid: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                }
            },
        },
        plugins: {
            legend: {
                display: false
            }
        }
    },
});

// Update chart type
function updateChart(type) {
    usersRoleChart.config.type = type;
    usersRoleChart.update();
}

// Export report
function exportReport() {
    window.open('{{ route("tenant-dashboard.export-report") }}?format=json', '_blank');
}

// Auto refresh data every 5 minutes
setInterval(function() {
    location.reload();
}, 300000);
</script>
@endpush

@push('styles')
<style>
/* Author: Eng.Fahed */
.icon-circle {
    height: 2.5rem;
    width: 2.5rem;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.progress-sm {
    height: 0.5rem;
}

.badge-sm {
    font-size: 0.65rem;
}

.chart-area {
    position: relative;
    height: 300px;
}

/* RTL Support */
@if(app()->getLocale() == 'ar')
.dropdown-menu-right {
    right: 0;
    left: auto;
}
@endif
</style>
@endpush
