{{-- 
Author: Eng.Fahed
Employee Dashboard Profile - HR System
الملف الشخصي للموظف
--}}

@extends('layouts.app')

@section('title', trans('messages.My Profile'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.My Profile') }}</h1>
        <p class="text-muted">{{ trans('messages.View your personal and employment information') }}</p>
    </div>
    <a href="{{ route('employee-dashboard.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Back to Dashboard') }}
    </a>
</div>

<!-- Profile Overview -->
<div class="row mb-4">
    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-body text-center">
                @if($employee->profile_photo)
                    <img src="{{ Storage::url($employee->profile_photo) }}" 
                         alt="{{ $employee->full_name }}" 
                         class="img-fluid rounded-circle mb-3" 
                         style="width: 150px; height: 150px;">
                @else
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 150px; height: 150px; font-size: 48px;">
                        {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                    </div>
                @endif
                
                <h4 class="font-weight-bold">{{ $employee->full_name }}</h4>
                @if($employee->full_name_ar)
                    <p class="text-muted mb-2">{{ $employee->full_name_ar }}</p>
                @endif
                <p class="text-primary mb-1">{{ $employee->job_title }}</p>
                <p class="text-muted mb-3">{{ trans('messages.' . ucfirst(str_replace('_', ' ', $employee->department))) }}</p>
                
                <div class="row text-center">
                    <div class="col-6">
                        <div class="text-xs text-gray-600">{{ trans('messages.Employee ID') }}</div>
                        <div class="font-weight-bold">{{ $employee->employee_id }}</div>
                    </div>
                    <div class="col-6">
                        <div class="text-xs text-gray-600">{{ trans('messages.Years of Service') }}</div>
                        <div class="font-weight-bold">{{ number_format($employee->years_of_service, 1) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
    </div>
</div>

<!-- Employment and Documents -->
<div class="row">
    <!-- Employment Information -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-briefcase mr-2"></i>{{ trans('messages.Employment Information') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Employment Type') }}:</label>
                    <p class="text-gray-800 mb-0">
                        <span class="badge badge-primary">
                            {{ trans('messages.' . ucfirst(str_replace('_', ' ', $employee->employment_type))) }}
                        </span>
                    </p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Employment Status') }}:</label>
                    <p class="text-gray-800 mb-0">
                        <span class="badge {{ $employee->employment_status === 'active' ? 'badge-success' : 'badge-warning' }}">
                            {{ trans('messages.' . ucfirst($employee->employment_status)) }}
                        </span>
                    </p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Branch') }}:</label>
                    <p class="text-gray-800 mb-0">{{ $employee->branch->name ?? trans('messages.No Branch') }}</p>
                </div>
                
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Manager') }}:</label>
                    <p class="text-gray-800 mb-0">{{ $employee->manager->full_name ?? trans('messages.No Manager') }}</p>
                </div>
                
                @if($employee->contract_end_date)
                <div class="mb-3">
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
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-passport mr-2"></i>{{ trans('messages.Identity Documents') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Passport Number') }}:</label>
                    <p class="text-gray-800 mb-0">{{ $employee->passport_number }}</p>
                    <small class="text-muted">{{ trans('messages.Expires') }}: {{ $employee->passport_expiry->format('Y-m-d') }}</small>
                    @if($employee->passport_expiry->diffInDays(now(), false) <= 90)
                        <span class="badge badge-warning ml-1">{{ trans('messages.Expiring Soon') }}</span>
                    @endif
                </div>
                
                @if($employee->visa_number)
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Visa Number') }}:</label>
                    <p class="text-gray-800 mb-0">{{ $employee->visa_number }}</p>
                    @if($employee->visa_expiry)
                        <small class="text-muted">{{ trans('messages.Expires') }}: {{ $employee->visa_expiry->format('Y-m-d') }}</small>
                        @if($employee->visa_expiry->diffInDays(now(), false) <= 30)
                            <span class="badge badge-danger ml-1">{{ trans('messages.Expiring Soon') }}</span>
                        @endif
                    @endif
                </div>
                @endif
                
                @if($employee->emirates_id)
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Emirates ID') }}:</label>
                    <p class="text-gray-800 mb-0">{{ $employee->emirates_id }}</p>
                    @if($employee->emirates_id_expiry)
                        <small class="text-muted">{{ trans('messages.Expires') }}: {{ $employee->emirates_id_expiry->format('Y-m-d') }}</small>
                        @if($employee->emirates_id_expiry->diffInDays(now(), false) <= 30)
                            <span class="badge badge-warning ml-1">{{ trans('messages.Expiring Soon') }}</span>
                        @endif
                    @endif
                </div>
                @endif
                
                <div class="mt-3">
                    <a href="{{ route('employee-dashboard.documents') }}" class="btn btn-outline-primary btn-sm btn-block">
                        {{ trans('messages.View All Documents') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
