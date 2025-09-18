@extends('super-admin.layout')

@section('title', __('Super Admin Dashboard'))

@section('breadcrumb')
<li class="breadcrumb-item active">{{ __('Dashboard') }}</li>
@endsection

@section('content')
<!-- Info boxes -->
<div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-building"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{ __('Total Tenants') }}</span>
                <span class="info-box-number">{{ $stats['total_tenants'] }}</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{ __('Active Tenants') }}</span>
                <span class="info-box-number">{{ $stats['active_tenants'] }}</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{ __('Total Users') }}</span>
                <span class="info-box-number">{{ $stats['total_users'] }}</span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box mb-3">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-dollar-sign"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">{{ __('Monthly Revenue') }}</span>
                <span class="info-box-number">${{ number_format($stats['subscription_revenue'], 2) }}</span>
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
                    {{ __('Recent Tenants') }}
                </h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>{{ __('Company') }}</th>
                                <th>{{ __('Plan') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Created') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTenants as $tenant)
                            <tr>
                                <td>{{ $tenant->company_name }}</td>
                                <td>
                                    <span class="badge badge-{{ $tenant->subscription_plan == 'enterprise' ? 'danger' : ($tenant->subscription_plan == 'premium' ? 'warning' : 'info') }}">
                                        {{ ucfirst($tenant->subscription_plan) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $tenant->status == 'active' ? 'success' : 'danger' }}">
                                        {{ ucfirst($tenant->status) }}
                                    </span>
                                </td>
                                <td>{{ $tenant->created_at->format('M d, Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">{{ __('No tenants found') }}</td>
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
                    {{ __('Tenants by Plan') }}
                </h3>
            </div>
            <div class="card-body">
                @if($tenantsByPlan->count() > 0)
                <div class="row">
                    @foreach($tenantsByPlan as $plan)
                    <div class="col-md-6">
                        <div class="description-block border-right">
                            <span class="description-percentage text-{{ $plan->subscription_plan == 'enterprise' ? 'danger' : ($plan->subscription_plan == 'premium' ? 'warning' : 'info') }}">
                                {{ $plan->count }} {{ __('tenants') }}
                            </span>
                            <h5 class="description-header">{{ ucfirst($plan->subscription_plan) }}</h5>
                            <span class="description-text">{{ __('Plan') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-center">{{ __('No data available') }}</p>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-bolt mr-1"></i>
                    {{ __('Quick Actions') }}
                </h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <a href="{{ route('super-admin.tenants.create') }}" class="btn btn-primary btn-block mb-2">
                            <i class="fas fa-plus mr-2"></i>{{ __('Add New Tenant') }}
                        </a>
                    </div>
                    <div class="col-12">
                        <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-info btn-block mb-2">
                            <i class="fas fa-list mr-2"></i>{{ __('View All Tenants') }}
                        </a>
                    </div>
                    <div class="col-12">
                        <a href="#" class="btn btn-success btn-block">
                            <i class="fas fa-download mr-2"></i>{{ __('Export Reports') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
