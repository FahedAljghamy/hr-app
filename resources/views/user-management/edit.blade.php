{{-- 
Author: Eng.Fahed
Edit User View - HR System
Edit user page with roles and permissions
--}}

@extends('layouts.app')

@section('title', trans('messages.Edit User') . ': ' . $user->name)

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Edit User') }}</h1>
        <p class="text-muted">Editing user "{{ $user->name }}" and their roles & permissions</p>
    </div>
    <div>
        <a href="{{ route('user-management.show', $user) }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm mr-2">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Cancel Edit') }}
        </a>
        <a href="{{ route('user-management.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm">
            <i class="fas fa-list fa-sm text-white-50"></i> {{ trans('messages.All Users') }}
        </a>
    </div>
</div>

<!-- Error Messages -->
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle"></i>
    <strong>Please correct the following errors:</strong>
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

<form method="POST" action="{{ route('user-management.update', $user) }}" id="editUserForm">
    @csrf
    @method('PUT')
    
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
                            <label for="name" class="form-label">{{ trans('messages.Full Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   placeholder="Enter full name" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">{{ trans('messages.Email Address') }} <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   placeholder="user@example.com" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">{{ trans('messages.New Password') }} (Leave blank to keep current)</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       placeholder="Enter new password">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                                        <i class="fas fa-eye" id="password-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="form-text text-muted">Must be at least 8 characters if provided</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm {{ trans('messages.New Password') }}</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation" 
                                       class="form-control" placeholder="Confirm new password">
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
                            <label for="user_type" class="form-label">{{ trans('messages.User Type') }} <span class="text-danger">*</span></label>
                            <select name="user_type" id="user_type" class="form-control @error('user_type') is-invalid @enderror" required>
                                <option value="">Choose {{ trans('messages.User Type') }}</option>
                                <option value="tenant_admin" {{ old('user_type', $user->user_type) == 'tenant_admin' ? 'selected' : '' }}>{{ trans('messages.Tenant') }} Admin') }}</option>
                                <option value="employee" {{ old('user_type', $user->user_type) == 'employee' ? 'selected' : '' }}>{{ trans('messages.Employee') }}</option>
                            </select>
                            @error('user_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="tenant_id" class="form-label">{{ trans('messages.Tenant') }}</label>
                            <select name="tenant_id" id="tenant_id" class="form-control @error('tenant_id') is-invalid @enderror">
                                <option value="">Choose {{ trans('messages.Tenant') }}</option>
                                @foreach($tenants as $tenant)
                                    <option value="{{ $tenant->id }}" {{ old('tenant_id', $user->tenant_id) == $tenant->id ? 'selected' : '' }}>
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
        
        <!-- {{ trans('messages.Current Status') }} Card -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle mr-2"></i>{{ trans('messages.Current Status') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="card border-success">
                                <div class="card-body py-3">
                                    <div class="h4 font-weight-bold text-success">{{ $user->roles->count() }}</div>
                                    <div class="text-xs text-success">Assigned Roles</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card border-info">
                                <div class="card-body py-3">
                                    <div class="h4 font-weight-bold text-info">{{ $allPermissions->count() }}</div>
                                    <div class="text-xs text-info">Total Permissions</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Role Assignment -->
                    <div class="mt-3">
                        <h6 class="font-weight-bold text-gray-800 mb-2">Quick Role Assignment:</h6>
                        @foreach($roles as $role)
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="roles[]" 
                                   value="{{ $role->name }}" id="role_{{ $role->id }}"
                                   {{ in_array($role->name, $userRoles) ? 'checked' : '' }}>
                            <label class="form-check-label" for="role_{{ $role->id }}">
                                <strong>{{ $role->name }}</strong>
                                <small class="text-muted d-block">{{ $role->permissions->count() }} permissions</small>
                            </label>
                        </div>
                        @endforeach
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
                        <i class="fas fa-save mr-2"></i>{{ trans('messages.Update User') }}
                    </button>
                    <a href="{{ route('user-management.show', $user) }}" class="btn btn-secondary btn-lg">
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
// JavaScript for edit user form

// Toggle password visibility
function togglePassword(fieldId) {
    const field = $('#' + fieldId);
    const eye = $('#' + fieldId + '-eye');
    
    if (field.attr('type') === 'password') {
        field.attr('type', 'text');
        eye.removeClass('fa-eye').addClass('fa-eye-slash');
    } else {
        field.attr('type', 'password');
        eye.removeClass('fa-eye-slash').addClass('fa-eye');
    }
}

$(document).ready(function() {
    // Form validation
    $('#editUserForm').on('submit', function(e) {
        const name = $('#name').val().trim();
        const email = $('#email').val().trim();
        const password = $('#password').val();
        const passwordConfirmation = $('#password_confirmation').val();
        const userType = $('#user_type').val();
        
        if (!name) {
            alert('Please enter user name');
            $('#name').focus();
            e.preventDefault();
            return false;
        }
        
        if (!email) {
            alert('Please enter email address');
            $('#email').focus();
            e.preventDefault();
            return false;
        }
        
        if (password && password !== passwordConfirmation) {
            alert('Password and confirmation do not match');
            $('#password_confirmation').focus();
            e.preventDefault();
            return false;
        }
        
        if (!userType) {
            alert('Please select user type');
            $('#user_type').focus();
            e.preventDefault();
            return false;
        }
        
        // Confirmation
        if (!confirm(`Are you sure you want to update user "${name}"?`)) {
            e.preventDefault();
            return false;
        }
    });
    
    // Auto-hide success alerts after 5 seconds
    setTimeout(function() {
        $('.alert-success').fadeOut('slow');
    }, 5000);
});
</script>
@endpush
