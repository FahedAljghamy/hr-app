{{-- 
Author: Eng.Fahed
Show Role View - HR System
عرض تفاصيل الدور وال�رتبطة به
--}}

@extends('layouts.app')

@section('title', trans('messages.Role Details') . ': ' . $role->name)

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Role Details') }}</h1>
        <p class="text-muted">Viewing details for role "{{ $role->name }}" and its permissions</p>
    </div>
    <div>
        <a href="{{ route('roles.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm mr-2">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Back to List') }}
        </a>
        @can('roles.edit')
        <a href="{{ route('roles.edit', $role) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
            <i class="fas fa-edit fa-sm text-white-50"></i> {{ trans('messages.Edit Role') }}
        </a>
        @endcan
    </div>
</div>

<div class="row">
    <!-- {{ trans('messages.Role Information') }} Card -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user-shield mr-2"></i>{{ trans('messages.Role Information') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="icon-circle bg-primary mx-auto mb-3">
                        <i class="fas fa-user-shield text-white fa-2x"></i>
                    </div>
                    <h4 class="font-weight-bold text-gray-800">{{ $role->name }}</h4>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tr>
                            <td class="font-weight-bold text-gray-600">Role ID:</td>
                            <td class="text-gray-800">{{ $role->id }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-gray-600">{{ trans('messages.Role Name') }}: </td>
                            <td class="text-gray-800">{{ $role->name }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-gray-600">Permissions Count:</td>
                            <td>
                                <span class="badge badge-primary badge-pill">
                                    {{ $role->permissions->count() }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-gray-600">Users Count:</td>
                            <td>
                                <span class="badge badge-success badge-pill">
                                    {{ $role->users()->count() }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-gray-600">Created:</td>
                            <td class="text-gray-800">{{ $role->created_at->format('Y/m/d H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-gray-600">Last Updated:</td>
                            <td class="text-gray-800">{{ $role->updated_at->format('Y/m/d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-pie mr-2"></i>Statistics
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="card border-primary">
                            <div class="card-body py-3">
                                <div class="h4 font-weight-bold text-primary">{{ $role->permissions->count() }}</div>
                                <div class="text-xs text-primary">Total Permissions</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="card border-success">
                            <div class="card-body py-3">
                                <div class="h4 font-weight-bold text-success">{{ $role->users()->count() }}</div>
                                <div class="text-xs text-success">Assigned Users</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions List -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-key mr-2"></i>Associated Permissions ({{ $role->permissions->count() }})
                </h6>
            </div>
            <div class="card-body">
                @if($role->permissions->count() > 0)
                    @php
                        $groupedPermissions = $role->permissions->groupBy(function ($permission) {
                            return explode('.', $permission->name)[0];
                        });
                    @endphp

                    <div class="row">
                        @foreach($groupedPermissions as $groupName => $permissions)
                        <div class="col-md-6 mb-4">
                            <div class="card border-left-success">
                                <div class="card-header py-2">
                                    <h6 class="font-weight-bold text-success mb-0">
                                        <i class="fas fa-folder-open mr-2"></i>{{ getGroupDisplayName($groupName) }}
                                        <span class="badge badge-success badge-pill ml-2">{{ $permissions->count() }}</span>
                                    </h6>
                                </div>
                                <div class="card-body py-2">
                                    @foreach($permissions as $permission)
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="text-sm">{{ getPermissionDisplayName($permission->name) }}</span>
                                        <span class="badge badge-success badge-sm">
                                            <i class="fas fa-check mr-1"></i>Active
                                        </span>
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
                        <h5 class="text-gray-600">No Permissions</h5>
                        <p class="text-muted">No permissions have been assigned to this role yet</p>
                        @can('roles.edit')
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i>Add Permissions
                        </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Actions Section -->
@canany(['roles.edit', 'roles.delete'])
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-cogs mr-2"></i>{{ trans('messages.Available Actions') }}
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 text-center">
                @can('roles.edit')
                <a href="{{ route('roles.edit', $role) }}" class="btn btn-warning btn-lg mr-3">
                    <i class="fas fa-edit mr-2"></i>{{ trans('messages.Edit Role') }}
                </a>
                @endcan

                @can('roles.delete')
                <form method="POST" action="{{ route('roles.destroy', $role) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirmDelete('{{ $role->name }}')"
                            class="btn btn-danger btn-lg">
                        <i class="fas fa-trash mr-2"></i>{{ trans('messages.Delete Role') }}
                    </button>
                </form>
                @endcan
            </div>
        </div>
    </div>
</div>
@endcanany
@endsection

@push('scripts')
<script>
// Author: Eng.Fahed
// JavaScript for role show page

function confirmDelete(roleName) {
    return confirm(`Are you sure you want to delete the role "${roleName}"?\n\nThis role will be removed from all associated users.\nThis action cannot be undone.`);
}

$(document).ready(function() {
    // Add hover effects to cards
    $('.card').hover(
        function() {
            $(this).addClass('shadow-lg').removeClass('shadow');
        },
        function() {
            $(this).addClass('shadow').removeClass('shadow-lg');
        }
    );
});
</script>
@endpush

@push('styles')
<style>
/* Author: Eng.Fahed */
.icon-circle {
    height: 4rem;
    width: 4rem;
    border-radius: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.badge-sm {
    font-size: 0.65rem;
}

/* RTL Support */
@if(app()->getLocale() == 'ar')
.table td, .table th {
    text-align: right;
}
@endif
</style>
@endpush