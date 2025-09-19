{{-- 
Author: Eng.Fahed
Branches Index View - HR System
عرض قائمة الفروع مع إمكانية البحث والفلترة
--}}

@extends('layouts.app')

@section('title', trans('messages.Branches'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Branches') }}</h1>
        <p class="text-muted">{{ trans('messages.Manage company branches and locations') }}</p>
    </div>
    @can('branches.create')
    <a href="{{ route('branches.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> {{ trans('messages.Add New Branch') }}
    </a>
    @endcan
</div>

<!-- Success/Error Messages -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle"></i>
    <strong>{{ trans('messages.Success') }}!</strong> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle"></i>
    <strong>{{ trans('messages.Error') }}!</strong> {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
@endif

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            {{ trans('messages.Total Branches') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $branches->total() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
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
                            {{ trans('messages.Active Branches') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $branches->where('is_active', true)->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-search mr-2"></i>{{ trans('messages.Search') }} {{ trans('messages.and') }} {{ trans('messages.Filter') }}
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('branches.index') }}">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="search" class="form-label">{{ trans('messages.Search') }}</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           class="form-control" placeholder="{{ trans('messages.Search by name, address, or manager...') }}">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="status" class="form-label">{{ trans('messages.Status') }}</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">{{ trans('messages.All Statuses') }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ trans('messages.Active') }}</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ trans('messages.Inactive') }}</option>
                    </select>
                </div>
                
                <div class="col-md-2 mb-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> {{ trans('messages.Search') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Branches Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">{{ trans('messages.All Branches') }} ({{ $branches->total() }})</h6>
        <div class="dropdown no-arrow">
            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown">
                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                @can('branches.create')
                <a class="dropdown-item" href="{{ route('branches.create') }}">
                    <i class="fas fa-plus fa-sm fa-fw mr-2 text-gray-400"></i>
                    {{ trans('messages.Add New Branch') }}
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
        @if($branches->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>{{ trans('messages.Branch Name') }}</th>
                        <th>{{ trans('messages.Manager Name') }}</th>
                        <th>{{ trans('messages.Address') }}</th>
                        <th>{{ trans('messages.Phone Number') }}</th>
                        <th>{{ trans('messages.Working Hours') }}</th>
                        <th>{{ trans('messages.Status') }}</th>
                        <th>{{ trans('messages.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($branches as $branch)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-primary text-white mr-3">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div>
                                    <div class="font-weight-bold">{{ $branch->name }}</div>
                                    @if($branch->email)
                                        <small class="text-muted">{{ $branch->email }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>{{ $branch->manager_name ?: trans('messages.Not specified') }}</td>
                        <td>
                            <div class="text-truncate" style="max-width: 200px;" title="{{ $branch->address }}">
                                {{ $branch->address }}
                            </div>
                        </td>
                        <td>{{ $branch->phone ?: '-' }}</td>
                        <td>{{ $branch->formatted_working_hours }}</td>
                        <td>
                            <span class="badge {{ $branch->status_badge_class }}">
                                {{ trans('messages.' . $branch->status_text) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                @can('branches.view')
                                <a href="{{ route('branches.show', $branch) }}" 
                                   class="btn btn-info btn-sm" title="{{ trans('messages.View Details') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan

                                @can('branches.edit')
                                <a href="{{ route('branches.edit', $branch) }}" 
                                   class="btn btn-warning btn-sm" title="{{ trans('messages.Edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan

                                @can('branches.delete')
                                <form method="POST" action="{{ route('branches.destroy', $branch) }}" 
                                      class="d-inline"
                                      onsubmit="return confirm('{{ trans('messages.Are you sure you want to delete this branch?') }}')">
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
        @if($branches->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    {{ trans('messages.Showing') }} {{ $branches->firstItem() }} {{ trans('messages.to') }} {{ $branches->lastItem() }} 
                    {{ trans('messages.of') }} {{ $branches->total() }} {{ trans('messages.results') }}
                </div>
                <div>
                    {{ $branches->links() }}
                </div>
            </div>
        </div>
        @endif

        @else
        <!-- Empty State -->
        <div class="text-center py-4">
            <div class="mb-3">
                <i class="fas fa-building fa-3x text-gray-300"></i>
            </div>
            <h5 class="text-gray-600">{{ trans('messages.No') }} {{ trans('messages.branches') }}</h5>
            <p class="text-muted">{{ trans('messages.Start by adding new branches to the system') }}</p>
            @can('branches.create')
            <a href="{{ route('branches.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ trans('messages.Add New Branch') }}
            </a>
            @endcan
        </div>
        @endif
    </div>
</div>
@endsection
