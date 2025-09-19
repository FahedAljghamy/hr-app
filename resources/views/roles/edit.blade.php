{{-- 
Author: Eng.Fahed
{{ trans('messages.Edit Role') }} View - HR System
صف{{ trans('messages.Edit Role') }}{{ trans('messages.Permissions') }}ه
--}}

@extends('layouts.app')

@section('title', trans('messages.Edit Role') . ': ' . $role->name)

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Edit Role') }}</h1>
        <p class="text-muted">Editing role "{{ $role->name }}" and its permissions</p>
    </div>
    <div>
        <a href="{{ route('roles.show', $role) }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm mr-2">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Cancel Edit') }}
        </a>
        <a href="{{ route('roles.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm">
            <i class="fas fa-list fa-sm text-white-50"></i> {{ trans('messages.All Roles') }}
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

<form method="POST" action="{{ route('roles.update', $role) }}" id="editRoleForm">
    @csrf
    @method('PUT')
    
    <div class="row">
        <!-- {{ trans('messages.Basic Information') }} -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-shield mr-2"></i>{{ trans('messages.Role Information') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="name" class="form-label">{{ trans('messages.Role Name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" 
                               class="form-control @error('name') is-invalid @enderror" 
                               placeholder="e.g. HR Manager" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Current Permissions Summary -->
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-info-circle mr-2"></i>{{ trans('messages.Current Status') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="h4 font-weight-bold text-primary">{{ $role->permissions->count() }}</div>
                                    <div class="text-xs text-muted">Current Permissions</div>
                                </div>
                                <div class="col-6">
                                    <div class="h4 font-weight-bold text-success">{{ $role->users()->count() }}</div>
                                    <div class="text-xs text-muted">Users Assigned</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Permissions Section -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-key mr-2"></i>{{ trans('messages.Update Permissions') }}
                    </h6>
                    <div>
                        <button type="button" class="btn btn-success btn-sm" onclick="selectAllPermissions()">
                            <i class="fas fa-check-double"></i> {{ trans('messages.Select All') }}
                        </button>
                        <button type="button" class="btn btn-secondary btn-sm ml-2" onclick="deselectAllPermissions()">
                            <i class="fas fa-times"></i> {{ trans('messages.Deselect All') }}
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($permissions->count() > 0)
                        <div class="row">
                            @foreach($permissions as $groupName => $groupPermissions)
                            <div class="col-md-6 mb-4">
                                <div class="card border-left-info">
                                    <div class="card-header py-2 d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0 font-weight-bold text-info">
                                            <i class="fas fa-folder mr-2"></i>{{ getGroupDisplayName($groupName) }}
                                        </h6>
                                        <div class="form-check">
                                            <input class="form-check-input group-checkbox" type="checkbox" 
                                                   data-group="{{ $groupName }}" onchange="toggleGroup('{{ $groupName }}')">
                                            <label class="form-check-label text-sm">{{ trans('messages.Select All') }}</label>
                                        </div>
                                    </div>
                                    <div class="card-body py-2">
                                        @foreach($groupPermissions as $permission)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input permission-checkbox group-{{ $groupName }}" 
                                                   type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                                                   id="perm_{{ $permission->id }}"
                                                   {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}
                                                   onchange="updateGroupCheckbox('{{ $groupName }}')">
                                            <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                <strong>{{ getPermissionDisplayName($permission->name) }}</strong>
                                                @if(in_array($permission->name, $rolePermissions))
                                                    <span class="badge badge-success badge-sm ml-1">Current</span>
                                                @endif
                                                <small class="text-muted d-block">{{ $permission->name }}</small>
                                            </label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-key fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">No permissions available currently</p>
                        </div>
                    @endif

                    @error('permissions')
                        <div class="alert alert-danger mt-3">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <!-- Changes Summary -->
    <div id="changesSummary" class="card shadow mb-4" style="display: none;">
        <div class="card-header py-3 bg-warning">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-exclamation-triangle mr-2"></i>Changes Summary
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="font-weight-bold text-success">New Permissions:</h6>
                    <ul id="newPermissions" class="list-unstyled text-success"></ul>
                </div>
                <div class="col-md-6">
                    <h6 class="font-weight-bold text-danger">Removed Permissions:</h6>
                    <ul id="removedPermissions" class="list-unstyled text-danger"></ul>
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
                        <i class="fas fa-save mr-2"></i>{{ trans('messages.Save Changes') }}
                    </button>
                    <a href="{{ route('roles.show', $role) }}" class="btn btn-secondary btn-lg">
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
// JavaScript functions for role editing form

// {{ trans('messages.Current Permissions') }} (للمق{{ trans('messages.roles') }}نة)
const originalPermissions = @json($rolePermissions);

// تحديد جميع ال{{ trans('messages.permissions') }}
function selectAllPermissions() {
    $('.permission-checkbox').prop('checked', true);
    $('.group-checkbox').prop('checked', true);
    updateChangesSummary();
}

//  جميع ال{{ trans('messages.permissions') }}
function deselectAllPermissions() {
    $('.permission-checkbox').prop('checked', false);
    $('.group-checkbox').prop('checked', false).prop('indeterminate', false);
    updateChangesSummary();
}

// تبديل تحديد مجموعة كاملة
function toggleGroup(groupName) {
    const groupCheckbox = $(`[data-group="${groupName}"]`);
    const groupPermissions = $(`.group-${groupName}`);
    
    groupPermissions.prop('checked', groupCheckbox.is(':checked'));
    updateChangesSummary();
}

// تحديث حالة checkbox المجموعة بناءً على العناصر المحددة
function updateGroupCheckbox(groupName) {
    const groupCheckbox = $(`[data-group="${groupName}"]`);
    const groupPermissions = $(`.group-${groupName}`);
    const checkedPermissions = $(`.group-${groupName}:checked`);
    
    if (checkedPermissions.length === 0) {
        groupCheckbox.prop('checked', false).prop('indeterminate', false);
    } else if (checkedPermissions.length === groupPermissions.length) {
        groupCheckbox.prop('checked', true).prop('indeterminate', false);
    } else {
        groupCheckbox.prop('checked', false).prop('indeterminate', true);
    }
    
    updateChangesSummary();
}

// تحديث ملخص التغييرات
function updateChangesSummary() {
    const selectedPermissions = $('.permission-checkbox:checked').map(function() {
        return this.value;
    }).get();
    
    const newPermissions = selectedPermissions.filter(p => !originalPermissions.includes(p));
    const removedPermissions = originalPermissions.filter(p => !selectedPermissions.includes(p));
    
    const changesSummary = $('#changesSummary');
    const newPermissionsList = $('#newPermissions');
    const removedPermissionsList = $('#removedPermissions');
    
    // مسح القوائم
    newPermissionsList.empty();
    removedPermissionsList.empty();
    
    // Add new permissions
    newPermissions.forEach(permission => {
        newPermissionsList.append(`<li><i class="fas fa-plus mr-2"></i>${permission}</li>`);
    });
    
    // �حذوفة
    removedPermissions.forEach(permission => {
        removedPermissionsList.append(`<li><i class="fas fa-minus mr-2"></i>${permission}</li>`);
    });
    
    // إظه{{ trans('messages.roles') }}/إخفاء ملخص التغييرات
    if (newPermissions.length > 0 || removedPermissions.length > 0) {
        changesSummary.show();
    } else {
        changesSummary.hide();
    }
}

$(document).ready(function() {
    // تهيئة حالة checkboxes المجموعات عند تحميل الصفحة
    $('.group-checkbox').each(function() {
        const groupName = $(this).data('group');
        updateGroupCheckbox(groupName);
    });
    
    // �عي الأحداث لل{{ trans('messages.permissions') }}
    $('.permission-checkbox').on('change', updateChangesSummary);
    
    // التحقق من صحة النموذج قبل الإرسال
    $('#editRoleForm').on('submit', function(e) {
        const roleName = $('#name').val().trim();
        
        if (!roleName) {
            e.preventDefault();
            alert('Please enter role name');
            $('#name').focus();
            return false;
        }
        
        // {{ trans('messages.Confirm Password') }}يد التحديث
        const selectedPermissions = $('.permission-checkbox:checked').map(function() {
            return this.value;
        }).get();
        const hasChanges = JSON.stringify(selectedPermissions.sort()) !== JSON.stringify(originalPermissions.sort()) || 
                          $('#name').val() !== '{{ $role->name }}';
        
        if (hasChanges) {
            if (!confirm(`Are you sure you want to save changes to role "${roleName}"?`)) {
                e.preventDefault();
                return false;
            }
        } else {
            alert('No changes were made');
            e.preventDefault();
            return false;
        }
    });
    
    // تحديث ملخص التغييرات الأولي
    updateChangesSummary();
    
    // Auto-hide success alerts after 5 seconds
    setTimeout(function() {
        $('.alert-success').fadeOut('slow');
    }, 5000);
});
</script>
@endpush
