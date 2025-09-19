@extends('super-admin.layout')

@section('title', __('Tenant Details') . ': ' . $tenant->company_name)

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ __('Tenant Details') }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('super-admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('super-admin.tenants.index') }}">{{ __('Tenants') }}</a></li>
                    <li class="breadcrumb-item active">{{ $tenant->company_name }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <!-- Company Information -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Company Information') }}</h3>
                        <div class="card-tools">
                            <a href="{{ route('super-admin.tenants.edit', $tenant) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> {{ __('Edit') }}
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                @if($tenant->logo)
                                    <img src="{{ asset('storage/' . $tenant->logo) }}" alt="{{ $tenant->company_name }}" class="img-fluid rounded">
                                @else
                                    <div class="bg-primary rounded d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="fas fa-building fa-4x text-white"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-8">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>{{ __('Company Name') }}:</strong></td>
                                        <td>{{ $tenant->company_name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Tenant Name') }}:</strong></td>
                                        <td>{{ $tenant->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Domain') }}:</strong></td>
                                        <td>
                                            <a href="http://{{ $tenant->domain }}" target="_blank" class="text-primary">
                                                {{ $tenant->domain }}
                                            </a>
                                        </td>
                                    </tr>
                                    @if($tenant->subdomain)
                                    <tr>
                                        <td><strong>{{ __('Subdomain') }}:</strong></td>
                                        <td>{{ $tenant->subdomain }}</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td><strong>{{ __('Contact Email') }}:</strong></td>
                                        <td>{{ $tenant->contact_email }}</td>
                                    </tr>
                                    @if($tenant->contact_phone)
                                    <tr>
                                        <td><strong>{{ __('Contact Phone') }}:</strong></td>
                                        <td>{{ $tenant->contact_phone }}</td>
                                    </tr>
                                    @endif
                                    @if($tenant->address)
                                    <tr>
                                        <td><strong>{{ __('Address') }}:</strong></td>
                                        <td>{{ $tenant->address }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subscription Information -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Subscription Information') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>{{ __('Plan') }}:</strong></td>
                                        <td>
                                            <span class="badge badge-{{ $tenant->subscription_plan == 'enterprise' ? 'success' : ($tenant->subscription_plan == 'premium' ? 'warning' : 'info') }}">
                                                {{ ucfirst($tenant->subscription_plan) }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Monthly Fee') }}:</strong></td>
                                        <td>${{ number_format($tenant->monthly_fee, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Max Employees') }}:</strong></td>
                                        <td>{{ $tenant->max_employees }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td><strong>{{ __('Start Date') }}:</strong></td>
                                        <td>{{ $tenant->subscription_start_date->format('M d, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('End Date') }}:</strong></td>
                                        <td>
                                            @if($tenant->subscription_end_date < now())
                                                <span class="text-danger">
                                                    <i class="fas fa-exclamation-triangle"></i> {{ __('Expired') }}
                                                </span>
                                            @elseif($tenant->subscription_end_date < now()->addDays(7))
                                                <span class="text-warning">
                                                    <i class="fas fa-clock"></i> {{ $tenant->subscription_end_date->format('M d, Y') }}
                                                </span>
                                            @else
                                                <span class="text-success">{{ $tenant->subscription_end_date->format('M d, Y') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ __('Days Remaining') }}:</strong></td>
                                        <td>
                                            @if($tenant->subscription_end_date < now())
                                                <span class="text-danger">{{ __('Expired') }}</span>
                                            @else
                                                <span class="text-success">{{ $tenant->subscription_end_date->diffInDays(now()) }} {{ __('days') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Status Card -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Status') }}</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            @if($tenant->status == 'active')
                                <i class="fas fa-check-circle fa-4x text-success"></i>
                            @elseif($tenant->status == 'inactive')
                                <i class="fas fa-pause-circle fa-4x text-secondary"></i>
                            @else
                                <i class="fas fa-ban fa-4x text-danger"></i>
                            @endif
                        </div>
                        <h4>
                            <span class="badge badge-{{ $tenant->status == 'active' ? 'success' : ($tenant->status == 'inactive' ? 'secondary' : 'danger') }} badge-lg">
                                {{ ucfirst($tenant->status) }}
                            </span>
                        </h4>
                        <p class="text-muted">{{ __('Current tenant status') }}</p>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Statistics') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-info">
                                        <i class="fas fa-users"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">{{ __('Employees') }}</span>
                                        <span class="info-box-number">{{ $tenant->users_count ?? 0 }}</span>
                                        <div class="progress">
                                            <div class="progress-bar" style="width: {{ ($tenant->users_count ?? 0) / $tenant->max_employees * 100 }}%"></div>
                                        </div>
                                        <span class="progress-description">
                                            {{ ($tenant->users_count ?? 0) }} / {{ $tenant->max_employees }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="info-box">
                                    <span class="info-box-icon bg-success">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">{{ __('Days Left') }}</span>
                                        <span class="info-box-number">
                                            @if($tenant->subscription_end_date < now())
                                                0
                                            @else
                                                {{ $tenant->subscription_end_date->diffInDays(now()) }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Quick Actions') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('super-admin.tenants.edit', $tenant) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> {{ __('Edit Tenant') }}
                            </a>
                            @if($tenant->status == 'active')
                                <form action="{{ route('super-admin.tenants.suspend', $tenant) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-warning btn-block" onclick="return confirm('{{ __('Are you sure you want to suspend this tenant?') }}')">
                                        <i class="fas fa-pause"></i> {{ __('Suspend') }}
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('super-admin.tenants.activate', $tenant) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-block">
                                        <i class="fas fa-play"></i> {{ __('Activate') }}
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('super-admin.tenants.destroy', $tenant) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this tenant? This action cannot be undone.') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-block">
                                    <i class="fas fa-trash"></i> {{ __('Delete') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users List -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Users') }}</h3>
                    </div>
                    <div class="card-body">
                        @if($tenant->users && $tenant->users->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Email') }}</th>
                                            <th>{{ __('Type') }}</th>
                                            <th>{{ __('Created') }}</th>
                                            <th>{{ __('Last Login') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tenant->users as $user)
                                        <tr>
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <span class="badge badge-{{ $user->user_type == 'tenant_admin' ? 'warning' : 'info' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $user->user_type)) }}
                                                </span>
                                            </td>
                                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                                            <td>{{ $user->updated_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-users fa-3x mb-3"></i>
                                <br>
                                {{ __('No users found for this tenant') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

