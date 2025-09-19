{{-- 
Author: Eng.Fahed
{{ trans('messages.Edit Permission') }} View - HR System
صف{{ trans('messages.Edit Permission') }}
--}}

@extends('layouts.app')

@section('title', {{ trans('messages.Edit Permission') }}: ' . $permission->name)

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Edit Permission') }}</h1>
        <p class="text-muted">Editing permission "{{ $permission->name }}"</p>
    </div>
    <div>
        <a href="{{ route('permissions.show', $permission) }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm mr-2">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Cancel') }} Edit') }}
        </a>
        <a href="{{ route('permissions.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm">
            <i class="fas fa-list fa-sm text-white-50"></i> {{ trans('messages.All Permissions') }}
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

<!-- Success Message -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<form method="POST" action="{{ route('permissions.update', $permission) }}" id="editPermissionForm">
    @csrf
    @method('PUT')
    
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
                        <input type="text" name="name" id="name" value="{{ old('name', $permission->name) }}" 
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
        
        <!-- {{ trans('messages.Permission Details') }} -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle mr-2"></i>{{ trans('messages.Current Status') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="icon-circle bg-info mx-auto mb-3">
                            <i class="fas fa-key text-white"></i>
                        </div>
                        <h5 class="font-weight-bold text-gray-800">{{ $permission->name }}</h5>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-borderless table-sm">
                            <tr>
                                <td class="font-weight-bold text-gray-600">Permission ID:</td>
                                <td class="text-gray-800">{{ $permission->id }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-600">Guard Name:</td>
                                <td class="text-gray-800">{{ $permission->guard_name }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-600">Roles Using:</td>
                                <td>
                                    <span class="badge badge-primary badge-pill">
                                        {{ $permission->roles->count() }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-600">Created:</td>
                                <td class="text-gray-800">{{ $permission->created_at->format('Y/m/d') }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold text-gray-600">Updated:</td>
                                <td class="text-gray-800">{{ $permission->updated_at->format('Y/m/d') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Associated Roles -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-user-shield mr-2"></i>Associated Roles
                    </h6>
                </div>
                <div class="card-body">
                    @if($permission->roles->count() > 0)
                        @foreach($permission->roles as $role)
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="font-weight-medium">{{ $role->name }}</span>
                            <span class="badge badge-success badge-sm">
                                {{ $role->users()->count() }} users
                            </span>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted">
                            <i class="fas fa-info-circle mb-2"></i>
                            <p class="mb-0">Not assigned to any roles</p>
                        </div>
                    @endif
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
                        <i class="fas fa-save mr-2"></i>{{ trans('messages.Update Permission') }}
                    </button>
                    <a href="{{ route('permissions.show', $permission) }}" class="btn btn-secondary btn-lg">
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
// JavaScript for permission editing

$(document).ready(function() {
    // Form validation
    $('#editPermissionForm').on('submit', function(e) {
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
        
        // Check if name changed
        const originalName = '{{ $permission->name }}';
        if (permissionName !== originalName) {
            if (!confirm(`Are you sure you want to change permission name from "${originalName}" to "${permissionName}"?`)) {
                e.preventDefault();
                return false;
            }
        }
    });
    
    // Auto-hide success alerts after 5 seconds
    setTimeout(function() {
        $('.alert-success').fadeOut('slow');
    }, 5000);
});
</script>
@endpush
