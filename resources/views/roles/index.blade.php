{{-- 
Author: Eng.Fahed
Roles Index View - HR System
 إمكان{{ trans('messages.Search') }}{{ trans('messages.Filter') }}
--}}

@extends('layouts.app')

@section('title', trans('messages.Roles'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Roles') }}</h1>
    @can('roles.create')
    <a href="{{ route('roles.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> {{ trans('messages.Add New Role') }}
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
    <!-- Roles Overview Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            {{ trans('messages.Total Roles') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $roles->total() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-shield fa-2x text-gray-300"></i>
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
                            {{ trans('messages.Active Roles') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $roles->total() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            {{ trans('messages.Total Users') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ \App\Models\User::count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">{{ trans('messages.Roles List') }}</h6>
        <div class="dropdown no-arrow">
            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                @can('roles.create')
                <a class="dropdown-item" href="{{ route('roles.create') }}">
                    <i class="fas fa-plus fa-sm fa-fw mr-2 text-gray-400"></i>
                    {{ trans('messages.Add New Role') }}
                </a>
                @endcan
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" onclick="window.print()">
                    <i class="fas fa-print fa-sm fa-fw mr-2 text-gray-400"></i>
                    {{ trans('messages.Print') }}
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if($roles->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>{{ trans('messages.Role Name') }}</th>
                        <th>{{ trans('messages.Permissions Count') }}</th>
                        <th>{{ trans('messages.Users Count') }}</th>
                        <th>{{ trans('messages.Created Date') }}</th>
                        <th>{{ trans('messages.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="mr-3">
                                    <div class="icon-circle bg-primary">
                                        <i class="fas fa-user-shield text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-weight-bold">{{ $role->name }}</div>
                                    <div class="text-muted small">دور نظام</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-primary badge-pill">
                                {{ $role->permissions->count() }} {{ trans('messages.permission') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-success badge-pill">
                                {{ $role->users()->count() }} {{ trans('messages.user') }}
                            </span>
                        </td>
                        <td>
                            <span class="text-muted">{{ $role->created_at->format('Y/m/d') }}</span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                @can('roles.view')
                                <a href="{{ route('roles.show', $role) }}" 
                                   class="btn btn-info btn-sm" title="{{ trans('messages.View Details') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan

                                @can('roles.edit')
                                <a href="{{ route('roles.edit', $role) }}" 
                                   class="btn btn-warning btn-sm" title="{{ trans('messages.Edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan

                                @can('roles.delete')
                                <form method="POST" action="{{ route('roles.destroy', $role) }}" 
                                      class="d-inline"
                                      onsubmit="return confirm('{{ trans('messages.Are you sure you want to delete') }} {{ trans('messages.this role') }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="{{ trans('messages.Delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($roles->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    Showing {{ $roles->firstItem() }} to {{ $roles->lastItem() }} of {{ $roles->total() }} results
                </div>
                <div>
                    {{ $roles->links() }}
                </div>
            </div>
        </div>
        @endif
        @else
        <div class="text-center py-4">
            <div class="mb-3">
                <i class="fas fa-user-shield fa-3x text-gray-300"></i>
            </div>
            <h5 class="text-gray-600">{{ trans('messages.roles') }}</h5>
            <p class="text-muted">{{ trans('messages.Start by creating new roles for the system') }}</p>
            @can('roles.create')
            <a href="{{ route('roles.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ trans('messages.Add New Role') }}
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
// JavaScript for enhanced user experience

// Auto-hide success alerts after 5 seconds
$(document).ready(function() {
    setTimeout(function() {
        $('.alert-success').fadeOut('slow');
    }, 5000);
});

// Confirm delete with role name
function confirmDelete(roleName) {
    return confirm(`هل أنت م{{ trans('messages.Confirm Password') }}د من {{ trans('messages.Delete Role') }} "${roleName}"؟\n\nهذا الإجراء لا يمكن التراجع عنه.`);
}
</script>
@endpush
