{{-- 
Author: Eng.Fahed
Payrolls Index View - HR System
قائمة الرواتب مع البحث والفلترة
--}}

@extends('layouts.app')

@section('title', trans('messages.Payroll Management'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Payroll Management') }}</h1>
        <p class="text-muted">{{ trans('messages.Manage employee payrolls and salary payments') }}</p>
    </div>
    @can('payrolls.create')
    <a href="{{ route('payrolls.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> {{ trans('messages.Create Payroll') }}
    </a>
    @endcan
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            {{ trans('messages.This Month') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_this_month'] }}</div>
                        <div class="text-xs text-gray-600">{{ trans('messages.Total Payrolls') }}</div>
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
                            {{ trans('messages.Paid This Month') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['paid_this_month'] }}</div>
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
                            {{ trans('messages.Pending This Month') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_this_month'] }}</div>
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
                            {{ trans('messages.Total Amount') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($stats['total_amount_this_month'], 0) }} AED
                        </div>
                        <div class="text-xs text-gray-600">{{ trans('messages.This Month') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter mr-2"></i>{{ trans('messages.Search and Filter') }}
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('payrolls.index') }}">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="employee_id" class="form-label">{{ trans('messages.Employee') }}</label>
                    <select name="employee_id" id="employee_id" class="form-control">
                        <option value="">{{ trans('messages.All Employees') }}</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->full_name }} ({{ $employee->employee_id }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2 mb-3">
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
                
                <div class="col-md-2 mb-3">
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
                
                <div class="col-md-3 mb-3">
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
                
                <div class="col-md-2 mb-3">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search mr-1"></i>{{ trans('messages.Search') }}
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <a href="{{ route('payrolls.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo mr-1"></i>{{ trans('messages.Clear Filters') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Payrolls Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table mr-2"></i>{{ trans('messages.Payrolls List') }}
        </h6>
    </div>
    <div class="card-body">
        @if($payrolls->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        <th>{{ trans('messages.Employee') }}</th>
                        <th>{{ trans('messages.Pay Period') }}</th>
                        <th>{{ trans('messages.Basic Salary') }}</th>
                        <th>{{ trans('messages.Total Allowances') }}</th>
                        <th>{{ trans('messages.Total Deductions') }}</th>
                        <th>{{ trans('messages.Net Salary') }}</th>
                        <th>{{ trans('messages.Payment Status') }}</th>
                        <th>{{ trans('messages.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payrolls as $payroll)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($payroll->employee->profile_photo)
                                    <img src="{{ Storage::url($payroll->employee->profile_photo) }}" 
                                         alt="{{ $payroll->employee->full_name }}" 
                                         class="rounded-circle mr-2" 
                                         width="32" height="32">
                                @else
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-2" 
                                         style="width: 32px; height: 32px; font-size: 14px;">
                                        {{ substr($payroll->employee->first_name, 0, 1) }}{{ substr($payroll->employee->last_name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="font-weight-bold">{{ $payroll->employee->full_name }}</div>
                                    <small class="text-muted">{{ $payroll->employee->employee_id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="font-weight-bold">{{ $payroll->pay_period_display }}</div>
                            <small class="text-muted">{{ $payroll->pay_date->format('Y-m-d') }}</small>
                        </td>
                        <td>{{ number_format($payroll->basic_salary, 2) }} {{ $payroll->currency }}</td>
                        <td>{{ number_format($payroll->total_allowances, 2) }} {{ $payroll->currency }}</td>
                        <td>{{ number_format($payroll->total_deductions, 2) }} {{ $payroll->currency }}</td>
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
                            @if($payroll->is_overdue)
                                <br><small class="text-danger">{{ trans('messages.Overdue') }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                @can('payrolls.view')
                                <a href="{{ route('payrolls.show', $payroll) }}" 
                                   class="btn btn-sm btn-info" title="{{ trans('messages.View Details') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                
                                @can('payrolls.edit')
                                @if(!$payroll->isPaid())
                                <a href="{{ route('payrolls.edit', $payroll) }}" 
                                   class="btn btn-sm btn-warning" title="{{ trans('messages.Edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                                @endcan
                                
                                @can('payrolls.edit')
                                @if($payroll->payment_status === 'pending')
                                <button type="button" class="btn btn-sm btn-success" 
                                        onclick="markAsPaid({{ $payroll->id }})" 
                                        title="{{ trans('messages.Mark as Paid') }}">
                                    <i class="fas fa-check"></i>
                                </button>
                                @endif
                                @endcan
                                
                                @can('payrolls.delete')
                                @if(!$payroll->isPaid())
                                <form action="{{ route('payrolls.destroy', $payroll) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('{{ trans('messages.Are you sure you want to delete this payroll?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="{{ trans('messages.Delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
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
            <p class="text-muted">{{ trans('messages.Try adjusting your search criteria or create a new payroll') }}</p>
            @can('payrolls.create')
            <a href="{{ route('payrolls.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>{{ trans('messages.Create First Payroll') }}
            </a>
            @endcan
        </div>
        @endif
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
</script>
@endpush
