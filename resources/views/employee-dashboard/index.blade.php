{{-- 
Author: Eng.Fahed
Employee Dashboard Index - HR System
لوحة تحكم الموظف - عرض بياناته وإحصائياته فقط
--}}

@extends('layouts.app')

@section('title', trans('messages.My Dashboard'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Welcome') }}, {{ $employee->first_name }}!</h1>
        <p class="text-muted">{{ trans('messages.Your personal dashboard and information') }}</p>
    </div>
    <div>
        <a href="{{ route('employee-dashboard.profile') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-user fa-sm text-white-50"></i> {{ trans('messages.View Profile') }}
        </a>
    </div>
</div>

<!-- Document Alerts -->
@if(count($alerts) > 0)
<div class="row mb-4">
    <div class="col-12">
        @foreach($alerts as $alert)
        <div class="alert {{ $alert['urgency'] === 'high' ? 'alert-danger' : 'alert-warning' }} alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>{{ trans('messages.Important Alert') }}!</strong> 
            {{ $alert['message'] }}
            @if($alert['days_left'] > 0)
                ({{ $alert['days_left'] }} {{ trans('messages.days left') }})
            @endif
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Overview Cards -->
<div class="row mb-4">
    <!-- Years of Service -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            {{ trans('messages.Years of Service') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($stats['years_of_service'], 1) }}
                        </div>
                        <div class="text-xs text-gray-600">{{ trans('messages.years') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Salary -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            {{ trans('messages.Monthly Salary') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($stats['total_salary'], 0) }} AED
                        </div>
                        <div class="text-xs text-gray-600">{{ trans('messages.Gross Amount') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- This Year Earnings -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            {{ trans('messages.This Year Earnings') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($stats['total_earned_this_year'], 0) }} AED
                        </div>
                        <div class="text-xs text-gray-600">{{ $stats['payrolls_this_year'] }} {{ trans('messages.payrolls') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Payrolls -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            {{ trans('messages.Pending Payrolls') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_payrolls'] }}</div>
                        <div class="text-xs text-gray-600">{{ trans('messages.Awaiting Payment') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="row">
    <!-- Left Column -->
    <div class="col-lg-8">
        <!-- Current Month Payroll -->
        @if($currentPayroll)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-file-invoice-dollar mr-2"></i>{{ trans('messages.Current Month Payroll') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ trans('messages.Pay Period') }}:</span>
                            <span class="font-weight-bold">{{ $currentPayroll->pay_period_display }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ trans('messages.Basic Salary') }}:</span>
                            <span>{{ number_format($currentPayroll->basic_salary, 2) }} {{ $currentPayroll->currency }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ trans('messages.Total Allowances') }}:</span>
                            <span class="text-success">+{{ number_format($currentPayroll->total_allowances, 2) }} {{ $currentPayroll->currency }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ trans('messages.Total Deductions') }}:</span>
                            <span class="text-danger">-{{ number_format($currentPayroll->total_deductions, 2) }} {{ $currentPayroll->currency }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-center">
                            <div class="h3 text-success mb-2">{{ number_format($currentPayroll->net_salary, 2) }} {{ $currentPayroll->currency }}</div>
                            <div class="text-xs text-gray-600 mb-3">{{ trans('messages.Net Salary') }}</div>
                            
                            <span class="badge {{ $currentPayroll->payment_status === 'paid' ? 'badge-success' : 'badge-warning' }} p-2">
                                <i class="fas fa-{{ $currentPayroll->payment_status === 'paid' ? 'check-circle' : 'clock' }} mr-1"></i>
                                {{ trans('messages.' . ucfirst($currentPayroll->payment_status)) }}
                            </span>
                            
                            @if($currentPayroll->payment_status === 'paid' && $currentPayroll->paid_at)
                                <div class="text-xs text-gray-600 mt-2">
                                    {{ trans('messages.Paid on') }} {{ $currentPayroll->paid_at->format('Y-m-d') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Monthly Earnings Chart -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-area mr-2"></i>{{ trans('messages.Monthly Earnings Trend') }}
                </h6>
            </div>
            <div class="card-body">
                <canvas id="earningsChart" width="100%" height="40"></canvas>
            </div>
        </div>

        <!-- Recent Payrolls -->
        @if($recentPayrolls->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-history mr-2"></i>{{ trans('messages.Recent Payrolls') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th>{{ trans('messages.Pay Period') }}</th>
                                <th>{{ trans('messages.Net Salary') }}</th>
                                <th>{{ trans('messages.Payment Status') }}</th>
                                <th>{{ trans('messages.Pay Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPayrolls as $payroll)
                            <tr>
                                <td>{{ $payroll->pay_period_display }}</td>
                                <td class="font-weight-bold text-success">
                                    {{ number_format($payroll->net_salary, 2) }} {{ $payroll->currency }}
                                </td>
                                <td>
                                    <span class="badge {{ $payroll->payment_status === 'paid' ? 'badge-success' : 'badge-warning' }}">
                                        {{ trans('messages.' . ucfirst($payroll->payment_status)) }}
                                    </span>
                                </td>
                                <td>{{ $payroll->pay_date->format('Y-m-d') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="text-center mt-3">
                    <a href="{{ route('employee-dashboard.payrolls') }}" class="btn btn-outline-primary">
                        {{ trans('messages.View All My Payrolls') }}
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Right Column -->
    <div class="col-lg-4">
        <!-- Profile Summary -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user mr-2"></i>{{ trans('messages.Profile Summary') }}
                </h6>
            </div>
            <div class="card-body text-center">
                @if($employee->profile_photo)
                    <img src="{{ Storage::url($employee->profile_photo) }}" 
                         alt="{{ $employee->full_name }}" 
                         class="img-fluid rounded-circle mb-3" 
                         style="width: 120px; height: 120px;">
                @else
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 120px; height: 120px; font-size: 36px;">
                        {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                    </div>
                @endif
                
                <h5 class="font-weight-bold">{{ $employee->full_name }}</h5>
                <p class="text-muted mb-1">{{ $employee->job_title }}</p>
                <p class="text-muted mb-3">{{ trans('messages.' . ucfirst(str_replace('_', ' ', $employee->department))) }}</p>
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="text-xs text-gray-600">{{ trans('messages.Employee ID') }}</div>
                        <div class="font-weight-bold">{{ $employee->employee_id }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-xs text-gray-600">{{ trans('messages.Hire Date') }}</div>
                        <div class="font-weight-bold">{{ $employee->hire_date->format('Y-m-d') }}</div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <a href="{{ route('employee-dashboard.profile') }}" class="btn btn-primary btn-sm btn-block">
                        {{ trans('messages.View Full Profile') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-chart-pie mr-2"></i>{{ trans('messages.Quick Stats') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>{{ trans('messages.Contract Status') }}:</span>
                        <span class="badge {{ $employee->contract_status === 'active' ? 'badge-success' : 
                                             ($employee->contract_status === 'expiring_soon' ? 'badge-warning' : 'badge-danger') }}">
                            {{ trans('messages.' . ucfirst(str_replace('_', ' ', $employee->contract_status))) }}
                        </span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>{{ trans('messages.Branch') }}:</span>
                        <span>{{ $employee->branch->name ?? trans('messages.No Branch') }}</span>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>{{ trans('messages.Manager') }}:</span>
                        <span>{{ $employee->manager->full_name ?? trans('messages.No Manager') }}</span>
                    </div>
                </div>
                
                @if($stats['document_alerts'] > 0)
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>{{ trans('messages.Document Alerts') }}:</span>
                        <span class="badge badge-danger">{{ $stats['document_alerts'] }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-secondary">
                    <i class="fas fa-bolt mr-2"></i>{{ trans('messages.Quick Actions') }}
                </h6>
            </div>
            <div class="card-body">
                <a href="{{ route('employee-dashboard.payrolls') }}" class="btn btn-success btn-sm btn-block mb-2">
                    <i class="fas fa-money-bill mr-2"></i>{{ trans('messages.View My Payrolls') }}
                </a>
                
                <a href="{{ route('employee-dashboard.documents') }}" class="btn btn-info btn-sm btn-block mb-2">
                    <i class="fas fa-file-alt mr-2"></i>{{ trans('messages.View My Documents') }}
                </a>
                
                <a href="{{ route('employee-dashboard.profile') }}" class="btn btn-primary btn-sm btn-block">
                    <i class="fas fa-user mr-2"></i>{{ trans('messages.Edit Profile') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// رسم بياني للأرباح الشهرية
const ctx = document.getElementById('earningsChart').getContext('2d');
const monthlyData = @json($monthlyEarnings);

const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: monthlyData.map(item => item.month),
        datasets: [{
            label: '{{ trans('messages.Monthly Earnings') }} (AED)',
            data: monthlyData.map(item => item.amount),
            borderColor: 'rgb(78, 115, 223)',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString() + ' AED';
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.parsed.y.toLocaleString() + ' AED';
                    }
                }
            }
        }
    }
});
</script>
@endpush
