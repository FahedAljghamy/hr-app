{{-- 
Author: Eng.Fahed
Leaves Create View - HR System
صفحة تقديم طلب إجازة جديد
--}}

@extends('layouts.app')

@section('title', trans('messages.Request Leave'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Request Leave') }}</h1>
        <p class="text-muted">{{ trans('messages.Submit a new leave request for approval') }}</p>
    </div>
    <a href="{{ route('leaves.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Back to List') }}
    </a>
</div>

<!-- Employee Leave Balance -->
@if(isset($remainingAnnual) && isset($remainingSick))
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            {{ trans('messages.Annual Leave Balance') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $remainingAnnual }} {{ trans('messages.days') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            {{ trans('messages.Sick Leave Balance') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $remainingSick }} {{ trans('messages.days') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-medkit fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Error Messages -->
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle"></i>
    <strong>{{ trans('messages.Please correct the following errors:') }}</strong>
    <ul class="mb-0 mt-2">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
@endif

<!-- Leave Request Form -->
<form action="{{ route('leaves.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Leave Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-plus mr-2"></i>{{ trans('messages.Leave Details') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="leave_type" class="form-label">{{ trans('messages.Leave Type') }} <span class="text-danger">*</span></label>
                            <select name="leave_type" id="leave_type" class="form-control @error('leave_type') is-invalid @enderror" required>
                                <option value="">{{ trans('messages.Select Leave Type') }}</option>
                                @foreach($leaveTypes as $key => $type)
                                    <option value="{{ $key }}" {{ old('leave_type') === $key ? 'selected' : '' }}>
                                        {{ trans('messages.' . $type) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('leave_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="day_type" class="form-label">{{ trans('messages.Day Type') }} <span class="text-danger">*</span></label>
                            <select name="day_type" id="day_type" class="form-control @error('day_type') is-invalid @enderror" required>
                                @foreach($dayTypes as $key => $type)
                                    <option value="{{ $key }}" {{ old('day_type', 'full_day') === $key ? 'selected' : '' }}>
                                        {{ trans('messages.' . $type) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('day_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label">{{ trans('messages.Start Date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" 
                                   class="form-control @error('start_date') is-invalid @enderror" 
                                   min="{{ date('Y-m-d') }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">{{ trans('messages.End Date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" 
                                   class="form-control @error('end_date') is-invalid @enderror" 
                                   min="{{ date('Y-m-d') }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Time fields for partial days -->
                        <div class="col-md-6 mb-3" id="time_fields" style="display: none;">
                            <label for="start_time" class="form-label">{{ trans('messages.Start Time') }}</label>
                            <input type="time" name="start_time" id="start_time" value="{{ old('start_time', '09:00') }}" 
                                   class="form-control @error('start_time') is-invalid @enderror">
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3" id="end_time_field" style="display: none;">
                            <label for="end_time" class="form-label">{{ trans('messages.End Time') }}</label>
                            <input type="time" name="end_time" id="end_time" value="{{ old('end_time', '13:00') }}" 
                                   class="form-control @error('end_time') is-invalid @enderror">
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="reason" class="form-label">{{ trans('messages.Reason') }} <span class="text-danger">*</span></label>
                            <textarea name="reason" id="reason" rows="3" 
                                      class="form-control @error('reason') is-invalid @enderror" 
                                      placeholder="{{ trans('messages.Please provide a detailed reason for your leave request') }}" required>{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">{{ trans('messages.Additional Details') }}</label>
                            <textarea name="description" id="description" rows="3" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="{{ trans('messages.Any additional information or special requirements') }}">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Work Coverage -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-users mr-2"></i>{{ trans('messages.Work Coverage') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="covering_employee_id" class="form-label">{{ trans('messages.Covering Employee') }}</label>
                            <select name="covering_employee_id" id="covering_employee_id" class="form-control @error('covering_employee_id') is-invalid @enderror">
                                <option value="">{{ trans('messages.No Coverage Required') }}</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}" {{ old('covering_employee_id') == $emp->id ? 'selected' : '' }}>
                                        {{ $emp->full_name }} ({{ $emp->job_title }})
                                    </option>
                                @endforeach
                            </select>
                            @error('covering_employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="emergency_contact" class="form-label">{{ trans('messages.Emergency Contact') }}</label>
                            <input type="text" name="emergency_contact" id="emergency_contact" value="{{ old('emergency_contact') }}" 
                                   class="form-control @error('emergency_contact') is-invalid @enderror" 
                                   placeholder="{{ trans('messages.Contact number during leave') }}">
                            @error('emergency_contact')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="handover_notes" class="form-label">{{ trans('messages.Handover Notes') }}</label>
                            <textarea name="handover_notes" id="handover_notes" rows="4" 
                                      class="form-control @error('handover_notes') is-invalid @enderror" 
                                      placeholder="{{ trans('messages.Instructions for covering employee or pending tasks') }}">{{ old('handover_notes') }}</textarea>
                            @error('handover_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Employee Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user mr-2"></i>{{ trans('messages.Employee Information') }}
                    </h6>
                </div>
                <div class="card-body text-center">
                    @if($employee->profile_photo)
                        <img src="{{ Storage::url($employee->profile_photo) }}" 
                             alt="{{ $employee->full_name }}" 
                             class="img-fluid rounded-circle mb-3" 
                             style="width: 100px; height: 100px;">
                    @else
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" 
                             style="width: 100px; height: 100px; font-size: 32px;">
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
                            <div class="text-xs text-gray-600">{{ trans('messages.Manager') }}</div>
                            <div class="font-weight-bold">{{ $employee->manager->full_name ?? trans('messages.No Manager') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave Calculator -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-calculator mr-2"></i>{{ trans('messages.Leave Calculator') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="h4 text-primary" id="calculated-days">0</div>
                        <div class="text-xs text-gray-600">{{ trans('messages.Total Days') }}</div>
                    </div>
                    
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span>{{ trans('messages.Start Date') }}:</span>
                            <span id="display-start-date" class="font-weight-bold">{{ trans('messages.Not Selected') }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span>{{ trans('messages.End Date') }}:</span>
                            <span id="display-end-date" class="font-weight-bold">{{ trans('messages.Not Selected') }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span>{{ trans('messages.Type') }}:</span>
                            <span id="display-day-type" class="font-weight-bold">{{ trans('messages.Full Day') }}</span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-0">
                        <div class="d-flex justify-content-between">
                            <span>{{ trans('messages.Working Days') }}:</span>
                            <span id="working-days" class="font-weight-bold text-success">0</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Leave -->
            <div class="card shadow mb-4" id="medical-card" style="display: none;">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-medkit mr-2"></i>{{ trans('messages.Medical Certificate') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="medical_certificate" class="form-label">{{ trans('messages.Upload Medical Certificate') }}</label>
                        <input type="file" name="medical_certificate" id="medical_certificate" 
                               class="form-control-file @error('medical_certificate') is-invalid @enderror" 
                               accept=".pdf,.jpg,.jpeg,.png">
                        <small class="form-text text-muted">{{ trans('messages.Required for sick leave. Max size: 5MB') }}</small>
                        @error('medical_certificate')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="card shadow">
        <div class="card-body text-center">
            <button type="submit" class="btn btn-primary btn-lg mr-3">
                <i class="fas fa-paper-plane mr-2"></i>{{ trans('messages.Submit Leave Request') }}
            </button>
            <a href="{{ route('leaves.index') }}" class="btn btn-secondary btn-lg">
                <i class="fas fa-times mr-2"></i>{{ trans('messages.Cancel') }}
            </a>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // حساب أيام الإجازة تلقائياً
    function calculateLeaveDays() {
        const startDate = $('#start_date').val();
        const endDate = $('#end_date').val();
        const dayType = $('#day_type').val();
        
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            
            if (end >= start) {
                let workingDays = 0;
                let currentDate = new Date(start);
                
                while (currentDate <= end) {
                    const dayOfWeek = currentDate.getDay();
                    // استثناء الجمعة (5) والسبت (6)
                    if (dayOfWeek !== 5 && dayOfWeek !== 6) {
                        if (dayType === 'full_day') {
                            workingDays += 1;
                        } else if (dayType === 'half_day') {
                            workingDays += 0.5;
                        } else { // quarter_day
                            workingDays += 0.25;
                        }
                    }
                    currentDate.setDate(currentDate.getDate() + 1);
                }
                
                $('#calculated-days').text(Math.ceil(workingDays));
                $('#working-days').text(workingDays);
                $('#display-start-date').text(startDate);
                $('#display-end-date').text(endDate);
            }
        }
    }

    // عرض/إخفاء حقول الوقت
    $('#day_type').change(function() {
        const dayType = $(this).val();
        $('#display-day-type').text($(this).find(':selected').text());
        
        if (dayType === 'full_day') {
            $('#time_fields, #end_time_field').hide();
        } else {
            $('#time_fields').show();
            if (dayType === 'half_day') {
                $('#end_time_field').show();
            } else {
                $('#end_time_field').hide();
            }
        }
        calculateLeaveDays();
    });

    // عرض/إخفاء بطاقة الشهادة الطبية
    $('#leave_type').change(function() {
        if ($(this).val() === 'sick') {
            $('#medical-card').show();
            $('#medical_certificate').attr('required', true);
        } else {
            $('#medical-card').hide();
            $('#medical_certificate').attr('required', false);
        }
    });

    // ربط الأحداث
    $('#start_date, #end_date').change(calculateLeaveDays);
    $('#day_type').trigger('change');
    
    // تحديد تاريخ النهاية تلقائياً
    $('#start_date').change(function() {
        if ($(this).val() && !$('#end_date').val()) {
            $('#end_date').val($(this).val());
        }
        $('#end_date').attr('min', $(this).val());
    });
});
</script>
@endpush
