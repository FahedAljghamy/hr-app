{{-- 
Author: Eng.Fahed
Leaves Edit View - HR System
صفحة تعديل طلب الإجازة
--}}

@extends('layouts.app')

@section('title', trans('messages.Edit Leave Request'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Edit Leave Request') }}</h1>
        <p class="text-muted">{{ trans('messages.Modify your leave request details') }}</p>
    </div>
    <div>
        <a href="{{ route('leaves.show', $leave) }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm mr-2">
            <i class="fas fa-eye fa-sm text-white-50"></i> {{ trans('messages.View Details') }}
        </a>
        <a href="{{ route('leaves.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Back to List') }}
        </a>
    </div>
</div>

<!-- Status Alert -->
@if($leave->status !== 'pending')
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle mr-2"></i>
    <strong>{{ trans('messages.Notice') }}:</strong> 
    {{ trans('messages.This leave request has been') }} {{ trans('messages.' . ucfirst($leave->status)) }}.
    {{ trans('messages.Changes may not be allowed') }}.
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
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
<form action="{{ route('leaves.update', $leave) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Leave Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-calendar-edit mr-2"></i>{{ trans('messages.Leave Details') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="leave_type" class="form-label">{{ trans('messages.Leave Type') }} <span class="text-danger">*</span></label>
                            <select name="leave_type" id="leave_type" class="form-control @error('leave_type') is-invalid @enderror" required>
                                <option value="">{{ trans('messages.Select Leave Type') }}</option>
                                @foreach($leaveTypes as $key => $type)
                                    <option value="{{ $key }}" {{ old('leave_type', $leave->leave_type) === $key ? 'selected' : '' }}>
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
                                    <option value="{{ $key }}" {{ old('day_type', $leave->day_type) === $key ? 'selected' : '' }}>
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
                            <input type="date" name="start_date" id="start_date" 
                                   value="{{ old('start_date', $leave->start_date->format('Y-m-d')) }}" 
                                   class="form-control @error('start_date') is-invalid @enderror" 
                                   min="{{ date('Y-m-d') }}" required>
                            @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label">{{ trans('messages.End Date') }} <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="end_date" 
                                   value="{{ old('end_date', $leave->end_date->format('Y-m-d')) }}" 
                                   class="form-control @error('end_date') is-invalid @enderror" 
                                   min="{{ date('Y-m-d') }}" required>
                            @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Time fields for partial days -->
                        <div class="col-md-6 mb-3" id="time_fields" style="{{ $leave->day_type === 'full_day' ? 'display: none;' : '' }}">
                            <label for="start_time" class="form-label">{{ trans('messages.Start Time') }}</label>
                            <input type="time" name="start_time" id="start_time" 
                                   value="{{ old('start_time', $leave->start_time?->format('H:i') ?? '09:00') }}" 
                                   class="form-control @error('start_time') is-invalid @enderror">
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3" id="end_time_field" style="{{ in_array($leave->day_type, ['full_day', 'quarter_day']) ? 'display: none;' : '' }}">
                            <label for="end_time" class="form-label">{{ trans('messages.End Time') }}</label>
                            <input type="time" name="end_time" id="end_time" 
                                   value="{{ old('end_time', $leave->end_time?->format('H:i') ?? '13:00') }}" 
                                   class="form-control @error('end_time') is-invalid @enderror">
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="reason" class="form-label">{{ trans('messages.Reason') }} <span class="text-danger">*</span></label>
                            <textarea name="reason" id="reason" rows="3" 
                                      class="form-control @error('reason') is-invalid @enderror" 
                                      placeholder="{{ trans('messages.Please provide a detailed reason for your leave request') }}" required>{{ old('reason', $leave->reason) }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">{{ trans('messages.Additional Details') }}</label>
                            <textarea name="description" id="description" rows="3" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="{{ trans('messages.Any additional information or special requirements') }}">{{ old('description', $leave->description) }}</textarea>
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
                                    <option value="{{ $emp->id }}" {{ old('covering_employee_id', $leave->covering_employee_id) == $emp->id ? 'selected' : '' }}>
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
                            <input type="text" name="emergency_contact" id="emergency_contact" 
                                   value="{{ old('emergency_contact', $leave->emergency_contact) }}" 
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
                                      placeholder="{{ trans('messages.Instructions for covering employee or pending tasks') }}">{{ old('handover_notes', $leave->handover_notes) }}</textarea>
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
            <!-- Current Request Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle mr-2"></i>{{ trans('messages.Current Request') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="font-weight-bold text-gray-700">{{ trans('messages.Status') }}:</label>
                        <span class="badge {{ $leave->status_badge_class }} ml-2">
                            {{ trans('messages.' . ucfirst($leave->status)) }}
                        </span>
                    </div>
                    
                    <div class="mb-3">
                        <label class="font-weight-bold text-gray-700">{{ trans('messages.Current Duration') }}:</label>
                        <p class="mb-0">{{ $leave->duration_display }}</p>
                    </div>
                    
                    <div class="mb-3">
                        <label class="font-weight-bold text-gray-700">{{ trans('messages.Submitted') }}:</label>
                        <p class="mb-0">{{ $leave->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    
                    @if($leave->approved_at)
                    <div class="mb-3">
                        <label class="font-weight-bold text-gray-700">{{ trans('messages.Approved') }}:</label>
                        <p class="mb-0">{{ $leave->approved_at->format('Y-m-d H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Leave Calculator -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-calculator mr-2"></i>{{ trans('messages.Leave Calculator') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="h4 text-primary" id="calculated-days">{{ $leave->total_days }}</div>
                        <div class="text-xs text-gray-600">{{ trans('messages.Total Days') }}</div>
                    </div>
                    
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span>{{ trans('messages.Start Date') }}:</span>
                            <span id="display-start-date" class="font-weight-bold">{{ $leave->start_date->format('Y-m-d') }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span>{{ trans('messages.End Date') }}:</span>
                            <span id="display-end-date" class="font-weight-bold">{{ $leave->end_date->format('Y-m-d') }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <div class="d-flex justify-content-between">
                            <span>{{ trans('messages.Type') }}:</span>
                            <span id="display-day-type" class="font-weight-bold">{{ trans('messages.' . ucfirst(str_replace('_', ' ', $leave->day_type))) }}</span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-0">
                        <div class="d-flex justify-content-between">
                            <span>{{ trans('messages.Working Days') }}:</span>
                            <span id="working-days" class="font-weight-bold text-success">{{ $leave->total_days }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Medical Leave -->
            <div class="card shadow mb-4" id="medical-card" style="{{ $leave->leave_type === 'sick' ? '' : 'display: none;' }}">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-medkit mr-2"></i>{{ trans('messages.Medical Certificate') }}
                    </h6>
                </div>
                <div class="card-body">
                    @if($leave->medical_certificate)
                    <div class="mb-3">
                        <label class="font-weight-bold text-gray-700">{{ trans('messages.Current Certificate') }}:</label>
                        <p class="mb-2">
                            <a href="{{ Storage::url($leave->medical_certificate) }}" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-download mr-1"></i>{{ trans('messages.View Certificate') }}
                            </a>
                        </p>
                    </div>
                    @endif
                    
                    <div class="form-group">
                        <label for="medical_certificate" class="form-label">
                            {{ $leave->medical_certificate ? trans('messages.Replace Medical Certificate') : trans('messages.Upload Medical Certificate') }}
                        </label>
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
            <button type="submit" class="btn btn-success btn-lg mr-3">
                <i class="fas fa-save mr-2"></i>{{ trans('messages.Update Leave Request') }}
            </button>
            <a href="{{ route('leaves.show', $leave) }}" class="btn btn-secondary btn-lg mr-3">
                <i class="fas fa-times mr-2"></i>{{ trans('messages.Cancel') }}
            </a>
            
            @if($leave->can_be_cancelled)
            <button type="button" class="btn btn-warning btn-lg" onclick="cancelLeave({{ $leave->id }})">
                <i class="fas fa-ban mr-2"></i>{{ trans('messages.Cancel Request') }}
            </button>
            @endif
        </div>
    </div>
</form>

<!-- Cancel Modal -->
@if($leave->can_be_cancelled)
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">{{ trans('messages.Cancel Leave Request') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="cancelForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="cancel_reason">{{ trans('messages.Cancellation Reason') }} <span class="text-danger">*</span></label>
                        <textarea name="reason" id="cancel_reason" rows="3" 
                                  class="form-control" required
                                  placeholder="{{ trans('messages.Please provide a reason for cancellation') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('messages.Cancel') }}</button>
                    <button type="submit" class="btn btn-warning">{{ trans('messages.Cancel Leave') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
let currentLeaveId = {{ $leave->id }};

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
        } else {
            $('#medical-card').hide();
        }
    });

    // ربط الأحداث
    $('#start_date, #end_date').change(calculateLeaveDays);
    $('#day_type').trigger('change');
    
    // تحديد تاريخ النهاية تلقائياً
    $('#start_date').change(function() {
        $('#end_date').attr('min', $(this).val());
    });
});

// إلغاء الإجازة
function cancelLeave(leaveId) {
    $('#cancelModal').modal('show');
}

// معالجة الإلغاء
$('#cancelForm').on('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = $(this).find('button[type="submit"]');
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>{{ trans('messages.Processing') }}...');
    
    $.ajax({
        url: `/leaves/${currentLeaveId}/cancel`,
        method: 'PATCH',
        data: {
            _token: '{{ csrf_token() }}',
            reason: $('#cancel_reason').val()
        },
        success: function(response) {
            $('#cancelModal').modal('hide');
            window.location.href = `/leaves/${currentLeaveId}`;
        },
        error: function(xhr) {
            alert('Error: ' + xhr.responseJSON.message);
        },
        complete: function() {
            submitBtn.prop('disabled', false).html('{{ trans('messages.Cancel Leave') }}');
        }
    });
});
</script>
@endpush
