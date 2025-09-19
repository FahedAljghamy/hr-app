{{-- 
Author: Eng.Fahed
{{ trans('messages.User Management') }} Index View - HR System
�{{ trans('messages.Roles') }}�{{ trans('messages.Permissions') }}
--}}

@extends('layouts.app')

@section('title', trans('messages.User Management'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.User Management') }}</h1>
        <p class="text-muted">{{ trans('messages.Manage users and their roles & permissions') }}</p>
    </div>
    @can('users.create')
    <a href="{{ route('user-management.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-user-plus fa-sm text-white-50"></i> {{ trans('messages.Add New User') }}
    </a>
    @endcan
</div>

<!-- Success/Error Messages -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle"></i>
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle"></i>
    {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<!-- Content Row -->
<div class="row">
    <!-- Users Overview Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            {{ trans('messages.Total Users') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $users->total() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            {{ trans('messages.Active Users') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $users->total() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            {{ trans('messages.Roles') }} {{ trans('messages.Available') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $roles->count() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            {{ trans('messages.Total Permissions') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ \Spatie\Permission\Models\Permission::count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-key fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- {{ trans('messages.Search') }} and {{ trans('messages.Filter') }} Section -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-search mr-2"></i>{{ trans('messages.Search') }}�
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('user-management.index') }}">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="search" class="form-label">{{ trans('messages.Search') }}</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           class="form-control" placeholder="{{ trans('messages.Search') }} {{ trans('messages.by name or email...') }}">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="role" class="form-label">{{ trans('messages.Role') }}</label>
                    <select name="role" id="role" class="form-control">
                        <option value="">{{ trans('messages.All Roles') }}</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="user_type" class="form-label">{{ trans('messages.User Type') }}</label>
                    <select name="user_type" id="user_type" class="form-control">
                        <option value="">{{ trans('messages.All Types') }}</option>
                        @foreach($userTypes as $type)
                            <option value="{{ $type }}" {{ request('user_type') == $type ? 'selected' : '' }}>
                                {{ getUserTypeDisplayName($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> {{ trans('messages.Search') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">{{ trans('messages.Users List') }} ({{ $users->total() }})</h6>
        <div class="dropdown no-arrow">
            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                @can('users.create')
                <a class="dropdown-item" href="{{ route('user-management.create') }}">
                    <i class="fas fa-user-plus fa-sm fa-fw mr-2 text-gray-400"></i>
                    {{ trans('messages.Add New User') }}
                </a>
                @endcan
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" onclick="window.print()">
                    <i class="fas fa-print fa-sm fa-fw mr-2 text-gray-400"></i>
                    {{ trans('messages.Print') }}
                </a>
                <a class="dropdown-item" href="#">
                    <i class="fas fa-download fa-sm fa-fw mr-2 text-gray-400"></i>
                    {{ trans('messages.Export Excel') }}
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if($users->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>{{ trans('messages.User') }}</th>
                        <th>{{ trans('messages.User Type') }}</th>
                        <th>{{ trans('messages.Roles') }}</th>
                        <th>{{ trans('messages.Permissions') }}</th>
                        <th>{{ trans('messages.Tenant') }}</th>
                        <th>{{ trans('messages.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <div class="icon-circle bg-primary">
                                        <span class="text-white font-weight-bold">
                                            {{ strtoupper(substr($user->name, 0, 2)) }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-weight-bold">{{ $user->name }}</div>
                                    <div class="text-muted small">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ getUserTypeBadgeClass($user->user_type) }}">
                                {{ getUserTypeDisplayName($user->user_type) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap">
                                @forelse($user->roles->take(2) as $role)
                                    <span class="badge badge-primary mr-1 mb-1">{{ $role->name }}</span>
                                @empty
                                    <span class="text-muted small">{{ trans('messages.No roles') }}</span>
                                @endforelse
                                
                                @if($user->roles->count() > 2)
                                    <span class="badge badge-secondary">+{{ $user->roles->count() - 2 }}</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-info">
                                {{ $user->getAllPermissions()->count() }} {{ trans('messages.permission') }}
                            </span>
                        </td>
                        <td>
                            <span class="text-muted">{{ $user->tenant ? $user->tenant->name : trans('messages.Not specified') }}</span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                @can('users.view')
                                <a href="{{ route('user-management.show', $user) }}" 
                                   class="btn btn-info btn-sm" title="{{ trans('messages.View Details') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan

                                @can('users.edit')
                                <a href="{{ route('user-management.edit', $user) }}" 
                                   class="btn btn-warning btn-sm" title="{{ trans('messages.Edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan

                                @can('users.delete')
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('user-management.destroy', $user) }}" 
                                      class="d-inline"
                                      onsubmit="return confirm('{{ trans('messages.Are you sure you want to delete this user?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="{{ trans('messages.Delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} results
                </div>
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
        @endif
        @else
        <div class="text-center py-4">
            <div class="mb-3">
                <i class="fas fa-users fa-3x text-gray-300"></i>
            </div>
            <h5 class="text-gray-600">{{ trans('messages.No') }} {{ trans('messages.users') }}</h5>
            <p class="text-muted">{{ trans('messages.Start by adding new users to the system') }}</p>
            @can('users.create')
            <a href="{{ route('user-management.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> {{ trans('messages.Add New User') }}
            </a>
            @endcan
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Author: Eng.Fahed
// JavaScript for user management

$(document).ready(function() {
    // Auto-hide success alerts after 5 seconds
    setTimeout(function() {
        $('.alert-success').fadeOut('slow');
    }, 5000);
    
    // Initialize DataTable if needed
    if ($('#dataTable').length) {
        $('#dataTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Arabic.json"
            },
            "order": [[ 0, "asc" ]],
            "pageLength": 25
        });
    }
});
</script>
@endpush

