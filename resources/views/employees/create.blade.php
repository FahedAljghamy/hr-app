{{-- 
Author: Eng.Fahed
Employees Create View - HR System
صفحة إضافة موظف جديد
--}}

@extends('layouts.app')

@section('title', trans('messages.Add Employee'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Add Employee') }}</h1>
        <p class="text-muted">{{ trans('messages.Create a new employee record with complete information') }}</p>
    </div>
    <a href="{{ route('employees.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
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

<!-- Create Employee Form -->
<form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <!-- Personal Information -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-user mr-2"></i>{{ trans('messages.Personal Information') }}
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="first_name" class="form-label">{{ trans('messages.First Name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" 
                           class="form-control @error('first_name') is-invalid @enderror" required>
                    @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="middle_name" class="form-label">{{ trans('messages.Middle Name') }}</label>
                    <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name') }}" 
                           class="form-control @error('middle_name') is-invalid @enderror">
                    @error('middle_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="last_name" class="form-label">{{ trans('messages.Last Name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" 
                           class="form-control @error('last_name') is-invalid @enderror" required>
                    @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="full_name_ar" class="form-label">{{ trans('messages.Full Name Arabic') }}</label>
                    <input type="text" name="full_name_ar" id="full_name_ar" value="{{ old('full_name_ar') }}" 
                           class="form-control @error('full_name_ar') is-invalid @enderror">
                    @error('full_name_ar')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">{{ trans('messages.Email') }} <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" 
                           class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="phone" class="form-label">{{ trans('messages.Phone') }} <span class="text-danger">*</span></label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" 
                           class="form-control @error('phone') is-invalid @enderror" required>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="phone_secondary" class="form-label">{{ trans('messages.Secondary Phone') }}</label>
                    <input type="text" name="phone_secondary" id="phone_secondary" value="{{ old('phone_secondary') }}" 
                           class="form-control @error('phone_secondary') is-invalid @enderror">
                    @error('phone_secondary')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="date_of_birth" class="form-label">{{ trans('messages.Date of Birth') }} <span class="text-danger">*</span></label>
                    <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth') }}" 
                           class="form-control @error('date_of_birth') is-invalid @enderror" required>
                    @error('date_of_birth')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="gender" class="form-label">{{ trans('messages.Gender') }} <span class="text-danger">*</span></label>
                    <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror" required>
                        <option value="">{{ trans('messages.Select Gender') }}</option>
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>{{ trans('messages.Male') }}</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>{{ trans('messages.Female') }}</option>
                    </select>
                    @error('gender')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="marital_status" class="form-label">{{ trans('messages.Marital Status') }}</label>
                    <select name="marital_status" id="marital_status" class="form-control @error('marital_status') is-invalid @enderror">
                        <option value="">{{ trans('messages.Select Status') }}</option>
                        <option value="single" {{ old('marital_status') === 'single' ? 'selected' : '' }}>{{ trans('messages.Single') }}</option>
                        <option value="married" {{ old('marital_status') === 'married' ? 'selected' : '' }}>{{ trans('messages.Married') }}</option>
                        <option value="divorced" {{ old('marital_status') === 'divorced' ? 'selected' : '' }}>{{ trans('messages.Divorced') }}</option>
                        <option value="widowed" {{ old('marital_status') === 'widowed' ? 'selected' : '' }}>{{ trans('messages.Widowed') }}</option>
                    </select>
                    @error('marital_status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="nationality" class="form-label">{{ trans('messages.Nationality') }} <span class="text-danger">*</span></label>
                    <input type="text" name="nationality" id="nationality" value="{{ old('nationality') }}" 
                           class="form-control @error('nationality') is-invalid @enderror" required>
                    @error('nationality')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="profile_photo" class="form-label">{{ trans('messages.Profile Photo') }}</label>
                    <input type="file" name="profile_photo" id="profile_photo" 
                           class="form-control-file @error('profile_photo') is-invalid @enderror" 
                           accept="image/jpeg,image/png,image/jpg">
                    <small class="form-text text-muted">{{ trans('messages.Max size: 2MB. Formats: JPG, PNG') }}</small>
                    @error('profile_photo')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-12 mb-3">
                    <label for="address" class="form-label">{{ trans('messages.Address') }} <span class="text-danger">*</span></label>
                    <textarea name="address" id="address" rows="3" 
                              class="form-control @error('address') is-invalid @enderror" required>{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Identity and Legal Documents -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-warning">
                <i class="fas fa-passport mr-2"></i>{{ trans('messages.Identity & Legal Documents') }}
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="passport_number" class="form-label">{{ trans('messages.Passport Number') }} <span class="text-danger">*</span></label>
                    <input type="text" name="passport_number" id="passport_number" value="{{ old('passport_number') }}" 
                           class="form-control @error('passport_number') is-invalid @enderror" required>
                    @error('passport_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="passport_expiry" class="form-label">{{ trans('messages.Passport Expiry') }} <span class="text-danger">*</span></label>
                    <input type="date" name="passport_expiry" id="passport_expiry" value="{{ old('passport_expiry') }}" 
                           class="form-control @error('passport_expiry') is-invalid @enderror" required>
                    @error('passport_expiry')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="passport_country" class="form-label">{{ trans('messages.Passport Country') }}</label>
                    <input type="text" name="passport_country" id="passport_country" value="{{ old('passport_country') }}" 
                           class="form-control @error('passport_country') is-invalid @enderror">
                    @error('passport_country')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="visa_number" class="form-label">{{ trans('messages.Visa Number') }}</label>
                    <input type="text" name="visa_number" id="visa_number" value="{{ old('visa_number') }}" 
                           class="form-control @error('visa_number') is-invalid @enderror">
                    @error('visa_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="visa_expiry" class="form-label">{{ trans('messages.Visa Expiry') }}</label>
                    <input type="date" name="visa_expiry" id="visa_expiry" value="{{ old('visa_expiry') }}" 
                           class="form-control @error('visa_expiry') is-invalid @enderror">
                    @error('visa_expiry')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="emirates_id" class="form-label">{{ trans('messages.Emirates ID') }}</label>
                    <input type="text" name="emirates_id" id="emirates_id" value="{{ old('emirates_id') }}" 
                           class="form-control @error('emirates_id') is-invalid @enderror">
                    @error('emirates_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="passport_copy" class="form-label">{{ trans('messages.Passport Copy') }}</label>
                    <input type="file" name="passport_copy" id="passport_copy" 
                           class="form-control-file @error('passport_copy') is-invalid @enderror" 
                           accept=".pdf,.jpg,.jpeg,.png">
                    <small class="form-text text-muted">{{ trans('messages.Max size: 5MB. Formats: PDF, JPG, PNG') }}</small>
                    @error('passport_copy')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="visa_copy" class="form-label">{{ trans('messages.Visa Copy') }}</label>
                    <input type="file" name="visa_copy" id="visa_copy" 
                           class="form-control-file @error('visa_copy') is-invalid @enderror" 
                           accept=".pdf,.jpg,.jpeg,.png">
                    <small class="form-text text-muted">{{ trans('messages.Max size: 5MB. Formats: PDF, JPG, PNG') }}</small>
                    @error('visa_copy')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
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
                    <label for="job_title" class="form-label">{{ trans('messages.Job Title') }} <span class="text-danger">*</span></label>
                    <input type="text" name="job_title" id="job_title" value="{{ old('job_title') }}" 
                           class="form-control @error('job_title') is-invalid @enderror" required>
                    @error('job_title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="department" class="form-label">{{ trans('messages.Department') }} <span class="text-danger">*</span></label>
                    <select name="department" id="department" class="form-control @error('department') is-invalid @enderror" required>
                        <option value="">{{ trans('messages.Select Department') }}</option>
                        @foreach($departments as $key => $dept)
                            <option value="{{ $key }}" {{ old('department') === $key ? 'selected' : '' }}>
                                {{ trans('messages.' . $dept) }}
                            </option>
                        @endforeach
                    </select>
                    @error('department')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="employment_type" class="form-label">{{ trans('messages.Employment Type') }} <span class="text-danger">*</span></label>
                    <select name="employment_type" id="employment_type" class="form-control @error('employment_type') is-invalid @enderror" required>
                        <option value="">{{ trans('messages.Select Type') }}</option>
                        @foreach($employmentTypes as $key => $type)
                            <option value="{{ $key }}" {{ old('employment_type') === $key ? 'selected' : '' }}>
                                {{ trans('messages.' . $type) }}
                            </option>
                        @endforeach
                    </select>
                    @error('employment_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="employment_status" class="form-label">{{ trans('messages.Employment Status') }}</label>
                    <select name="employment_status" id="employment_status" class="form-control @error('employment_status') is-invalid @enderror">
                        @foreach($employmentStatuses as $key => $status)
                            <option value="{{ $key }}" {{ old('employment_status', 'active') === $key ? 'selected' : '' }}>
                                {{ trans('messages.' . $status) }}
                            </option>
                        @endforeach
                    </select>
                    @error('employment_status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="hire_date" class="form-label">{{ trans('messages.Hire Date') }} <span class="text-danger">*</span></label>
                    <input type="date" name="hire_date" id="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}" 
                           class="form-control @error('hire_date') is-invalid @enderror" required>
                    @error('hire_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="contract_start_date" class="form-label">{{ trans('messages.Contract Start Date') }}</label>
                    <input type="date" name="contract_start_date" id="contract_start_date" value="{{ old('contract_start_date') }}" 
                           class="form-control @error('contract_start_date') is-invalid @enderror">
                    @error('contract_start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="contract_end_date" class="form-label">{{ trans('messages.Contract End Date') }}</label>
                    <input type="date" name="contract_end_date" id="contract_end_date" value="{{ old('contract_end_date') }}" 
                           class="form-control @error('contract_end_date') is-invalid @enderror">
                    @error('contract_end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="branch_id" class="form-label">{{ trans('messages.Branch') }}</label>
                    <select name="branch_id" id="branch_id" class="form-control @error('branch_id') is-invalid @enderror">
                        <option value="">{{ trans('messages.Select Branch') }}</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="manager_id" class="form-label">{{ trans('messages.Manager') }}</label>
                    <select name="manager_id" id="manager_id" class="form-control @error('manager_id') is-invalid @enderror">
                        <option value="">{{ trans('messages.No Manager') }}</option>
                        @foreach($managers as $manager)
                            <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                {{ $manager->full_name }} ({{ $manager->job_title }})
                            </option>
                        @endforeach
                    </select>
                    @error('manager_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Salary Information -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info">
                <i class="fas fa-money-bill-wave mr-2"></i>{{ trans('messages.Salary Information') }}
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
                
                <div class="col-md-12 mb-3">
                    <label for="other_allowances" class="form-label">{{ trans('messages.Other Allowances') }} (AED)</label>
                    <input type="number" name="other_allowances" id="other_allowances" value="{{ old('other_allowances', 0) }}" 
                           class="form-control @error('other_allowances') is-invalid @enderror" 
                           step="0.01" min="0">
                    @error('other_allowances')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="card shadow">
        <div class="card-body text-center">
            <button type="submit" class="btn btn-primary btn-lg mr-3">
                <i class="fas fa-save mr-2"></i>{{ trans('messages.Save Employee') }}
            </button>
            <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-lg">
                <i class="fas fa-times mr-2"></i>{{ trans('messages.Cancel') }}
            </a>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // تحديد تاريخ بداية العقد تلقائياً عند تحديد تاريخ التوظيف
    $('#hire_date').change(function() {
        if ($(this).val() && !$('#contract_start_date').val()) {
            $('#contract_start_date').val($(this).val());
        }
    });
});
</script>
@endpush
