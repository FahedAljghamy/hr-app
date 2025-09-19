{{-- 
Author: Eng.Fahed
Create Permission View - HR System
Create new permission page
--}}

@extends('layouts.app')

@section('title', trans('messages.Create New Permission'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Create New Permission') }}</h1>
        <p class="text-muted">{{ trans('messages.Add a new permission to the system') }}</p>
    </div>
    <a href="{{ route('permissions.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Back to List') }}
    </a>
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

<form method="POST" action="{{ route('permissions.store') }}" id="createPermissionForm">
    @csrf
    
    <div class="row">
        <!-- {{ trans('messages.Basic Information') }} -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-key mr-2"></i>{{ trans('messages.Permission Information') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name" class="form-label">{{ trans('messages.Permission Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" 
                               class="form-control @error('name') is-invalid @enderror" 
                               placeholder="e.g. users.view" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Use format: module.action (e.g. users.view, employees.create)</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Selection Helper -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-magic mr-2"></i>Quick Generator
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="quick_type" class="form-label">Module Type</label>
                        <select id="quick_type" class="form-control">
                            <option value="">Choose Module</option>
                            <option value="users">{{ trans('messages.User Management') }}</option>
                            <option value="roles">Role Management</option>
                            <option value="permissions">Permission Management</option>
                            <option value="employees">{{ trans('messages.Employee') }} Management</option>
                            <option value="attendance">Attendance</option>
                            <option value="leaves">Leave Management</option>
                            <option value="payroll">Payroll Management</option>
                            <option value="reports">Reports</option>
                            <option value="dashboard">Dashboard</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="quick_action" class="form-label">Action Type</label>
                        <select id="quick_action" class="form-control">
                            <option value="">Choose Action</option>
                            <option value="view">View</option>
                            <option value="create">Create</option>
                            <option value="edit">Edit</option>
                            <option value="delete">Delete</option>
                            <option value="assign">Assign</option>
                            <option value="approve">Approve</option>
                            <option value="export">Export</option>
                        </select>
                    </div>
                    
                    <button type="button" class="btn btn-primary btn-block" onclick="generatePermissionName()">
                        <i class="fas fa-wand-magic mr-2"></i>Generate {{ trans('messages.Permission Name') }}
                    </button>
                </div>
            </div>

            <!-- Permission Preview -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-eye mr-2"></i>Preview
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="icon-circle bg-success mx-auto mb-3">
                            <i class="fas fa-key text-white"></i>
                        </div>
                        <div id="permission-preview" class="font-weight-bold text-gray-800">
                            Permission name will appear here
                        </div>
                        <small class="text-muted">Preview of the permission to be created</small>
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
                        <i class="fas fa-save mr-2"></i>Create Permission
                    </button>
                    <a href="{{ route('permissions.index') }}" class="btn btn-secondary btn-lg">
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
// JavaScript for permission creation

function generatePermissionName() {
    const type = $('#quick_type').val();
    const action = $('#quick_action').val();
    
    if (type && action) {
        const permissionName = `${type}.${action}`;
        $('#name').val(permissionName);
        $('#permission-preview').text(permissionName);
        
        // Update preview card color based on module
        const moduleColors = {
            'users': 'bg-primary',
            'roles': 'bg-success', 
            'permissions': 'bg-info',
            'employees': 'bg-warning',
            'attendance': 'bg-secondary',
            'leaves': 'bg-danger',
            'payroll': 'bg-dark',
            'reports': 'bg-info',
            'dashboard': 'bg-primary'
        };
        
        $('.icon-circle').removeClass('bg-success bg-primary bg-info bg-warning bg-secondary bg-danger bg-dark')
                         .addClass(moduleColors[type] || 'bg-success');
    } else {
        alert('Please choose both module and action first');
    }
}

$(document).ready(function() {
    // Update preview when typing manually
    $('#name').on('input', function() {
        const value = $(this).val();
        $('#permission-preview').text(value || 'Permission name will appear here');
    });
    
    // Form validation
    $('#createPermissionForm').on('submit', function(e) {
        const permissionName = $('#name').val().trim();
        
        if (!permissionName) {
            e.preventDefault();
            alert('Please enter permission name');
            $('#name').focus();
            return false;
        }
        
        if (!permissionName.includes('.')) {
            if (!confirm('Permission name should follow format "module.action". Continue anyway?')) {
                e.preventDefault();
                return false;
            }
        }
        
        // Confirmation
        if (!confirm(`Are you sure you want to create permission "${permissionName}"?`)) {
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

@push('styles')
<style>
/* Author: Eng.Fahed */
.icon-circle {
    height: 3rem;
    width: 3rem;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* RTL Support */
@if(app()->getLocale() == 'ar')
.form-label {
    text-align: right;
}
@endif
</style>
@endpush