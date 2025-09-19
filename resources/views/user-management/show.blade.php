{{-- 
Author: Eng.Fahed
Show User View - HR System
عرض تفاصيل المستخدم وأدو{{ trans('messages.roles') }}ه وصل{{ trans('messages.Permissions') }}ه
--}}

@extends('layouts.app')

@section('title', {{ trans('messages.User Details') }}: ' . $user->name)

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.User Details') }}</h1>
        <p class="text-muted">Viewing details for user "{{ $user->name }}"</p>
    </div>
    <div>
        <a href="{{ route('user-management.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm mr-2">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Back to List') }}
        </a>
        @can('users.edit')
        <a href="{{ route('user-management.edit', $user) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
            <i class="fas fa-edit fa-sm text-white-50"></i> {{ trans('messages.Edit User') }}
        </a>
        @endcan
    </div>
</div>

<div class="row">
    <!-- User Information Card -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user mr-2"></i>User Information
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="icon-circle-lg bg-primary mx-auto mb-3">
                        <span class="text-white font-weight-bold h4">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </span>
                    </div>
                    <h4 class="font-weight-bold text-gray-800">{{ $user->name }}</h4>
                    <p class="text-muted">{{ $user->email }}</p>
                    <span class="badge {{ getUserTypeBadgeClass($user->user_type) }} badge-pill">
                        {{ getUserTypeDisplayName($user->user_type) }}
                    </span>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <tr>
                            <td class="font-weight-bold text-gray-600">User ID:</td>
                            <td class="text-gray-800">{{ $user->id }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-gray-600">Email:</td>
                            <td class="text-gray-800">{{ $user->email }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-gray-600">{{ trans('messages.User Type') }}: </td>
                            <td>
                                <span class="badge {{ getUserTypeBadgeClass($user->user_type) }}">
                                    {{ getUserTypeDisplayName($user->user_type) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-gray-600">{{ trans('messages.Tenant') }}: </td>
                            <td class="text-gray-800">{{ $user->tenant ? $user->tenant->name : 'Not Assigned' }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-gray-600">Total Permissions:</td>
                            <td>
                                <span class="badge badge-info badge-pill">
                                    {{ $allPermissions->count() }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-gray-600">Created:</td>
                            <td class="text-gray-800">{{ $user->created_at->format('Y/m/d H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-gray-600">Last Updated:</td>
                            <td class="text-gray-800">{{ $user->updated_at->format('Y/m/d H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles and Permissions -->
    <div class="col-lg-8">
        <!-- User Roles -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-user-shield mr-2"></i>Assigned Roles ({{ $user->roles->count() }})
                </h6>
            </div>
            <div class="card-body">
                @if($user->roles->count() > 0)
                    <div class="row">
                        @foreach($user->roles as $index => $role)
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-{{ ['primary', 'success', 'info', 'warning'][$index % 4] }}">
                                <div class="card-body py-3">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h6 class="font-weight-bold mb-0">{{ $role->name }}</h6>
                                            <small class="text-muted">{{ $role->permissions->count() }} permissions</small>
                                        </div>
                                        <div>
                                            <a href="{{ route('roles.show', $role) }}" class="btn btn-outline-{{ ['primary', 'success', 'info', 'warning'][$index % 4] }} btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-user-shield fa-3x text-gray-300 mb-3"></i>
                        <h5 class="text-gray-600">No Roles Assigned</h5>
                        <p class="text-muted">This user has no roles assigned yet</p>
                        @can('users.edit')
                        <a href="{{ route('user-management.edit', $user) }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i>Assign Roles
                        </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>

        <!-- {{ trans('messages.All Permissions') }} -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-key mr-2"></i>{{ trans('messages.All Permissions') }} ({{ $allPermissions->count() }})
                </h6>
            </div>
            <div class="card-body">
                @if($allPermissions->count() > 0)
                    @php
                        $groupedUserPermissions = $allPermissions->groupBy(function ($permission) {
                            return explode('.', $permission->name)[0];
                        });
                    @endphp

                    <div class="row">
                        @foreach($groupedUserPermissions as $groupName => $permissions)
                        <div class="col-md-6 mb-3">
                            <div class="card border-left-info">
                                <div class="card-header py-2">
                                    <h6 class="font-weight-bold text-info mb-0">
                                        <i class="fas fa-folder-open mr-2"></i>{{ getGroupDisplayName($groupName) }}
                                        <span class="badge badge-info badge-pill ml-2">{{ $permissions->count() }}</span>
                                    </h6>
                                </div>
                                <div class="card-body py-2">
                                    @foreach($permissions as $permission)
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="text-sm">{{ getPermissionDisplayName($permission->name) }}</span>
                                        @if($user->hasDirectPermission($permission->name))
                                            <span class="badge badge-warning badge-sm">Direct</span>
                                        @else
                                            <span class="badge badge-success badge-sm">Via Role</span>
                                        @endif
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
                        <p class="text-muted">This user has no permissions assigned</p>
                        @can('users.edit')
                        <a href="{{ route('user-management.edit', $user) }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i>Assign Permissions
                        </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Actions Section -->
@canany(['users.edit', 'users.delete'])
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-cogs mr-2"></i>{{ trans('messages.Available Actions') }}
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12 text-center">
                @can('users.edit')
                <a href="{{ route('user-management.edit', $user) }}" class="btn btn-warning btn-lg mr-3">
                    <i class="fas fa-edit mr-2"></i>{{ trans('messages.Edit User') }}
                </a>
                @endcan

                @can('users.delete')
                @if($user->id !== auth()->id())
                <form method="POST" action="{{ route('user-management.destroy', $user) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirmDelete('{{ $user->name }}')"
                            class="btn btn-danger btn-lg">
                        <i class="fas fa-trash mr-2"></i>{{ trans('messages.Delete User') }}
                    </button>
                </form>
                @endif
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
// JavaScript for user show page

function confirmDelete(userName) {
    return confirm(`Are you sure you want to delete user "${userName}"?\n\nThis action cannot be undone.`);
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
