{{-- 
Author: Eng.Fahed
Employee Dashboard Payrolls - HR System
رواتب الموظف
--}}

@extends('layouts.app')

@section('title', trans('messages.My Payrolls'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.My Payrolls') }}</h1>
        <p class="text-muted">{{ trans('messages.View your salary history and payment details') }}</p>
    </div>
    <a href="{{ route('employee-dashboard.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Back to Dashboard') }}
    </a>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            {{ trans('messages.Total Payrolls') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_payrolls'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
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
                            {{ trans('messages.Paid Payrolls') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['paid_payrolls'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            {{ trans('messages.Pending Payrolls') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_payrolls'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                            {{ trans('messages.Total Earned') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($stats['total_earned'], 0) }} AED
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter mr-2"></i>{{ trans('messages.Filter Payrolls') }}
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('employee-dashboard.payrolls') }}">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="year" class="form-label">{{ trans('messages.Year') }}</label>
                    <select name="year" id="year" class="form-control">
                        <option value="">{{ trans('messages.All Years') }}</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="month" class="form-label">{{ trans('messages.Month') }}</label>
                    <select name="month" id="month" class="form-control">
                        <option value="">{{ trans('messages.All Months') }}</option>
                        @foreach($months as $key => $month)
                            <option value="{{ $key }}" {{ request('month') == $key ? 'selected' : '' }}>
                                {{ trans('messages.' . $month) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="status" class="form-label">{{ trans('messages.Payment Status') }}</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">{{ trans('messages.All Statuses') }}</option>
                        @foreach($statuses as $key => $status)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                {{ trans('messages.' . $status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-search mr-1"></i>{{ trans('messages.Filter') }}
                    </button>
                    <a href="{{ route('employee-dashboard.payrolls') }}" class="btn btn-secondary">
                        <i class="fas fa-undo mr-1"></i>{{ trans('messages.Clear Filters') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Payrolls List -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table mr-2"></i>{{ trans('messages.My Payrolls History') }}
        </h6>
    </div>
    <div class="card-body">
        @if($payrolls->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        <th>{{ trans('messages.Pay Period') }}</th>
                        <th>{{ trans('messages.Basic Salary') }}</th>
                        <th>{{ trans('messages.Allowances') }}</th>
                        <th>{{ trans('messages.Deductions') }}</th>
                        <th>{{ trans('messages.Net Salary') }}</th>
                        <th>{{ trans('messages.Payment Status') }}</th>
                        <th>{{ trans('messages.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payrolls as $payroll)
                    <tr>
                        <td>
                            <div class="font-weight-bold">{{ $payroll->pay_period_display }}</div>
                            <small class="text-muted">{{ $payroll->pay_date->format('Y-m-d') }}</small>
                        </td>
                        <td>{{ number_format($payroll->basic_salary, 2) }} {{ $payroll->currency }}</td>
                        <td class="text-success">+{{ number_format($payroll->total_allowances, 2) }} {{ $payroll->currency }}</td>
                        <td class="text-danger">-{{ number_format($payroll->total_deductions, 2) }} {{ $payroll->currency }}</td>
                        <td>
                            <span class="font-weight-bold text-success">
                                {{ number_format($payroll->net_salary, 2) }} {{ $payroll->currency }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $payroll->payment_status === 'paid' ? 'badge-success' : 
                                                   ($payroll->payment_status === 'pending' ? 'badge-warning' : 'badge-danger') }}">
                                {{ trans('messages.' . ucfirst($payroll->payment_status)) }}
                            </span>
                            @if($payroll->paid_at)
                                <br><small class="text-muted">{{ $payroll->paid_at->format('Y-m-d') }}</small>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('employee-dashboard.payroll-details', $payroll) }}" 
                               class="btn btn-sm btn-info" title="{{ trans('messages.View Details') }}">
                                <i class="fas fa-eye"></i>
                            </a>
                            
                            @if($payroll->payment_status === 'paid')
                            <button type="button" class="btn btn-sm btn-secondary" 
                                    onclick="printPayslip({{ $payroll->id }})" 
                                    title="{{ trans('messages.Print Payslip') }}">
                                <i class="fas fa-print"></i>
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                {{ trans('messages.Showing') }} {{ $payrolls->firstItem() ?? 0 }} {{ trans('messages.to') }} 
                {{ $payrolls->lastItem() ?? 0 }} {{ trans('messages.of') }} {{ $payrolls->total() }} {{ trans('messages.results') }}
            </div>
            <div>
                {{ $payrolls->links('pagination.custom') }}
            </div>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-file-invoice-dollar fa-3x text-gray-300 mb-3"></i>
            <h5 class="text-gray-600">{{ trans('messages.No payrolls found') }}</h5>
            <p class="text-muted">{{ trans('messages.Your payroll history will appear here once processed') }}</p>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function printPayslip(payrollId) {
    // فتح صفحة تفاصيل الراتب في نافذة جديدة للطباعة
    const url = `/employee-dashboard/payrolls/${payrollId}`;
    const printWindow = window.open(url, '_blank');
    
    printWindow.onload = function() {
        setTimeout(function() {
            printWindow.print();
        }, 500);
    };
}
</script>
@endpush
