{{-- 
Author: Eng.Fahed
Payrolls Show View - HR System
عرض تفاصيل الراتب
--}}

@extends('layouts.app')

@section('title', trans('messages.Payroll Details') . ': ' . $payroll->employee->full_name . ' - ' . $payroll->pay_period_display)

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Payroll Details') }}</h1>
        <p class="text-muted">{{ $payroll->employee->full_name }} - {{ $payroll->pay_period_display }}</p>
    </div>
    <div>
        @can('payrolls.edit')
        @if(!$payroll->isPaid())
        <a href="{{ route('payrolls.edit', $payroll) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm mr-2">
            <i class="fas fa-edit fa-sm text-white-50"></i> {{ trans('messages.Edit Payroll') }}
        </a>
        @endif
        @endcan
        
        @can('payrolls.edit')
        @if($payroll->payment_status === 'pending')
        <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm mr-2" 
                onclick="markAsPaid({{ $payroll->id }})">
            <i class="fas fa-check fa-sm text-white-50"></i> {{ trans('messages.Mark as Paid') }}
        </button>
        @endif
        @endcan
        
        <a href="{{ route('payrolls.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Back to List') }}
        </a>
    </div>
</div>

<!-- Status Alert -->
@if($payroll->is_overdue)
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle mr-2"></i>
    <strong>{{ trans('messages.Overdue Payment') }}!</strong> 
    {{ trans('messages.This payroll was due on') }} {{ $payroll->pay_date->format('Y-m-d') }}.
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
@endif

<!-- Overview Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            {{ trans('messages.Gross Salary') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($payroll->gross_salary, 2) }} {{ $payroll->currency }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            {{ trans('messages.Total Allowances') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($payroll->total_allowances, 2) }} {{ $payroll->currency }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-plus-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            {{ trans('messages.Total Deductions') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($payroll->total_deductions, 2) }} {{ $payroll->currency }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-minus-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            {{ trans('messages.Net Salary') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($payroll->net_salary, 2) }} {{ $payroll->currency }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
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
        <!-- Employee Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user mr-2"></i>{{ trans('messages.Employee Information') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2 text-center">
                        @if($payroll->employee->profile_photo)
                            <img src="{{ Storage::url($payroll->employee->profile_photo) }}" 
                                 alt="{{ $payroll->employee->full_name }}" 
                                 class="img-fluid rounded-circle mb-2" 
                                 style="width: 80px; height: 80px;">
                        @else
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-2" 
                                 style="width: 80px; height: 80px; font-size: 24px;">
                                {{ substr($payroll->employee->first_name, 0, 1) }}{{ substr($payroll->employee->last_name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="col-md-5">
                        <p class="mb-1"><strong>{{ trans('messages.Full Name') }}:</strong> {{ $payroll->employee->full_name }}</p>
                        <p class="mb-1"><strong>{{ trans('messages.Employee ID') }}:</strong> {{ $payroll->employee->employee_id }}</p>
                        <p class="mb-1"><strong>{{ trans('messages.Email') }}:</strong> {{ $payroll->employee->email }}</p>
                        <p class="mb-1"><strong>{{ trans('messages.Phone') }}:</strong> {{ $payroll->employee->phone }}</p>
                    </div>
                    <div class="col-md-5">
                        <p class="mb-1"><strong>{{ trans('messages.Job Title') }}:</strong> {{ $payroll->employee->job_title }}</p>
                        <p class="mb-1"><strong>{{ trans('messages.Department') }}:</strong> {{ trans('messages.' . ucfirst(str_replace('_', ' ', $payroll->employee->department))) }}</p>
                        <p class="mb-1"><strong>{{ trans('messages.Branch') }}:</strong> {{ $payroll->employee->branch->name ?? trans('messages.No Branch') }}</p>
                        <p class="mb-1"><strong>{{ trans('messages.Hire Date') }}:</strong> {{ $payroll->employee->hire_date->format('Y-m-d') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Salary Breakdown -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-money-bill-wave mr-2"></i>{{ trans('messages.Salary Breakdown') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Basic Salary -->
                    <div class="col-md-6 mb-4">
                        <h6 class="font-weight-bold text-gray-800 mb-3">{{ trans('messages.Basic Components') }}</h6>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ trans('messages.Basic Salary') }}:</span>
                            <span class="font-weight-bold">{{ number_format($payroll->basic_salary, 2) }} {{ $payroll->currency }}</span>
                        </div>
                    </div>

                    <!-- Allowances -->
                    <div class="col-md-6 mb-4">
                        <h6 class="font-weight-bold text-success mb-3">{{ trans('messages.Allowances') }}</h6>
                        @if($payroll->housing_allowance > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ trans('messages.Housing Allowance') }}:</span>
                            <span class="text-success">+{{ number_format($payroll->housing_allowance, 2) }} {{ $payroll->currency }}</span>
                        </div>
                        @endif
                        @if($payroll->transport_allowance > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ trans('messages.Transport Allowance') }}:</span>
                            <span class="text-success">+{{ number_format($payroll->transport_allowance, 2) }} {{ $payroll->currency }}</span>
                        </div>
                        @endif
                        @if($payroll->food_allowance > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ trans('messages.Food Allowance') }}:</span>
                            <span class="text-success">+{{ number_format($payroll->food_allowance, 2) }} {{ $payroll->currency }}</span>
                        </div>
                        @endif
                        @if($payroll->overtime_allowance > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ trans('messages.Overtime Allowance') }}:</span>
                            <span class="text-success">+{{ number_format($payroll->overtime_allowance, 2) }} {{ $payroll->currency }}</span>
                        </div>
                        @endif
                        @if($payroll->performance_bonus > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ trans('messages.Performance Bonus') }}:</span>
                            <span class="text-success">+{{ number_format($payroll->performance_bonus, 2) }} {{ $payroll->currency }}</span>
                        </div>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="font-weight-bold">{{ trans('messages.Total Allowances') }}:</span>
                            <span class="font-weight-bold text-success">{{ number_format($payroll->total_allowances, 2) }} {{ $payroll->currency }}</span>
                        </div>
                    </div>

                    <!-- Deductions -->
                    @if($payroll->total_deductions > 0)
                    <div class="col-md-6 mb-4">
                        <h6 class="font-weight-bold text-warning mb-3">{{ trans('messages.Deductions') }}</h6>
                        @if($payroll->tax_deduction > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ trans('messages.Tax Deduction') }}:</span>
                            <span class="text-warning">-{{ number_format($payroll->tax_deduction, 2) }} {{ $payroll->currency }}</span>
                        </div>
                        @endif
                        @if($payroll->insurance_deduction > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ trans('messages.Insurance Deduction') }}:</span>
                            <span class="text-warning">-{{ number_format($payroll->insurance_deduction, 2) }} {{ $payroll->currency }}</span>
                        </div>
                        @endif
                        @if($payroll->absence_deduction > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ trans('messages.Absence Deduction') }}:</span>
                            <span class="text-warning">-{{ number_format($payroll->absence_deduction, 2) }} {{ $payroll->currency }}</span>
                        </div>
                        @endif
                        @if($payroll->other_deductions > 0)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ trans('messages.Other Deductions') }}:</span>
                            <span class="text-warning">-{{ number_format($payroll->other_deductions, 2) }} {{ $payroll->currency }}</span>
                        </div>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between">
                            <span class="font-weight-bold">{{ trans('messages.Total Deductions') }}:</span>
                            <span class="font-weight-bold text-warning">{{ number_format($payroll->total_deductions, 2) }} {{ $payroll->currency }}</span>
                        </div>
                    </div>
                    @endif

                    <!-- Final Calculation -->
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="h6">{{ trans('messages.Gross Salary') }}:</span>
                                    <span class="h6">{{ number_format($payroll->gross_salary, 2) }} {{ $payroll->currency }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="h6">{{ trans('messages.Total Deductions') }}:</span>
                                    <span class="h6 text-warning">-{{ number_format($payroll->total_deductions, 2) }} {{ $payroll->currency }}</span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <span class="h4 font-weight-bold">{{ trans('messages.Net Salary') }}:</span>
                                    <span class="h4 font-weight-bold text-success">{{ number_format($payroll->net_salary, 2) }} {{ $payroll->currency }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-calendar-check mr-2"></i>{{ trans('messages.Attendance Information') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center">
                        <div class="h4 text-primary">{{ $payroll->working_days }}</div>
                        <div class="text-xs text-gray-600">{{ trans('messages.Working Days') }}</div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="h4 text-success">{{ $payroll->attended_days }}</div>
                        <div class="text-xs text-gray-600">{{ trans('messages.Attended Days') }}</div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="h4 text-danger">{{ $payroll->absent_days }}</div>
                        <div class="text-xs text-gray-600">{{ trans('messages.Absent Days') }}</div>
                    </div>
                    <div class="col-md-3 text-center">
                        <div class="h4 text-info">{{ number_format($payroll->attendance_percentage, 1) }}%</div>
                        <div class="text-xs text-gray-600">{{ trans('messages.Attendance Rate') }}</div>
                    </div>
                </div>
                
                @if($payroll->overtime_hours > 0)
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between">
                            <span>{{ trans('messages.Overtime Hours') }}:</span>
                            <span class="font-weight-bold">{{ $payroll->overtime_hours }} {{ trans('messages.hours') }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between">
                            <span>{{ trans('messages.Overtime Rate') }}:</span>
                            <span class="font-weight-bold">{{ $payroll->overtime_rate }} {{ $payroll->currency }}/{{ trans('messages.hour') }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-4">
        <!-- Payment Status -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-secondary">
                    <i class="fas fa-credit-card mr-2"></i>{{ trans('messages.Payment Status') }}
                </h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <span class="badge badge-{{ $payroll->payment_status === 'paid' ? 'success' : ($payroll->payment_status === 'pending' ? 'warning' : 'danger') }} badge-lg p-3">
                        <i class="fas fa-{{ $payroll->payment_status === 'paid' ? 'check-circle' : ($payroll->payment_status === 'pending' ? 'clock' : 'times-circle') }} mr-2"></i>
                        {{ trans('messages.' . ucfirst($payroll->payment_status)) }}
                    </span>
                </div>
                
                <div class="mb-2">
                    <strong>{{ trans('messages.Pay Date') }}:</strong><br>
                    {{ $payroll->pay_date->format('Y-m-d') }}
                </div>
                
                <div class="mb-2">
                    <strong>{{ trans('messages.Payment Method') }}:</strong><br>
                    {{ trans('messages.' . ucfirst(str_replace('_', ' ', $payroll->payment_method))) }}
                </div>
                
                @if($payroll->payment_reference)
                <div class="mb-2">
                    <strong>{{ trans('messages.Payment Reference') }}:</strong><br>
                    <code>{{ $payroll->payment_reference }}</code>
                </div>
                @endif
                
                @if($payroll->paid_at)
                <div class="mb-2">
                    <strong>{{ trans('messages.Paid On') }}:</strong><br>
                    {{ $payroll->paid_at->format('Y-m-d H:i') }}
                </div>
                @endif
            </div>
        </div>

        <!-- Pay Period -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calendar mr-2"></i>{{ trans('messages.Pay Period') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="h4 text-primary">{{ $payroll->pay_period_display }}</div>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ trans('messages.Period Start') }}:</span>
                    <span>{{ $payroll->pay_period_start->format('Y-m-d') }}</span>
                </div>
                
                <div class="d-flex justify-content-between mb-2">
                    <span>{{ trans('messages.Period End') }}:</span>
                    <span>{{ $payroll->pay_period_end->format('Y-m-d') }}</span>
                </div>
                
                <div class="d-flex justify-content-between">
                    <span>{{ trans('messages.Pay Date') }}:</span>
                    <span class="font-weight-bold">{{ $payroll->pay_date->format('Y-m-d') }}</span>
                </div>
            </div>
        </div>

        @if($payroll->notes)
        <!-- Notes -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-dark">
                    <i class="fas fa-sticky-note mr-2"></i>{{ trans('messages.Notes') }}
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $payroll->notes }}</p>
            </div>
        </div>
        @endif

        <!-- Actions -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-secondary">
                    <i class="fas fa-tools mr-2"></i>{{ trans('messages.Available Actions') }}
                </h6>
            </div>
            <div class="card-body text-center">
                @can('payrolls.edit')
                @if(!$payroll->isPaid())
                <a href="{{ route('payrolls.edit', $payroll) }}" class="btn btn-warning btn-block mb-2">
                    <i class="fas fa-edit mr-2"></i>{{ trans('messages.Edit Payroll') }}
                </a>
                @endif
                @endcan
                
                @can('payrolls.edit')
                @if($payroll->payment_status === 'pending')
                <button type="button" class="btn btn-success btn-block mb-2" onclick="markAsPaid({{ $payroll->id }})">
                    <i class="fas fa-check mr-2"></i>{{ trans('messages.Mark as Paid') }}
                </button>
                @endif
                @endcan
                
                <button type="button" class="btn btn-info btn-block mb-2" onclick="printPayslip()">
                    <i class="fas fa-print mr-2"></i>{{ trans('messages.Print Payslip') }}
                </button>
                
                @can('payrolls.delete')
                @if(!$payroll->isPaid())
                <form action="{{ route('payrolls.destroy', $payroll) }}" method="POST" class="d-inline w-100"
                      onsubmit="return confirm('{{ trans('messages.Are you sure you want to delete this payroll?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-block">
                        <i class="fas fa-trash mr-2"></i>{{ trans('messages.Delete Payroll') }}
                    </button>
                </form>
                @endif
                @endcan
            </div>
        </div>
    </div>
</div>

<!-- Mark as Paid Modal -->
<div class="modal fade" id="markAsPaidModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('messages.Mark as Paid') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="markAsPaidForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="payment_reference">{{ trans('messages.Payment Reference') }} ({{ trans('messages.Optional') }})</label>
                        <input type="text" name="payment_reference" id="payment_reference" 
                               class="form-control" placeholder="{{ trans('messages.Enter payment reference number') }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('messages.Cancel') }}</button>
                    <button type="submit" class="btn btn-success">{{ trans('messages.Mark as Paid') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function markAsPaid(payrollId) {
    const form = document.getElementById('markAsPaidForm');
    form.action = `/payrolls/${payrollId}/mark-as-paid`;
    $('#markAsPaidModal').modal('show');
}

function printPayslip() {
    window.print();
}
</script>
@endpush

@push('styles')
<style>
@media print {
    .btn, .card-header, .sidebar, .topbar, .footer {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
}
</style>
@endpush
