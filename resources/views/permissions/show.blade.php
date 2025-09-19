{{-- 
Author: Eng.Fahed
Show Permission View - HR System
عرض تفاصيل الصلاحية والأدو{{ trans('messages.roles') }} المرتبطة بها
--}}

@extends('layouts.app')

@section('title', {{ trans('messages.Permission Details') }}: ' . $permission->name)

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Permission Details') }}</h1>
        <p class="text-muted">Viewing details for permission "{{ $permission->name }}"</p>
    </div>
    <div>
        <a href="{{ route('permissions.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm mr-2">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Back to List') }}
        </a>
        @can('permissions.edit')
        <a href="{{ route('permissions.edit', $permission) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
            <i class="fas fa-edit fa-sm text-white-50"></i> {{ trans('messages.Edit Permission') }}
        </a>
        @endcan
    </div>
</div>

<div class="row">
    <!-- {{ trans('messages.Permission Information') }} Card -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-key mr-2"></i>{{ trans('messages.Permission Information') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="icon-circle-lg bg-primary mx-auto mb-3">
                        <i class="fas fa-key text-white fa-2x"></i>
                    </div>
                    <h4 class="font-weight-bold text-gray-800">{{ $permission->name }}</h4>
                    @php
                        $parts = explode('.', $permission->name);
                        $module = $parts[0] ?? '';
                        $action = $parts[1] ?? '';
                    @endphp
                    <p class="text-muted">{{ getGroupDisplayName($module) }} - {{ getPermissionDisplayName($permission->name) }}</p>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tr>
                            <td class="font-weight-bold text-gray-600">Permission ID:</td>
                            <td class="text-gray-800">{{ $permission->id }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-gray-600">{{ trans('messages.Permission Name') }}: </td>
                            <td class="text-gray-800">{{ $permission->name }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-gray-600">Guard Name:</td>
                            <td class="text-gray-800">{{ $permission->guard_name }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-gray-600">Module:</td>
                            <td>
                                <span class="badge badge-info">{{ getGroupDisplayName($module) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-gray-600">Action:</td>
                            <td>
                                <span class="badge badge-success">{{ getPermissionDisplayName($permission->name) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-gray-600">Roles Count:</td>
                            <td>
                                <span class="badge badge-primary badge-pill">
                                    {{ $permission->roles->count() }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-gray-600">Created:</td>
                            <td class="text-gray-800">{{ $permission->created_at->format('Y/m/d H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-gray-600">Last Updated:</td>
                            <td class="text-gray-800">{{ $permission->updated_at->format('Y/m/d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Associated Roles -->
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-user-shield mr-2"></i>Associated Roles ({{ $permission->roles->count() }})
                </h6>
            </div>
            <div class="card-body">
                @if($permission->roles->count() > 0)
                    @foreach($permission->roles as $role)
                    <div class="card border-left-success mb-3">
                        <div class="card-body py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle bg-success mr-3">
                                        <i class="fas fa-user-shield text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="font-weight-bold mb-0">{{ $role->name }}</h6>
                                        <small class="text-muted">{{ $role->permissions->count() }} total permissions</small>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="badge badge-success badge-pill">{{ $role->users()->count() }} users</span>
                                    <div class="mt-1">
                                        <a href="{{ route('roles.show', $role) }}" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-user-shield fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-600">No Associated Roles</h5>
                        <p class="text-muted">This permission is not assigned to any roles yet</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-chart-pie mr-2"></i>Statistics
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="card border-primary">
                            <div class="card-body py-3">
                                <div class="h4 font-weight-bold text-primary">{{ $permission->roles->count() }}</div>
                                <div class="text-xs text-primary">Associated Roles</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="card border-success">
                            <div class="card-body py-3">
                                @php
                                    $totalUsers = $permission->roles->sum(function($role) {
                                        return $role->users()->count();
                                    });
                                @endphp
                                <div class="h4 font-weight-bold text-success">{{ $totalUsers }}</div>
                                <div class="text-xs text-success">Total Users</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actions Section -->
@canany(['permissions.edit', 'permissions.delete'])
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-cogs mr-2"></i>{{ trans('messages.Available Actions') }}
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 text-center">
                @can('permissions.edit')
                <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-warning btn-lg mr-3">
                    <i class="fas fa-edit mr-2"></i>{{ trans('messages.Edit Permission') }}
                </a>
                @endcan

                @can('permissions.delete')
                <form method="POST" action="{{ route('permissions.destroy', $permission) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirmDelete('{{ $permission->name }}')"
                            class="btn btn-danger btn-lg">
                        <i class="fas fa-trash mr-2"></i>{{ trans('messages.Delete Permission') }}
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
// JavaScript for permission show page

function confirmDelete(permissionName) {
    return confirm(`Are you sure you want to delete the permission "${permissionName}"?\n\nThis permission will be removed from all associated roles.\nThis action cannot be undone.`);
}

$(document).ready(function() {
    // Add hover effects to role cards
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
