{{-- 
Author: Eng.Fahed
Employees Show View - HR System
عرض تفاصيل الموظف
--}}

@extends('layouts.app')

@section('title', trans('messages.Employee Details') . ': ' . $employee->full_name)

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Employee Details') }}</h1>
        <p class="text-muted">{{ trans('messages.View complete information for') }} {{ $employee->full_name }}</p>
    </div>
    <div>
        @can('employees.edit')
        <a href="{{ route('employees.edit', $employee) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm mr-2">
            <i class="fas fa-edit fa-sm text-white-50"></i> {{ trans('messages.Edit Employee') }}
        </a>
        @endcan
        
        @can('payrolls.create')
        <a href="{{ route('payrolls.create', ['employee_id' => $employee->id]) }}" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm mr-2">
            <i class="fas fa-money-bill fa-sm text-white-50"></i> {{ trans('messages.Create Payroll') }}
        </a>
        @endcan
        
        <a href="{{ route('employees.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Back to List') }}
        </a>
    </div>
</div>

<!-- Document Expiry Alerts -->
@if(count($employee->document_expiry_alerts) > 0)
<div class="row mb-4">
    <div class="col-12">
        @foreach($employee->document_expiry_alerts as $alert)
        <div class="alert {{ $alert['urgency'] === 'high' ? 'alert-danger' : 'alert-warning' }} alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>{{ trans('messages.Document Alert') }}!</strong> 
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

<!-- Employee Overview Cards -->
<div class="row mb-4">
    <!-- Basic Info Card -->
    <div class="col-xl-4 col-lg-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            {{ trans('messages.Employee ID') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $employee->employee_id }}</div>
                        <div class="text-xs text-gray-600">{{ trans('messages.Years of Service') }}: {{ number_format($employee->years_of_service, 1) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-id-card fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employment Status Card -->
    <div class="col-xl-4 col-lg-6 mb-4">
        <div class="card border-left-{{ $employee->employment_status === 'active' ? 'success' : 'warning' }} shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-{{ $employee->employment_status === 'active' ? 'success' : 'warning' }} text-uppercase mb-1">
                            {{ trans('messages.Employment Status') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ trans('messages.' . ucfirst($employee->employment_status)) }}
                        </div>
                        <div class="text-xs text-gray-600">{{ $employee->job_title }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Salary Card -->
    <div class="col-xl-4 col-lg-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            {{ trans('messages.Total Salary') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($employee->total_salary, 2) }} {{ $employee->salary_currency }}
                        </div>
                        <div class="text-xs text-gray-600">{{ trans('messages.Monthly') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
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
        <!-- Personal Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user mr-2"></i>{{ trans('messages.Personal Information') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Full Name') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $employee->full_name }}</p>
                        @if($employee->full_name_ar)
                            <small class="text-muted">{{ $employee->full_name_ar }}</small>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Email') }}:</label>
                        <p class="text-gray-800 mb-0">
                            <a href="mailto:{{ $employee->email }}">{{ $employee->email }}</a>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Phone') }}:</label>
                        <p class="text-gray-800 mb-0">
                            <a href="tel:{{ $employee->phone }}">{{ $employee->phone }}</a>
                        </p>
                        @if($employee->phone_secondary)
                            <br><small class="text-muted">{{ trans('messages.Secondary') }}: {{ $employee->phone_secondary }}</small>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Date of Birth') }}:</label>
                        <p class="text-gray-800 mb-0">
                            {{ $employee->date_of_birth->format('Y-m-d') }}
                            <small class="text-muted">({{ $employee->age }} {{ trans('messages.years old') }})</small>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Gender') }}:</label>
                        <p class="text-gray-800 mb-0">{{ trans('messages.' . ucfirst($employee->gender)) }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Marital Status') }}:</label>
                        <p class="text-gray-800 mb-0">{{ trans('messages.' . ucfirst($employee->marital_status)) }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Nationality') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $employee->nationality }}</p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Address') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $employee->address }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employment Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-briefcase mr-2"></i>{{ trans('messages.Employment Information') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Job Title') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $employee->job_title }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Department') }}:</label>
                        <p class="text-gray-800 mb-0">
                            <span class="badge badge-info">
                                {{ trans('messages.' . ucfirst(str_replace('_', ' ', $employee->department))) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Branch') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $employee->branch->name ?? trans('messages.No Branch') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Manager') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $employee->manager->full_name ?? trans('messages.No Manager') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Employment Type') }}:</label>
                        <p class="text-gray-800 mb-0">
                            <span class="badge badge-primary">
                                {{ trans('messages.' . ucfirst(str_replace('_', ' ', $employee->employment_type))) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Hire Date') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $employee->hire_date->format('Y-m-d') }}</p>
                    </div>
                    @if($employee->contract_start_date)
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Contract Start Date') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $employee->contract_start_date->format('Y-m-d') }}</p>
                    </div>
                    @endif
                    @if($employee->contract_end_date)
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Contract End Date') }}:</label>
                        <p class="text-gray-800 mb-0">
                            {{ $employee->contract_end_date->format('Y-m-d') }}
                            @if($employee->contract_status === 'expiring_soon')
                                <span class="badge badge-warning ml-1">{{ trans('messages.Expiring Soon') }}</span>
                            @elseif($employee->contract_status === 'expired')
                                <span class="badge badge-danger ml-1">{{ trans('messages.Expired') }}</span>
                            @endif
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Identity Documents -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-passport mr-2"></i>{{ trans('messages.Identity & Legal Documents') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Passport Number') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $employee->passport_number }}</p>
                        <small class="text-muted">{{ trans('messages.Expires') }}: {{ $employee->passport_expiry->format('Y-m-d') }}</small>
                    </div>
                    @if($employee->visa_number)
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Visa Number') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $employee->visa_number }}</p>
                        @if($employee->visa_expiry)
                            <small class="text-muted">{{ trans('messages.Expires') }}: {{ $employee->visa_expiry->format('Y-m-d') }}</small>
                        @endif
                    </div>
                    @endif
                    @if($employee->emirates_id)
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Emirates ID') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $employee->emirates_id }}</p>
                        @if($employee->emirates_id_expiry)
                            <small class="text-muted">{{ trans('messages.Expires') }}: {{ $employee->emirates_id_expiry->format('Y-m-d') }}</small>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-4">
        <!-- Profile Photo -->
        @if($employee->profile_photo)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ trans('messages.Profile Photo') }}</h6>
            </div>
            <div class="card-body text-center">
                <img src="{{ Storage::url($employee->profile_photo) }}" 
                     alt="{{ $employee->full_name }}" 
                     class="img-fluid rounded-circle" 
                     style="max-width: 200px;">
            </div>
        </div>
        @endif

        <!-- Salary Breakdown -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-money-bill-wave mr-2"></i>{{ trans('messages.Salary Breakdown') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Basic Salary') }}:</label>
                    <p class="text-gray-800 mb-0">{{ number_format($employee->basic_salary, 2) }} {{ $employee->salary_currency }}</p>
                </div>
                @if($employee->housing_allowance > 0)
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Housing Allowance') }}:</label>
                    <p class="text-gray-800 mb-0">{{ number_format($employee->housing_allowance, 2) }} {{ $employee->salary_currency }}</p>
                </div>
                @endif
                @if($employee->transport_allowance > 0)
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Transport Allowance') }}:</label>
                    <p class="text-gray-800 mb-0">{{ number_format($employee->transport_allowance, 2) }} {{ $employee->salary_currency }}</p>
                </div>
                @endif
                @if($employee->food_allowance > 0)
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Food Allowance') }}:</label>
                    <p class="text-gray-800 mb-0">{{ number_format($employee->food_allowance, 2) }} {{ $employee->salary_currency }}</p>
                </div>
                @endif
                @if($employee->other_allowances > 0)
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Other Allowances') }}:</label>
                    <p class="text-gray-800 mb-0">{{ number_format($employee->other_allowances, 2) }} {{ $employee->salary_currency }}</p>
                </div>
                @endif
                <hr>
                <div class="mb-0">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Total Salary') }}:</label>
                    <p class="h5 text-success font-weight-bold mb-0">
                        {{ number_format($employee->total_salary, 2) }} {{ $employee->salary_currency }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Recent Payrolls -->
        @if($employee->payrolls->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-history mr-2"></i>{{ trans('messages.Recent Payrolls') }}
                </h6>
            </div>
            <div class="card-body">
                @foreach($employee->payrolls as $payroll)
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-grow-1">
                        <div class="font-weight-bold">{{ $payroll->pay_period_display }}</div>
                        <small class="text-muted">{{ number_format($payroll->net_salary, 2) }} {{ $payroll->currency }}</small>
                    </div>
                    <div>
                        <span class="badge {{ $payroll->payment_status === 'paid' ? 'badge-success' : 'badge-warning' }}">
                            {{ trans('messages.' . ucfirst($payroll->payment_status)) }}
                        </span>
                    </div>
                </div>
                @endforeach
                @can('payrolls.view')
                <a href="{{ route('payrolls.index', ['employee_id' => $employee->id]) }}" class="btn btn-sm btn-outline-primary btn-block">
                    {{ trans('messages.View All Payrolls') }}
                </a>
                @endcan
            </div>
        </div>
        @endif

        <!-- Direct Reports -->
        @if($employee->subordinates->count() > 0)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-secondary">
                    <i class="fas fa-users mr-2"></i>{{ trans('messages.Direct Reports') }}
                </h6>
            </div>
            <div class="card-body">
                @foreach($employee->subordinates as $subordinate)
                <div class="d-flex align-items-center mb-2">
                    <div class="flex-grow-1">
                        <a href="{{ route('employees.show', $subordinate) }}" class="font-weight-bold text-decoration-none">
                            {{ $subordinate->full_name }}
                        </a>
                        <br><small class="text-muted">{{ $subordinate->job_title }}</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
