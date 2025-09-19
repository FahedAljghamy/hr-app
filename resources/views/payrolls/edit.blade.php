{{-- 
Author: Eng.Fahed
Payrolls Edit View - HR System
صفحة تعديل الراتب
--}}

@extends('layouts.app')

@section('title', trans('messages.Edit Payroll') . ': ' . $payroll->employee->full_name . ' - ' . $payroll->pay_period_display)

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Edit Payroll') }}</h1>
        <p class="text-muted">{{ $payroll->employee->full_name }} - {{ $payroll->pay_period_display }}</p>
    </div>
    <div>
        <a href="{{ route('payrolls.show', $payroll) }}" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm mr-2">
            <i class="fas fa-eye fa-sm text-white-50"></i> {{ trans('messages.View Details') }}
        </a>
        <a href="{{ route('payrolls.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Back to List') }}
        </a>
    </div>
</div>

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

<!-- Edit Payroll Form -->
<form action="{{ route('payrolls.update', $payroll) }}" method="POST">
    @csrf
    @method('PUT')
    
    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Employee Information (Read Only) -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user mr-2"></i>{{ trans('messages.Employee Information') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>{{ trans('messages.Full Name') }}:</strong> {{ $payroll->employee->full_name }}</p>
                            <p class="mb-1"><strong>{{ trans('messages.Employee ID') }}:</strong> {{ $payroll->employee->employee_id }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>{{ trans('messages.Pay Period') }}:</strong> {{ $payroll->pay_period_display }}</p>
                            <p class="mb-1"><strong>{{ trans('messages.Pay Date') }}:</strong> {{ $payroll->pay_date->format('Y-m-d') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Salary Details -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-money-bill-wave mr-2"></i>{{ trans('messages.Salary Details') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="basic_salary" class="form-label">{{ trans('messages.Basic Salary') }} (AED) <span class="text-danger">*</span></label>
                            <input type="number" name="basic_salary" id="basic_salary" value="{{ old('basic_salary', $payroll->basic_salary) }}" 
                                   class="form-control @error('basic_salary') is-invalid @enderror" 
                                   step="0.01" min="0" required>
                            @error('basic_salary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="housing_allowance" class="form-label">{{ trans('messages.Housing Allowance') }} (AED)</label>
                            <input type="number" name="housing_allowance" id="housing_allowance" value="{{ old('housing_allowance', $payroll->housing_allowance) }}" 
                                   class="form-control @error('housing_allowance') is-invalid @enderror" 
                                   step="0.01" min="0">
                            @error('housing_allowance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="transport_allowance" class="form-label">{{ trans('messages.Transport Allowance') }} (AED)</label>
                            <input type="number" name="transport_allowance" id="transport_allowance" value="{{ old('transport_allowance', $payroll->transport_allowance) }}" 
                                   class="form-control @error('transport_allowance') is-invalid @enderror" 
                                   step="0.01" min="0">
                            @error('transport_allowance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="food_allowance" class="form-label">{{ trans('messages.Food Allowance') }} (AED)</label>
                            <input type="number" name="food_allowance" id="food_allowance" value="{{ old('food_allowance', $payroll->food_allowance) }}" 
                                   class="form-control @error('food_allowance') is-invalid @enderror" 
                                   step="0.01" min="0">
                            @error('food_allowance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="overtime_allowance" class="form-label">{{ trans('messages.Overtime Allowance') }} (AED)</label>
                            <input type="number" name="overtime_allowance" id="overtime_allowance" value="{{ old('overtime_allowance', $payroll->overtime_allowance) }}" 
                                   class="form-control @error('overtime_allowance') is-invalid @enderror" 
                                   step="0.01" min="0">
                            @error('overtime_allowance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="performance_bonus" class="form-label">{{ trans('messages.Performance Bonus') }} (AED)</label>
                            <input type="number" name="performance_bonus" id="performance_bonus" value="{{ old('performance_bonus', $payroll->performance_bonus) }}" 
                                   class="form-control @error('performance_bonus') is-invalid @enderror" 
                                   step="0.01" min="0">
                            @error('performance_bonus')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Deductions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-minus-circle mr-2"></i>{{ trans('messages.Deductions') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tax_deduction" class="form-label">{{ trans('messages.Tax Deduction') }} (AED)</label>
                            <input type="number" name="tax_deduction" id="tax_deduction" value="{{ old('tax_deduction', $payroll->tax_deduction) }}" 
                                   class="form-control @error('tax_deduction') is-invalid @enderror" 
                                   step="0.01" min="0">
                            @error('tax_deduction')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="insurance_deduction" class="form-label">{{ trans('messages.Insurance Deduction') }} (AED)</label>
                            <input type="number" name="insurance_deduction" id="insurance_deduction" value="{{ old('insurance_deduction', $payroll->insurance_deduction) }}" 
                                   class="form-control @error('insurance_deduction') is-invalid @enderror" 
                                   step="0.01" min="0">
                            @error('insurance_deduction')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="absence_deduction" class="form-label">{{ trans('messages.Absence Deduction') }} (AED)</label>
                            <input type="number" name="absence_deduction" id="absence_deduction" value="{{ old('absence_deduction', $payroll->absence_deduction) }}" 
                                   class="form-control @error('absence_deduction') is-invalid @enderror" 
                                   step="0.01" min="0">
                            @error('absence_deduction')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="other_deductions" class="form-label">{{ trans('messages.Other Deductions') }} (AED)</label>
                            <input type="number" name="other_deductions" id="other_deductions" value="{{ old('other_deductions', $payroll->other_deductions) }}" 
                                   class="form-control @error('other_deductions') is-invalid @enderror" 
                                   step="0.01" min="0">
                            @error('other_deductions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Attendance Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-calendar-check mr-2"></i>{{ trans('messages.Attendance Information') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="working_days" class="form-label">{{ trans('messages.Working Days') }} <span class="text-danger">*</span></label>
                        <input type="number" name="working_days" id="working_days" value="{{ old('working_days', $payroll->working_days) }}" 
                               class="form-control @error('working_days') is-invalid @enderror" 
                               min="1" max="31" required>
                        @error('working_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="attended_days" class="form-label">{{ trans('messages.Attended Days') }} <span class="text-danger">*</span></label>
                        <input type="number" name="attended_days" id="attended_days" value="{{ old('attended_days', $payroll->attended_days) }}" 
                               class="form-control @error('attended_days') is-invalid @enderror" 
                               min="0" max="31" required>
                        @error('attended_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="overtime_hours" class="form-label">{{ trans('messages.Overtime Hours') }}</label>
                        <input type="number" name="overtime_hours" id="overtime_hours" value="{{ old('overtime_hours', $payroll->overtime_hours) }}" 
                               class="form-control @error('overtime_hours') is-invalid @enderror" 
                               step="0.5" min="0">
                        @error('overtime_hours')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="payment_method" class="form-label">{{ trans('messages.Payment Method') }} <span class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-control @error('payment_method') is-invalid @enderror" required>
                            @foreach($paymentMethods as $key => $method)
                                <option value="{{ $key }}" {{ old('payment_method', $payroll->payment_method) === $key ? 'selected' : '' }}>
                                    {{ trans('messages.' . $method) }}
                                </option>
                            @endforeach
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="notes" class="form-label">{{ trans('messages.Notes') }}</label>
                        <textarea name="notes" id="notes" rows="4" 
                                  class="form-control @error('notes') is-invalid @enderror" 
                                  placeholder="{{ trans('messages.Enter any additional notes') }}">{{ old('notes', $payroll->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Salary Summary -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-calculator mr-2"></i>{{ trans('messages.Salary Summary') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ trans('messages.Gross Salary') }}:</span>
                        <span id="gross-salary" class="font-weight-bold">{{ number_format($payroll->gross_salary, 2) }} {{ $payroll->currency }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ trans('messages.Total Deductions') }}:</span>
                        <span id="total-deductions" class="text-danger">{{ number_format($payroll->total_deductions, 2) }} {{ $payroll->currency }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="font-weight-bold">{{ trans('messages.Net Salary') }}:</span>
                        <span id="net-salary" class="font-weight-bold text-success h5">{{ number_format($payroll->net_salary, 2) }} {{ $payroll->currency }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="card shadow">
        <div class="card-body text-center">
            <button type="submit" class="btn btn-primary btn-lg mr-3">
                <i class="fas fa-save mr-2"></i>{{ trans('messages.Update Payroll') }}
            </button>
            <a href="{{ route('payrolls.show', $payroll) }}" class="btn btn-secondary btn-lg">
                <i class="fas fa-times mr-2"></i>{{ trans('messages.Cancel') }}
            </a>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // حساب الراتب الصافي تلقائياً
    function calculateSalary() {
        let basicSalary = parseFloat($('#basic_salary').val()) || 0;
        let housingAllowance = parseFloat($('#housing_allowance').val()) || 0;
        let transportAllowance = parseFloat($('#transport_allowance').val()) || 0;
        let foodAllowance = parseFloat($('#food_allowance').val()) || 0;
        let overtimeAllowance = parseFloat($('#overtime_allowance').val()) || 0;
        let performanceBonus = parseFloat($('#performance_bonus').val()) || 0;
        
        let taxDeduction = parseFloat($('#tax_deduction').val()) || 0;
        let insuranceDeduction = parseFloat($('#insurance_deduction').val()) || 0;
        let absenceDeduction = parseFloat($('#absence_deduction').val()) || 0;
        let otherDeductions = parseFloat($('#other_deductions').val()) || 0;
        
        let grossSalary = basicSalary + housingAllowance + transportAllowance + foodAllowance + overtimeAllowance + performanceBonus;
        let totalDeductions = taxDeduction + insuranceDeduction + absenceDeduction + otherDeductions;
        let netSalary = grossSalary - totalDeductions;
        
        $('#gross-salary').text(grossSalary.toFixed(2) + ' AED');
        $('#total-deductions').text(totalDeductions.toFixed(2) + ' AED');
        $('#net-salary').text(netSalary.toFixed(2) + ' AED');
    }

    // ربط الأحداث
    $('input[type="number"]').on('input', calculateSalary);
    
    // حساب أولي
    calculateSalary();
});
</script>
@endpush
