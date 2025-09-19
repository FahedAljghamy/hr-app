@extends('super-admin.layout')

@section('title', trans('messages.Super Admin Dashboard'))

@section('breadcrumb')
<li class="breadcrumb-item active">{{ trans('messages.Dashboard') }}</li>
@endsection

@section('content')
<!-- Info boxes -->
<div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-building"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{ trans('messages.Total Tenants') }}</span>
                <span class="info-box-number">{{ $stats['total_tenants'] }}</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{ trans('messages.Active Tenants') }}</span>
                <span class="info-box-number">{{ $stats['active_tenants'] }}</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{ trans('messages.Total Users') }}</span>
                <span class="info-box-number">{{ $stats['total_users'] }}</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-dollar-sign"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{ trans('messages.Monthly Revenue') }}</span>
                <span class="info-box-number">${{ number_format($stats['subscription_revenue'], 2) }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Additional Statistics Row -->
<div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-secondary elevation-1"><i class="fas fa-ban"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{ trans('messages.Suspended Tenants') }}</span>
                <span class="info-box-number">{{ $stats['suspended_tenants'] }}</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-user-tie"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{ trans('messages.Total Employees') }}</span>
                <span class="info-box-number">{{ $stats['total_employees'] }}</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-dark elevation-1"><i class="fas fa-user-shield"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{ trans('messages.Tenant Admins') }}</span>
                <span class="info-box-number">{{ $stats['total_admins'] }}</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-orange elevation-1"><i class="fas fa-exclamation-triangle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{ trans('messages.Expiring Soon') }}</span>
                <span class="info-box-number">{{ $stats['expiring_soon'] }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <section class="col-lg-7 connectedSortable">
        <!-- Recent Tenants -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-building mr-1"></i>
                    {{ trans('messages.Recent Tenants') }}
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{ trans('messages.Company') }}</th>
                                <th>{{ trans('messages.Plan') }}</th>
                                <th>{{ trans('messages.Status') }}</th>
                                <th>{{ trans('messages.Created') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTenants as $tenant)
                            <tr>
                                <td>
                                    <strong>{{ $tenant->company_name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $tenant->domain }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $tenant->subscription_plan == 'enterprise' ? 'success' : ($tenant->subscription_plan == 'premium' ? 'warning' : 'info') }}">
                                        {{ ucfirst($tenant->subscription_plan) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $tenant->status == 'active' ? 'success' : ($tenant->status == 'inactive' ? 'secondary' : 'danger') }}">
                                        {{ ucfirst($tenant->status) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $tenant->created_at->format('M d, Y') }}
                                    <br>
                                    <small class="text-muted">{{ $tenant->users_count ?? 0 }} {{ trans('messages.users') }}</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">{{ trans('messages.No tenants found') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- Right col -->
    <section class="col-lg-5 connectedSortable">
        <!-- Subscription Plans Chart -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-pie mr-1"></i>
                    {{ trans('messages.Tenants by Plan') }}
                </h3>
            </div>
            <div class="card-body">
                @if(array_sum($tenantsByPlan) > 0)
                <div class="row">
                    @foreach($tenantsByPlan as $planType => $count)
                    <div class="col-md-4">
                        <div class="description-block border-right">
                            <span class="description-percentage text-{{ $planType == 'enterprise' ? 'success' : ($planType == 'premium' ? 'warning' : 'info') }}">
                                {{ $count }} {{ trans('messages.tenants') }}
                            </span>
                            <h5 class="description-header">{{ ucfirst($planType) }}</h5>
                            <span class="description-text">{{ trans('messages.Plan') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-center">{{ trans('messages.No data available') }}</p>
                @endif
            </div>
        </div>

        <!-- Expiring Subscriptions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    {{ trans('messages.Expiring Soon') }}
                </h3>
            </div>
            <div class="card-body">
                @if(isset($expiringSubscriptions) && $expiringSubscriptions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>{{ trans('messages.Company') }}</th>
                                    <th>{{ trans('messages.Days Left') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expiringSubscriptions->take(5) as $tenant)
                                <tr>
                                    <td>
                                        <strong>{{ $tenant->company_name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $tenant->subscription_plan }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $daysLeft = $tenant->subscription_end_date->diffInDays(now());
                                        @endphp
                                        <span class="badge badge-{{ $daysLeft <= 7 ? 'danger' : ($daysLeft <= 30 ? 'warning' : 'success') }}">
                                            {{ $daysLeft }} {{ trans('messages.days') }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-muted">{{ trans('messages.No expiring subscriptions') }}</p>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt mr-1"></i>
                    {{ trans('messages.Quick Actions') }}
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <a href="{{ route('super-admin.tenants.create') }}" class="btn btn-primary btn-block mb-2">
                            <i class="fas fa-plus mr-2"></i>{{ trans('messages.Add New Tenant') }}
                        </a>
                    </div>
                    <div class="col-12">
                        <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-info btn-block mb-2">
                            <i class="fas fa-list mr-2"></i>{{ trans('messages.View All Tenants') }}
                        </a>
                    </div>
                    <div class="col-12">
                        <a href="#" class="btn btn-success btn-block">
                            <i class="fas fa-download mr-2"></i>{{ trans('messages.Export Reports') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
