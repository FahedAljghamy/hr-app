{{-- 
Author: Eng.Fahed
Payrolls Create View - HR System
صفحة إنشاء راتب جديد
--}}

@extends('layouts.app')

@section('title', trans('messages.Create Payroll'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Create Payroll') }}</h1>
        <p class="text-muted">{{ trans('messages.Create a new payroll for an employee') }}</p>
    </div>
    <a href="{{ route('payrolls.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Back to List') }}
    </a>
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

<!-- Create Payroll Form -->
<form action="{{ route('payrolls.store') }}" method="POST">
    @csrf
    
    <div class="row">
        <!-- Employee Selection -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user mr-2"></i>{{ trans('messages.Employee Selection') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="employee_id" class="form-label">{{ trans('messages.Employee') }} <span class="text-danger">*</span></label>
                            <select name="employee_id" id="employee_id" class="form-control @error('employee_id') is-invalid @enderror" required>
                                <option value="">{{ trans('messages.Select Employee') }}</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" 
                                            data-basic-salary="{{ $employee->basic_salary }}"
                                            data-housing-allowance="{{ $employee->housing_allowance }}"
                                            data-transport-allowance="{{ $employee->transport_allowance }}"
                                            data-food-allowance="{{ $employee->food_allowance }}"
                                            data-other-allowances="{{ $employee->other_allowances }}"
                                            {{ old('employee_id', $selectedEmployee?->id) == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->full_name }} ({{ $employee->employee_id }}) - {{ $employee->job_title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="pay_year" class="form-label">{{ trans('messages.Pay Year') }} <span class="text-danger">*</span></label>
                            <select name="pay_year" id="pay_year" class="form-control @error('pay_year') is-invalid @enderror" required>
                                @for($year = date('Y') - 1; $year <= date('Y') + 1; $year++)
                                    <option value="{{ $year }}" {{ old('pay_year', $currentYear) == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endfor
                            </select>
                            @error('pay_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label for="pay_month" class="form-label">{{ trans('messages.Pay Month') }} <span class="text-danger">*</span></label>
                            <select name="pay_month" id="pay_month" class="form-control @error('pay_month') is-invalid @enderror" required>
                                @for($month = 1; $month <= 12; $month++)
                                    <option value="{{ $month }}" {{ old('pay_month', $currentMonth) == $month ? 'selected' : '' }}>
                                        {{ trans('messages.' . date('F', mktime(0, 0, 0, $month, 1))) }}
                                    </option>
                                @endfor
                            </select>
                            @error('pay_month')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Employee Info Display -->
                    <div id="employee-info" class="d-none">
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-gray-800">{{ trans('messages.Employee Information') }}</h6>
                                <p class="mb-1"><strong>{{ trans('messages.Full Name') }}:</strong> <span id="emp-name"></span></p>
                                <p class="mb-1"><strong>{{ trans('messages.Employee ID') }}:</strong> <span id="emp-id"></span></p>
                                <p class="mb-1"><strong>{{ trans('messages.Job Title') }}:</strong> <span id="emp-job"></span></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="font-weight-bold text-gray-800">{{ trans('messages.Current Salary Structure') }}</h6>
                                <p class="mb-1"><strong>{{ trans('messages.Basic Salary') }}:</strong> <span id="emp-basic"></span> AED</p>
                                <p class="mb-1"><strong>{{ trans('messages.Housing Allowance') }}:</strong> <span id="emp-housing"></span> AED</p>
                                <p class="mb-1"><strong>{{ trans('messages.Transport Allowance') }}:</strong> <span id="emp-transport"></span> AED</p>
                            </div>
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
                            <input type="number" name="basic_salary" id="basic_salary" value="{{ old('basic_salary') }}" 
                                   class="form-control @error('basic_salary') is-invalid @enderror" 
                                   step="0.01" min="0" required>
                            @error('basic_salary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="housing_allowance" class="form-label">{{ trans('messages.Housing Allowance') }} (AED)</label>
                            <input type="number" name="housing_allowance" id="housing_allowance" value="{{ old('housing_allowance', 0) }}" 
                                   class="form-control @error('housing_allowance') is-invalid @enderror" 
                                   step="0.01" min="0">
                            @error('housing_allowance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="transport_allowance" class="form-label">{{ trans('messages.Transport Allowance') }} (AED)</label>
                            <input type="number" name="transport_allowance" id="transport_allowance" value="{{ old('transport_allowance', 0) }}" 
                                   class="form-control @error('transport_allowance') is-invalid @enderror" 
                                   step="0.01" min="0">
                            @error('transport_allowance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="food_allowance" class="form-label">{{ trans('messages.Food Allowance') }} (AED)</label>
                            <input type="number" name="food_allowance" id="food_allowance" value="{{ old('food_allowance', 0) }}" 
                                   class="form-control @error('food_allowance') is-invalid @enderror" 
                                   step="0.01" min="0">
                            @error('food_allowance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="overtime_allowance" class="form-label">{{ trans('messages.Overtime Allowance') }} (AED)</label>
                            <input type="number" name="overtime_allowance" id="overtime_allowance" value="{{ old('overtime_allowance', 0) }}" 
                                   class="form-control @error('overtime_allowance') is-invalid @enderror" 
                                   step="0.01" min="0">
                            @error('overtime_allowance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="performance_bonus" class="form-label">{{ trans('messages.Performance Bonus') }} (AED)</label>
                            <input type="number" name="performance_bonus" id="performance_bonus" value="{{ old('performance_bonus', 0) }}" 
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
                            <input type="number" name="tax_deduction" id="tax_deduction" value="{{ old('tax_deduction', 0) }}" 
                                   class="form-control @error('tax_deduction') is-invalid @enderror" 
                                   step="0.01" min="0">
                            @error('tax_deduction')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="insurance_deduction" class="form-label">{{ trans('messages.Insurance Deduction') }} (AED)</label>
                            <input type="number" name="insurance_deduction" id="insurance_deduction" value="{{ old('insurance_deduction', 0) }}" 
                                   class="form-control @error('insurance_deduction') is-invalid @enderror" 
                                   step="0.01" min="0">
                            @error('insurance_deduction')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="absence_deduction" class="form-label">{{ trans('messages.Absence Deduction') }} (AED)</label>
                            <input type="number" name="absence_deduction" id="absence_deduction" value="{{ old('absence_deduction', 0) }}" 
                                   class="form-control @error('absence_deduction') is-invalid @enderror" 
                                   step="0.01" min="0">
                            @error('absence_deduction')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="other_deductions" class="form-label">{{ trans('messages.Other Deductions') }} (AED)</label>
                            <input type="number" name="other_deductions" id="other_deductions" value="{{ old('other_deductions', 0) }}" 
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
                        <input type="number" name="working_days" id="working_days" value="{{ old('working_days', 22) }}" 
                               class="form-control @error('working_days') is-invalid @enderror" 
                               min="1" max="31" required>
                        @error('working_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="attended_days" class="form-label">{{ trans('messages.Attended Days') }} <span class="text-danger">*</span></label>
                        <input type="number" name="attended_days" id="attended_days" value="{{ old('attended_days', 22) }}" 
                               class="form-control @error('attended_days') is-invalid @enderror" 
                               min="0" max="31" required>
                        @error('attended_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="absent_days" class="form-label">{{ trans('messages.Absent Days') }}</label>
                        <input type="number" name="absent_days" id="absent_days" value="{{ old('absent_days', 0) }}" 
                               class="form-control @error('absent_days') is-invalid @enderror" 
                               min="0" max="31" readonly>
                        @error('absent_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="overtime_hours" class="form-label">{{ trans('messages.Overtime Hours') }}</label>
                        <input type="number" name="overtime_hours" id="overtime_hours" value="{{ old('overtime_hours', 0) }}" 
                               class="form-control @error('overtime_hours') is-invalid @enderror" 
                               step="0.5" min="0">
                        @error('overtime_hours')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">
                        <i class="fas fa-credit-card mr-2"></i>{{ trans('messages.Payment Information') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="payment_method" class="form-label">{{ trans('messages.Payment Method') }} <span class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-control @error('payment_method') is-invalid @enderror" required>
                            @foreach($paymentMethods as $key => $method)
                                <option value="{{ $key }}" {{ old('payment_method', 'bank_transfer') === $key ? 'selected' : '' }}>
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
                                  placeholder="{{ trans('messages.Enter any additional notes') }}">{{ old('notes') }}</textarea>
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
                        <span id="gross-salary" class="font-weight-bold">0.00 AED</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ trans('messages.Total Deductions') }}:</span>
                        <span id="total-deductions" class="text-danger">0.00 AED</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span class="font-weight-bold">{{ trans('messages.Net Salary') }}:</span>
                        <span id="net-salary" class="font-weight-bold text-success h5">0.00 AED</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="card shadow">
        <div class="card-body text-center">
            <button type="submit" class="btn btn-primary btn-lg mr-3">
                <i class="fas fa-save mr-2"></i>{{ trans('messages.Create Payroll') }}
            </button>
            <a href="{{ route('payrolls.index') }}" class="btn btn-secondary btn-lg">
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

    // حساب أيام الغياب تلقائياً
    function calculateAbsentDays() {
        let workingDays = parseInt($('#working_days').val()) || 0;
        let attendedDays = parseInt($('#attended_days').val()) || 0;
        let absentDays = Math.max(0, workingDays - attendedDays);
        $('#absent_days').val(absentDays);
    }

    // عند اختيار موظف، ملء البيانات
    $('#employee_id').change(function() {
        let selected = $(this).find(':selected');
        if (selected.val()) {
            // عرض معلومات الموظف
            $('#emp-name').text(selected.text().split(' (')[0]);
            $('#emp-id').text(selected.text().match(/\(([^)]+)\)/)?.[1] || '');
            $('#emp-job').text(selected.text().split(' - ')[1] || '');
            
            // ملء بيانات الراتب
            $('#basic_salary').val(selected.data('basic-salary'));
            $('#housing_allowance').val(selected.data('housing-allowance'));
            $('#transport_allowance').val(selected.data('transport-allowance'));
            $('#food_allowance').val(selected.data('food-allowance'));
            
            $('#emp-basic').text(selected.data('basic-salary'));
            $('#emp-housing').text(selected.data('housing-allowance'));
            $('#emp-transport').text(selected.data('transport-allowance'));
            
            $('#employee-info').removeClass('d-none');
            calculateSalary();
        } else {
            $('#employee-info').addClass('d-none');
        }
    });

    // ربط الأحداث
    $('input[type="number"]').on('input', function() {
        if ($(this).attr('id') === 'working_days' || $(this).attr('id') === 'attended_days') {
            calculateAbsentDays();
        }
        calculateSalary();
    });

    // تحديد الموظف المختار مسبقاً
    if ($('#employee_id').val()) {
        $('#employee_id').trigger('change');
    }
    
    // حساب أولي
    calculateAbsentDays();
    calculateSalary();
});
</script>
@endpush
