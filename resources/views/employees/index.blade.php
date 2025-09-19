{{-- 
Author: Eng.Fahed
Employees Index View - HR System
قائمة الموظفين مع البحث والفلترة
--}}

@extends('layouts.app')

@section('title', trans('messages.All Employees'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.All Employees') }}</h1>
        <p class="text-muted">{{ trans('messages.Manage and view all employees in your organization') }}</p>
    </div>
    @can('employees.create')
    <a href="{{ route('employees.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> {{ trans('messages.Add Employee') }}
    </a>
    @endcan
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            {{ trans('messages.Total Employees') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
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
                            {{ trans('messages.Active Employees') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-check fa-2x text-gray-300"></i>
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
                            {{ trans('messages.Contract Expiring') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['contract_expiring'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-times fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            {{ trans('messages.Visa Expiring') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['visa_expiring'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-passport fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter mr-2"></i>{{ trans('messages.Search and Filter') }}
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('employees.index') }}">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="search" class="form-label">{{ trans('messages.Search') }}</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           value="{{ request('search') }}" placeholder="{{ trans('messages.Name, ID, Email, Job Title') }}">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="status" class="form-label">{{ trans('messages.Employment Status') }}</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">{{ trans('messages.All Statuses') }}</option>
                        @foreach($statuses as $key => $status)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                {{ trans('messages.' . $status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="department" class="form-label">{{ trans('messages.Department') }}</label>
                    <select name="department" id="department" class="form-control">
                        <option value="">{{ trans('messages.All Departments') }}</option>
                        @foreach($departments as $key => $dept)
                            <option value="{{ $key }}" {{ request('department') === $key ? 'selected' : '' }}>
                                {{ trans('messages.' . $dept) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="branch_id" class="form-label">{{ trans('messages.Branch') }}</label>
                    <select name="branch_id" id="branch_id" class="form-control">
                        <option value="">{{ trans('messages.All Branches') }}</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-search mr-1"></i>{{ trans('messages.Search') }}
                    </button>
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo mr-1"></i>{{ trans('messages.Clear Filters') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Employees Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table mr-2"></i>{{ trans('messages.Employees List') }}
        </h6>
    </div>
    <div class="card-body">
        @if($employees->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        <th>{{ trans('messages.Employee ID') }}</th>
                        <th>{{ trans('messages.Full Name') }}</th>
                        <th>{{ trans('messages.Job Title') }}</th>
                        <th>{{ trans('messages.Department') }}</th>
                        <th>{{ trans('messages.Branch') }}</th>
                        <th>{{ trans('messages.Employment Status') }}</th>
                        <th>{{ trans('messages.Hire Date') }}</th>
                        <th>{{ trans('messages.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employees as $employee)
                    <tr>
                        <td>
                            <span class="badge badge-secondary">{{ $employee->employee_id }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($employee->profile_photo)
                                    <img src="{{ Storage::url($employee->profile_photo) }}" 
                                         alt="{{ $employee->full_name }}" 
                                         class="rounded-circle mr-2" 
                                         width="32" height="32">
                                @else
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-2" 
                                         style="width: 32px; height: 32px; font-size: 14px;">
                                        {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="font-weight-bold">{{ $employee->full_name }}</div>
                                    <small class="text-muted">{{ $employee->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>{{ $employee->job_title }}</td>
                        <td>
                            <span class="badge badge-info">
                                {{ trans('messages.' . $departments[$employee->department] ?? $employee->department) }}
                            </span>
                        </td>
                        <td>{{ $employee->branch->name ?? trans('messages.No Branch') }}</td>
                        <td>
                            <span class="badge {{ $employee->employment_status === 'active' ? 'badge-success' : 
                                                   ($employee->employment_status === 'inactive' ? 'badge-warning' : 'badge-danger') }}">
                                {{ trans('messages.' . $statuses[$employee->employment_status]) }}
                            </span>
                        </td>
                        <td>{{ $employee->hire_date->format('Y-m-d') }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                @can('employees.view')
                                <a href="{{ route('employees.show', $employee) }}" 
                                   class="btn btn-sm btn-info" title="{{ trans('messages.View Details') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                
                                @can('employees.edit')
                                <a href="{{ route('employees.edit', $employee) }}" 
                                   class="btn btn-sm btn-warning" title="{{ trans('messages.Edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan
                                
                                @can('payrolls.create')
                                <a href="{{ route('payrolls.create', ['employee_id' => $employee->id]) }}" 
                                   class="btn btn-sm btn-success" title="{{ trans('messages.Create Payroll') }}">
                                    <i class="fas fa-money-bill"></i>
                                </a>
                                @endcan
                                
                                @can('employees.delete')
                                <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('{{ trans('messages.Are you sure you want to delete this employee?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="{{ trans('messages.Delete') }}">
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
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                {{ trans('messages.Showing') }} {{ $employees->firstItem() ?? 0 }} {{ trans('messages.to') }} 
                {{ $employees->lastItem() ?? 0 }} {{ trans('messages.of') }} {{ $employees->total() }} {{ trans('messages.results') }}
            </div>
            <div>
                {{ $employees->links('pagination.custom') }}
            </div>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
            <h5 class="text-gray-600">{{ trans('messages.No employees found') }}</h5>
            <p class="text-muted">{{ trans('messages.Try adjusting your search criteria or add a new employee') }}</p>
            @can('employees.create')
            <a href="{{ route('employees.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>{{ trans('messages.Add First Employee') }}
            </a>
            @endcan
        </div>
        @endif
    </div>
</div>
@endsection
