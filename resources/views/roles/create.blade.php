{{-- 
Author: Eng.Fahed
Create Role View - HR System
Create new role page with permissions selection
--}}

@extends('layouts.app')

@section('title', trans('messages.Create New Role'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Create New Role') }}</h1>
        <p class="text-muted">{{ trans('messages.Add a new role with appropriate permissions') }}</p>
    </div>
    <a href="{{ route('roles.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
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

<!-- Create Role Form -->
<form action="{{ route('roles.store') }}" method="POST">
    @csrf
    
    <div class="row">
        <!-- Basic Information -->
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
                        <input type="text" name="name" id="name" value="{{ old('name') }}" 
                               class="form-control @error('name') is-invalid @enderror" 
                               placeholder="{{ trans('messages.Enter role name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="guard_name" class="form-label">{{ trans('messages.Guard Name') }}</label>
                        <input type="text" name="guard_name" id="guard_name" value="{{ old('guard_name', 'web') }}" 
                               class="form-control @error('guard_name') is-invalid @enderror" readonly>
                        @error('guard_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Permissions Section -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-key mr-2"></i>{{ trans('messages.Assign Permissions') }}
                    </h6>
                    <div>
                        <button type="button" class="btn btn-success btn-sm" onclick="selectAllPermissions()">
                            <i class="fas fa-check-double"></i> {{ trans('messages.Select All') }}
                        </button>
                        <button type="button" class="btn btn-warning btn-sm" onclick="deselectAllPermissions()">
                            <i class="fas fa-times"></i> {{ trans('messages.Deselect All') }}
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $flatPermissions = $permissions->flatten();
                    @endphp
                    @if($flatPermissions->count() > 0)
                        @php
                            $groupedPermissions = $permissions; // Already grouped by controller
                        @endphp
                        
                        <div class="accordion" id="permissionsAccordion">
                            @foreach($groupedPermissions as $groupName => $groupPermissions)
                            @php
                                $displayGroupName = getGroupDisplayName($groupName);
                            @endphp
                            <div class="card">
                                <div class="card-header" id="heading{{ $loop->index }}">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link btn-block text-left collapsed" type="button" 
                                                data-toggle="collapse" data-target="#collapse{{ $loop->index }}" 
                                                aria-expanded="false" aria-controls="collapse{{ $loop->index }}">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>
                                                    <i class="fas fa-folder mr-2"></i>
                                                    {{ $displayGroupName }}
                                                </span>
                                                <div>
                                                    <input type="checkbox" class="group-checkbox mr-2" 
                                                           data-group="{{ $groupName }}" onchange="toggleGroup('{{ $groupName }}')">
                                                    <span class="badge badge-primary">{{ $groupPermissions->count() }}</span>
                                                </div>
                                            </div>
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapse{{ $loop->index }}" class="collapse" 
                                     data-parent="#permissionsAccordion">
                                    <div class="card-body">
                                        <div class="row">
                                            @foreach($groupPermissions as $permission)
                                            <div class="col-md-6 mb-2">
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                                           id="permission_{{ $permission->id }}" 
                                                           class="custom-control-input permission-checkbox" 
                                                           data-group="{{ $groupName }}" 
                                                           onchange="updateGroupCheckbox('{{ $groupName }}')"
                                                           {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="permission_{{ $permission->id }}">
                                                        {{ getPermissionDisplayName($permission->name) }}
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-key fa-3x text-gray-300 mb-3"></i>
                            <h5 class="text-gray-600">{{ trans('messages.No Permissions Available') }}</h5>
                            <p class="text-muted">{{ trans('messages.Please create permissions first') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center">
                    <button type="submit" class="btn btn-primary btn-lg mr-3">
                        <i class="fas fa-save mr-2"></i>{{ trans('messages.Create Role') }}
                    </button>
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-lg">
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
function selectAllPermissions() {
    $('.permission-checkbox').prop('checked', true);
    $('.group-checkbox').prop('checked', true);
}

function deselectAllPermissions() {
    $('.permission-checkbox').prop('checked', false);
    $('.group-checkbox').prop('checked', false);
}

function toggleGroup(groupName) {
    const groupCheckbox = $(`.group-checkbox[data-group="${groupName}"]`);
    const permissionCheckboxes = $(`.permission-checkbox[data-group="${groupName}"]`);
    
    permissionCheckboxes.prop('checked', groupCheckbox.is(':checked'));
}

function updateGroupCheckbox(groupName) {
    const permissionCheckboxes = $(`.permission-checkbox[data-group="${groupName}"]`);
    const checkedPermissions = permissionCheckboxes.filter(':checked');
    const groupCheckbox = $(`.group-checkbox[data-group="${groupName}"]`);
    
    if (checkedPermissions.length === 0) {
        groupCheckbox.prop('checked', false);
        groupCheckbox.prop('indeterminate', false);
    } else if (checkedPermissions.length === permissionCheckboxes.length) {
        groupCheckbox.prop('checked', true);
        groupCheckbox.prop('indeterminate', false);
    } else {
        groupCheckbox.prop('checked', false);
        groupCheckbox.prop('indeterminate', true);
    }
}

// Initialize group checkboxes on page load
$(document).ready(function() {
    @foreach($permissions as $groupName => $groupPermissions)
    updateGroupCheckbox('{{ $groupName }}');
    @endforeach
});
</script>
@endpush