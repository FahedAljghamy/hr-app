{{-- 
Author: Eng.Fahed
Branch Show View - HR System
عرض تفاصيل الفرع
--}}

@extends('layouts.app')

@section('title', trans('messages.Branch Details') . ': ' . $branch->name)

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Branch Details') }}</h1>
        <p class="text-muted">{{ trans('messages.View detailed information about') }} "{{ $branch->name }}"</p>
    </div>
    <div>
        @can('branches.edit')
        <a href="{{ route('branches.edit', $branch) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm mr-2">
            <i class="fas fa-edit fa-sm text-white-50"></i> {{ trans('messages.Edit Branch') }}
        </a>
        @endcan
        <a href="{{ route('branches.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Back to List') }}
        </a>
    </div>
</div>

<!-- Branch Information Cards -->
<div class="row">
    <!-- Basic Information -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-building mr-2"></i>{{ trans('messages.Branch Information') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Branch Name') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $branch->name }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Manager Name') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $branch->manager_name ?: trans('messages.Not specified') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Phone Number') }}:</label>
                        <p class="text-gray-800 mb-0">
                            @if($branch->phone)
                                <a href="tel:{{ $branch->phone }}">{{ $branch->phone }}</a>
                            @else
                                <span class="text-muted">{{ trans('messages.Not specified') }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Email Address') }}:</label>
                        <p class="text-gray-800 mb-0">
                            @if($branch->email)
                                <a href="mailto:{{ $branch->email }}">{{ $branch->email }}</a>
                            @else
                                <span class="text-muted">{{ trans('messages.Not specified') }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Branch Address') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $branch->address }}</p>
                    </div>
                    @if($branch->location)
                    <div class="col-12 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Branch Location') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $branch->location }}</p>
                    </div>
                    @endif
                    @if($branch->description)
                    <div class="col-12 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Branch Description') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $branch->description }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Status and Working Hours -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-info-circle mr-2"></i>{{ trans('messages.Branch Status') }}
                </h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <span class="badge {{ $branch->status_badge_class }} badge-lg p-3">
                        <i class="fas fa-{{ $branch->is_active ? 'check-circle' : 'times-circle' }} mr-2"></i>
                        {{ trans('messages.' . $branch->status_text) }}
                    </span>
                </div>
                <p class="text-muted">{{ trans('messages.Current branch status') }}</p>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-clock mr-2"></i>{{ trans('messages.Working Hours') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="text-center">
                    <h4 class="text-gray-800">{{ $branch->formatted_working_hours }}</h4>
                    <p class="text-muted small">{{ trans('messages.Daily working schedule') }}</p>
                </div>
                
                @if($branch->working_hours)
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-right">
                            <h6 class="text-success">{{ $branch->working_hours['start'] ?? 'N/A' }}</h6>
                            <small class="text-muted">{{ trans('messages.Start Time') }}</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h6 class="text-danger">{{ $branch->working_hours['end'] ?? 'N/A' }}</h6>
                        <small class="text-muted">{{ trans('messages.End Time') }}</small>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Branch Statistics -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-chart-bar mr-2"></i>{{ trans('messages.Branch Statistics') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-12 mb-3">
                        <h4 class="text-primary">{{ $branch->created_at->diffForHumans() }}</h4>
                        <small class="text-muted">{{ trans('messages.Branch Age') }}</small>
                    </div>
                </div>
                
                <hr>
                
                <div class="small text-muted">
                    <div class="d-flex justify-content-between mb-2">
                        <span>{{ trans('messages.Created') }}:</span>
                        <span>{{ $branch->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>{{ trans('messages.Last Updated') }}:</span>
                        <span>{{ $branch->updated_at->format('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actions -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-secondary">
                    <i class="fas fa-tools mr-2"></i>{{ trans('messages.Available Actions') }}
                </h6>
            </div>
            <div class="card-body text-center">
                @can('branches.edit')
                <a href="{{ route('branches.edit', $branch) }}" class="btn btn-warning btn-lg mr-3">
                    <i class="fas fa-edit mr-2"></i>{{ trans('messages.Edit Branch') }}
                </a>
                @endcan
                
                @can('branches.delete')
                <form action="{{ route('branches.destroy', $branch) }}" method="POST" class="d-inline mr-3"
                      onsubmit="return confirm('{{ trans('messages.Are you sure you want to delete this branch?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-lg">
                        <i class="fas fa-trash mr-2"></i>{{ trans('messages.Delete Branch') }}
                    </button>
                </form>
                @endcan
                
                <a href="{{ route('branches.index') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-arrow-left mr-2"></i>{{ trans('messages.Back to List') }}
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
