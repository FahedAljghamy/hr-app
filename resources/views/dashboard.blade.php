{{--
/**
 * Author: Eng.Fahed
 * Dashboard View for HR System
 * Main dashboard with statistics and overview
 */ --}}
@extends('layouts.app')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Dashboard') }}</h1>
    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-download fa-sm text-white-50"></i> {{ trans('messages.Generate Report') }}
    </a>
</div>

<!-- Statistics Cards -->
<div class="row">
    <x-stat-card 
        title="{{ trans('messages.Total Employees') }}" 
        :value="$stats['total_employees']" 
        icon="fas fa-users" 
        color="primary" 
        percentage="12%" />
    
    <x-stat-card 
        title="{{ trans('messages.Departments') }}" 
        :value="$stats['total_departments']" 
        icon="fas fa-building" 
        color="success" 
        percentage="4%" />
    
    <x-stat-card 
        title="{{ trans('messages.Active Positions') }}" 
        :value="$stats['active_positions']" 
        icon="fas fa-briefcase" 
        color="info" 
        percentage="8%" />
    
    <x-stat-card 
        title="{{ trans('messages.Pending Leaves') }}" 
        :value="$stats['pending_leaves']" 
        icon="fas fa-calendar-times" 
        color="warning" 
        percentage="15%" 
        percentageColor="text-danger" />
</div>

<!-- Content Row -->
<div class="row">
    <!-- Recent Activities -->
    <div class="col-lg-8">
        <x-card title="{{ trans('messages.Recent Activities') }}" icon="fas fa-chart-line" color="primary">
            <div class="chart-area">
                <canvas id="myAreaChart"></canvas>
            </div>
        </x-card>
    </div>

    <!-- Employee Distribution -->
    <div class="col-lg-4">
        <x-card title="{{ trans('messages.Employee Distribution') }}" icon="fas fa-chart-pie" color="info">
            <div class="chart-pie pt-4 pb-2">
                <canvas id="myPieChart"></canvas>
            </div>
            <div class="mt-4 text-center small">
                <span class="mr-2">
                    <i class="fas fa-circle text-primary"></i> {{ trans('messages.IT Department') }}
                </span>
                <span class="mr-2">
                    <i class="fas fa-circle text-success"></i> {{ trans('messages.HR Department') }}
                </span>
                <span class="mr-2">
                    <i class="fas fa-circle text-info"></i> {{ trans('messages.Finance') }}
                </span>
            </div>
        </x-card>
    </div>
</div>

<!-- Content Row -->
<div class="row">
    <!-- Recent Employees -->
    <div class="col-lg-6">
        <x-data-table 
            title="{{ trans('messages.Recent Employees') }}" 
            icon="fas fa-user-plus" 
            color="success"
            :headers="[__('Name'), __('Department'), __('Position'), __('Join Date')]">
            <tr>
                <td>{{ trans('messages.Ahmad Al-Sweida') }}</td>
                <td>{{ trans('messages.IT Department') }}</td>
                <td>{{ trans('messages.Software Developer') }}</td>
                <td>{{ date('Y-m-d') }}</td>
            </tr>
            <tr>
                <td>{{ trans('messages.Fatima Al-Hassan') }}</td>
                <td>{{ trans('messages.HR Department') }}</td>
                <td>{{ trans('messages.HR Specialist') }}</td>
                <td>{{ date('Y-m-d', strtotime('-1 day')) }}</td>
            </tr>
            <tr>
                <td>{{ trans('messages.Mohammad Al-Karam') }}</td>
                <td>{{ trans('messages.Finance') }}</td>
                <td>{{ trans('messages.Accountant') }}</td>
                <td>{{ date('Y-m-d', strtotime('-2 days')) }}</td>
            </tr>
        </x-data-table>
    </div>

    <!-- Upcoming Events -->
    <div class="col-lg-6">
        <x-card title="{{ trans('messages.Upcoming Events') }}" icon="fas fa-calendar-alt" color="warning">
            <div class="list-group list-group-flush">
                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <div>
                        <h6 class="mb-1">{{ trans('messages.Team Meeting') }}</h6>
                        <p class="mb-1 text-muted">{{ trans('messages.IT Department monthly meeting') }}</p>
                        <small>{{ trans('messages.Tomorrow at 10:00 AM') }}</small>
                    </div>
                    <span class="badge badge-primary badge-pill">{{ trans('messages.Meeting') }}</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <div>
                        <h6 class="mb-1">{{ trans('messages.Performance Review') }}</h6>
                        <p class="mb-1 text-muted">{{ trans('messages.Quarterly performance evaluation') }}</p>
                        <small>{{ trans('messages.Next week') }}</small>
                    </div>
                    <span class="badge badge-success badge-pill">{{ trans('messages.Review') }}</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <div>
                        <h6 class="mb-1">{{ trans('messages.Training Session') }}</h6>
                        <p class="mb-1 text-muted">{{ trans('messages.Laravel advanced training') }}</p>
                        <small>{{ trans('messages.Next month') }}</small>
                    </div>
                    <span class="badge badge-info badge-pill">{{ trans('messages.Training') }}</span>
                </div>
            </div>
        </x-card>
    </div>
</div>
@endsection

@push('scripts')
<!-- Chart.js -->
<script src="{{ asset('assets/vendor/chart.js/Chart.min.js') }}"></script>

<!-- Page level custom scripts -->
<script>
// Area Chart
var ctx = document.getElementById("myAreaChart");
var myLineChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [{
            label: "{{ trans('messages.Employees') }}",
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
            data: [0, 10000, 5000, 15000, 10000, 20000, 15000, 25000, 20000, 30000, 25000, 40000],
        }],
    },
    options: {
        maintainAspectRatio: false,
        layout: {
            padding: {
                left: 10,
                right: 25,
                top: 25,
                bottom: 0
            }
        },
        scales: {
            xAxes: [{
                time: {
                    unit: 'date'
                },
                gridLines: {
                    display: false,
                    drawBorder: false
                },
                ticks: {
                    maxTicksLimit: 7
                }
            }],
            yAxes: [{
                ticks: {
                    maxTicksLimit: 5,
                    padding: 10,
                },
                gridLines: {
                    color: "rgb(234, 236, 244)",
                    zeroLineColor: "rgb(234, 236, 244)",
                    drawBorder: false,
                    borderDash: [2],
                    zeroLineBorderDash: [2]
                }
            }],
        },
        legend: {
            display: false
        },
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            titleMarginBottom: 10,
            titleFontColor: '#6e707e',
            titleFontSize: 14,
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            intersect: false,
            mode: 'index',
            caretPadding: 10,
        }
    }
});

// Pie Chart
var ctx = document.getElementById("myPieChart");
var myPieChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ["{{ trans('messages.IT Department') }}", "{{ trans('messages.HR Department') }}", "{{ trans('messages.Finance') }}"],
        datasets: [{
            data: [55, 30, 15],
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
            hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        tooltips: {
            backgroundColor: "rgb(255,255,255)",
            bodyFontColor: "#858796",
            borderColor: '#dddfeb',
            borderWidth: 1,
            xPadding: 15,
            yPadding: 15,
            displayColors: false,
            caretPadding: 10,
        },
        legend: {
            display: false
        },
        cutoutPercentage: 80,
    },
});
</script>
@endpush
