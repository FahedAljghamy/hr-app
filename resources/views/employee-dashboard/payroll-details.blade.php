{{-- 
Author: Eng.Fahed
Employee Dashboard Payroll Details - HR System
تفاصيل راتب الموظف
--}}

@extends('layouts.app')

@section('title', trans('messages.Payroll Details') . ': ' . $payroll->pay_period_display)

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.My Payroll Details') }}</h1>
        <p class="text-muted">{{ $payroll->pay_period_display }}</p>
    </div>
    <div>
        <button type="button" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm mr-2" onclick="window.print()">
            <i class="fas fa-print fa-sm text-white-50"></i> {{ trans('messages.Print Payslip') }}
        </button>
        <a href="{{ route('employee-dashboard.payrolls') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Back to Payrolls') }}
        </a>
    </div>
</div>

<!-- Payslip Header -->
<div class="card shadow mb-4" id="payslip">
    <div class="card-header py-3 bg-primary text-white">
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-0 text-white">{{ trans('messages.Payslip') }}</h5>
                <small>{{ $payroll->pay_period_display }}</small>
            </div>
            <div class="col-md-6 text-right">
                <div class="h5 mb-0 text-white">{{ number_format($payroll->net_salary, 2) }} {{ $payroll->currency }}</div>
                <small>{{ trans('messages.Net Salary') }}</small>
            </div>
        </div>
    </div>
    
    <div class="card-body">
        <!-- Employee Information -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h6 class="font-weight-bold text-gray-800 mb-3">{{ trans('messages.Employee Information') }}</h6>
                <p class="mb-1"><strong>{{ trans('messages.Full Name') }}:</strong> {{ $employee->full_name }}</p>
                <p class="mb-1"><strong>{{ trans('messages.Employee ID') }}:</strong> {{ $employee->employee_id }}</p>
                <p class="mb-1"><strong>{{ trans('messages.Job Title') }}:</strong> {{ $employee->job_title }}</p>
                <p class="mb-1"><strong>{{ trans('messages.Department') }}:</strong> {{ trans('messages.' . ucfirst(str_replace('_', ' ', $employee->department))) }}</p>
            </div>
            <div class="col-md-6">
                <h6 class="font-weight-bold text-gray-800 mb-3">{{ trans('messages.Pay Period Information') }}</h6>
                <p class="mb-1"><strong>{{ trans('messages.Pay Period') }}:</strong> {{ $payroll->pay_period_display }}</p>
                <p class="mb-1"><strong>{{ trans('messages.Period Start') }}:</strong> {{ $payroll->pay_period_start->format('Y-m-d') }}</p>
                <p class="mb-1"><strong>{{ trans('messages.Period End') }}:</strong> {{ $payroll->pay_period_end->format('Y-m-d') }}</p>
                <p class="mb-1"><strong>{{ trans('messages.Pay Date') }}:</strong> {{ $payroll->pay_date->format('Y-m-d') }}</p>
            </div>
        </div>

        <!-- Salary Breakdown -->
        <div class="row mb-4">
            <!-- Earnings -->
            <div class="col-md-6">
                <h6 class="font-weight-bold text-success mb-3">{{ trans('messages.Earnings') }}</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <tr>
                            <td>{{ trans('messages.Basic Salary') }}</td>
                            <td class="text-right">{{ number_format($payroll->basic_salary, 2) }} {{ $payroll->currency }}</td>
                        </tr>
                        @if($payroll->housing_allowance > 0)
                        <tr>
                            <td>{{ trans('messages.Housing Allowance') }}</td>
                            <td class="text-right text-success">+{{ number_format($payroll->housing_allowance, 2) }} {{ $payroll->currency }}</td>
                        </tr>
                        @endif
                        @if($payroll->transport_allowance > 0)
                        <tr>
                            <td>{{ trans('messages.Transport Allowance') }}</td>
                            <td class="text-right text-success">+{{ number_format($payroll->transport_allowance, 2) }} {{ $payroll->currency }}</td>
                        </tr>
                        @endif
                        @if($payroll->food_allowance > 0)
                        <tr>
                            <td>{{ trans('messages.Food Allowance') }}</td>
                            <td class="text-right text-success">+{{ number_format($payroll->food_allowance, 2) }} {{ $payroll->currency }}</td>
                        </tr>
                        @endif
                        @if($payroll->overtime_allowance > 0)
                        <tr>
                            <td>{{ trans('messages.Overtime Allowance') }}</td>
                            <td class="text-right text-success">+{{ number_format($payroll->overtime_allowance, 2) }} {{ $payroll->currency }}</td>
                        </tr>
                        @endif
                        @if($payroll->performance_bonus > 0)
                        <tr>
                            <td>{{ trans('messages.Performance Bonus') }}</td>
                            <td class="text-right text-success">+{{ number_format($payroll->performance_bonus, 2) }} {{ $payroll->currency }}</td>
                        </tr>
                        @endif
                        <tr class="border-top">
                            <td class="font-weight-bold">{{ trans('messages.Gross Salary') }}</td>
                            <td class="text-right font-weight-bold">{{ number_format($payroll->gross_salary, 2) }} {{ $payroll->currency }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Deductions -->
            <div class="col-md-6">
                <h6 class="font-weight-bold text-warning mb-3">{{ trans('messages.Deductions') }}</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        @if($payroll->tax_deduction > 0)
                        <tr>
                            <td>{{ trans('messages.Tax Deduction') }}</td>
                            <td class="text-right text-warning">-{{ number_format($payroll->tax_deduction, 2) }} {{ $payroll->currency }}</td>
                        </tr>
                        @endif
                        @if($payroll->insurance_deduction > 0)
                        <tr>
                            <td>{{ trans('messages.Insurance Deduction') }}</td>
                            <td class="text-right text-warning">-{{ number_format($payroll->insurance_deduction, 2) }} {{ $payroll->currency }}</td>
                        </tr>
                        @endif
                        @if($payroll->loan_deduction > 0)
                        <tr>
                            <td>{{ trans('messages.Loan Deduction') }}</td>
                            <td class="text-right text-warning">-{{ number_format($payroll->loan_deduction, 2) }} {{ $payroll->currency }}</td>
                        </tr>
                        @endif
                        @if($payroll->absence_deduction > 0)
                        <tr>
                            <td>{{ trans('messages.Absence Deduction') }}</td>
                            <td class="text-right text-warning">-{{ number_format($payroll->absence_deduction, 2) }} {{ $payroll->currency }}</td>
                        </tr>
                        @endif
                        @if($payroll->other_deductions > 0)
                        <tr>
                            <td>{{ trans('messages.Other Deductions') }}</td>
                            <td class="text-right text-warning">-{{ number_format($payroll->other_deductions, 2) }} {{ $payroll->currency }}</td>
                        </tr>
                        @endif
                        @if($payroll->total_deductions == 0)
                        <tr>
                            <td colspan="2" class="text-center text-muted">{{ trans('messages.No deductions applied') }}</td>
                        </tr>
                        @endif
                        <tr class="border-top">
                            <td class="font-weight-bold">{{ trans('messages.Total Deductions') }}</td>
                            <td class="text-right font-weight-bold text-warning">{{ number_format($payroll->total_deductions, 2) }} {{ $payroll->currency }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Final Amount -->
        <div class="row">
            <div class="col-12">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h4 class="mb-0">{{ trans('messages.Net Salary') }}: {{ number_format($payroll->net_salary, 2) }} {{ $payroll->currency }}</h4>
                        <small>{{ trans('messages.Amount to be paid') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendance Information -->
        <div class="row mt-4">
            <div class="col-12">
                <h6 class="font-weight-bold text-info mb-3">{{ trans('messages.Attendance Summary') }}</h6>
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="h5 text-primary">{{ $payroll->working_days }}</div>
                        <div class="text-xs text-gray-600">{{ trans('messages.Working Days') }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="h5 text-success">{{ $payroll->attended_days }}</div>
                        <div class="text-xs text-gray-600">{{ trans('messages.Attended Days') }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="h5 text-danger">{{ $payroll->absent_days }}</div>
                        <div class="text-xs text-gray-600">{{ trans('messages.Absent Days') }}</div>
                    </div>
                    <div class="col-md-3">
                        <div class="h5 text-info">{{ number_format($payroll->attendance_percentage, 1) }}%</div>
                        <div class="text-xs text-gray-600">{{ trans('messages.Attendance Rate') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="row mt-4">
            <div class="col-12">
                <h6 class="font-weight-bold text-secondary mb-3">{{ trans('messages.Payment Information') }}</h6>
                <div class="row">
                    <div class="col-md-4">
                        <p class="mb-1"><strong>{{ trans('messages.Payment Method') }}:</strong></p>
                        <p class="text-gray-800">{{ trans('messages.' . ucfirst(str_replace('_', ' ', $payroll->payment_method))) }}</p>
                    </div>
                    <div class="col-md-4">
                        <p class="mb-1"><strong>{{ trans('messages.Payment Status') }}:</strong></p>
                        <span class="badge {{ $payroll->payment_status === 'paid' ? 'badge-success' : 'badge-warning' }} p-2">
                            {{ trans('messages.' . ucfirst($payroll->payment_status)) }}
                        </span>
                    </div>
                    <div class="col-md-4">
                        @if($payroll->paid_at)
                        <p class="mb-1"><strong>{{ trans('messages.Paid On') }}:</strong></p>
                        <p class="text-gray-800">{{ $payroll->paid_at->format('Y-m-d H:i') }}</p>
                        @endif
                        
                        @if($payroll->payment_reference)
                        <p class="mb-1"><strong>{{ trans('messages.Payment Reference') }}:</strong></p>
                        <p class="text-gray-800"><code>{{ $payroll->payment_reference }}</code></p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @if($payroll->notes)
        <!-- Notes -->
        <div class="row mt-4">
            <div class="col-12">
                <h6 class="font-weight-bold text-dark mb-3">{{ trans('messages.Notes') }}</h6>
                <div class="alert alert-light">
                    {{ $payroll->notes }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
@media print {
    .btn, .card-header, .sidebar, .topbar, .footer, .d-sm-flex {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .card-body {
        padding: 0 !important;
    }
    body {
        font-size: 12px;
    }
    h1, h2, h3, h4, h5, h6 {
        font-size: 14px !important;
    }
}
</style>
@endpush
