{{-- 
Author: Eng.Fahed
Create User View - HR System
صفحة إنشاء مستخدم �ع أدو{{ trans('messages.roles') }} وصل{{ trans('messages.Permissions') }}
--}}

@extends('layouts.app')

@section('title', trans('messages.Add New User') . '')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Add New User') }}</h1>
        <p class="text-muted">إنشاء حساب مستخدم �</p>
    </div>
    <a href="{{ route('user-management.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Back to List') }}
    </a>
</div>

<!-- Error Messages -->
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle"></i>
    <strong>{{ trans("messages.Please correct the following errors:") }}</strong>
    <ul class="mb-0 mt-2">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<form method="POST" action="{{ route('user-management.store') }}" id="createUserForm">
    @csrf
    
    <div class="row">
        <!-- {{ trans('messages.Basic Information') }} -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user mr-2"></i>{{ trans('messages.Basic Information') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">{{ trans("messages.Full Name") }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   placeholder="أدخل {{ trans("messages.Full Name") }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">{{ trans("messages.Email Address") }} <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   placeholder="user@example.com" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">{{ trans("messages.Password") }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       placeholder="{{ trans("messages.Enter a strong password") }}" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                                        <i class="fas fa-eye" id="password-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">{{ trans("messages.Must be at least 8 characters") }}</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">{{ trans('messages.Confirm Password') }} <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                       class="form-control" placeholder="أعد إدخال {{ trans("messages.Password") }}" required>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password_confirmation')">
                                        <i class="fas fa-eye" id="password_confirmation-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="user_type" class="form-label">نوع المستخدم <span class="text-danger">*</span></label>
                            <select name="user_type" id="user_type" class="form-control @error('user_type') is-invalid @enderror" required>
                                <option value="">{{ trans("messages.Choose User Type") }}</option>
                                <option value="tenant_admin" {{ old('user_type') == 'tenant_admin' ? 'selected' : '' }}>مدير مؤسسة</option>
                                <option value="employee" {{ old('user_type') == 'employee' ? 'selected' : '' }}>موظف</option>
                            </select>
                            @error('user_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="tenant_id" class="form-label">المؤسسة</label>
                            <select name="tenant_id" id="tenant_id" class="form-control @error('tenant_id') is-invalid @enderror">
                                <option value="">{{ trans("messages.Choose Tenant") }}</option>
                                @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                        {{ $tenant->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tenant_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Roles and Permissions -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-shield mr-2"></i>{{ trans('messages.Roles & Permissions') }}
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Roles Section -->
                    <div class="mb-4">
                        <label class="form-label font-weight-bold">الأدو{{ trans('messages.roles') }}</label>
                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            @foreach($roles as $role)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" name="roles[]" 
                                       value="{{ $role->name }}" id="role_{{ $role->id }}"
                                       {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="role_{{ $role->id }}">
                                    <strong>{{ $role->name }}</strong>
                                    <small class="text-muted d-block">{{ $role->permissions->count() }} صلاحية</small>
                                </label>
                            </div>
                            @endforeach
                        </div>
                        @error('roles')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Direct Permissions Section -->
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">صل{{ trans('messages.Permissions') }} إضافية</label>
                        <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto;">
                            @foreach($permissions as $groupName => $groupPermissions)
                            <div class="mb-3">
                                <h6 class="text-primary mb-2">
                                    <i class="fas fa-folder mr-1"></i>{{ getGroupDisplayName($groupName) }}
                                </h6>
                                @foreach($groupPermissions as $permission)
                                <div class="form-check form-check-sm mb-1 ml-3">
                                    <input class="form-check-input" type="checkbox" name="permissions[]" 
                                           value="{{ $permission->name }}" id="perm_{{ $permission->id }}"
                                           {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label small" for="perm_{{ $permission->id }}">
                                        {{ getPermissionDisplayName($permission->name) }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            @endforeach
                        </div>
                        @error('permissions')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Submit Buttons -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary btn-lg mr-3">
                        <i class="fas fa-save mr-2"></i>{{ trans('messages.Create User') }}
                    </button>
                    <a href="{{ route('user-management.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times mr-2"></i>{{ trans('messages.Cancel') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
// Author: Eng.Fahed
// JavaScript for create user form

// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const eye = document.getElementById(fieldId + '-eye');
    
    if (field.type === 'password') {
        field.type = 'text';
        eye.classList.remove('fa-eye');
        eye.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        eye.classList.remove('fa-eye-slash');
        eye.classList.add('fa-eye');
    }
}

$(document).ready(function() {
    // Form validation
    $('#createUserForm').on('submit', function(e) {
        const name = $('#name').val().trim();
        const email = $('#email').val().trim();
        const password = $('#password').val();
        const passwordConfirmation = $('#password_confirmation').val();
        const userType = $('#user_type').val();
        
        if (!name) {
            alert('يرجى إدخال اسم المستخدم');
            $('#name').focus();
            e.preventDefault();
            return false;
        }
        
        if (!email) {
            alert('يرجى إدخال {{ trans("messages.Email Address") }}');
            $('#email').focus();
            e.preventDefault();
            return false;
        }
        
        if (!password) {
            alert('يرجى إدخال {{ trans("messages.Password") }}');
            $('#password').focus();
            e.preventDefault();
            return false;
        }
        
        if (password !== passwordConfirmation) {
            alert(�{{ trans('messages.Confirm Password') }}يدها غير متطابقتين');
            $('#password_confirmation').focus();
            e.preventDefault();
            return false;
        }
        
        if (!userType) {
            alert('يرجى اختي{{ trans('messages.roles') }} نوع المستخدم');
            $('#user_type').focus();
            e.preventDefault();
            return false;
        }
        
        // Confirmation
        if (!confirm(`هل أنت م{{ trans('messages.Confirm Password') }}د من {{ trans('messages.Create User') }} "${name}"؟`)) {
            e.preventDefault();
            return false;
        }
    });
    
    // Auto-select tenant based on user type
    $('#user_type').on('change', function() {
        const userType = $(this).val();
        if (userType === 'employee' || userType === 'tenant_admin') {
            // Auto-select current user's tenant if available
            @if(auth()->user()->tenant_id)
            $('#tenant_id').val('{{ auth()->user()->tenant_id }}');
            @endif
        }
    });
    
    // Role selection helper
    $('.form-check-input[name="roles[]"]').on('change', function() {
        const selectedRoles = $('.form-check-input[name="roles[]"]:checked').length;
        if (selectedRoles > 0) {
            $('.card-header h6:contains("الأدو{{ trans('messages.roles') }}")').html('<i class="fas fa-user-shield mr-2"></i>{{ trans('messages.Roles & Permissions') }} <span class="badge badge-primary">' + selectedRoles + '</span>');
        } else {
            $('.card-header h6:contains("الأدو{{ trans('messages.roles') }}")').html('<i class="fas fa-user-shield mr-2"></i>{{ trans('messages.Roles & Permissions') }}');
        }
    });
});
</script>
@endpush

