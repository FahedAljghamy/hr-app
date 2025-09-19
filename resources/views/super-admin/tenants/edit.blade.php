@extends('super-admin.layout')

@section('title', __('Edit Tenant') . ': ' . $tenant->company_name)

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ __('Edit Tenant') }}: {{ $tenant->company_name }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('super-admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('super-admin.tenants.index') }}">{{ __('Tenants') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Edit') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Tenant Information') }}</h3>
                    </div>
                    <form action="{{ route('super-admin.tenants.update', $tenant) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_name">{{ __('Company Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                               id="company_name" name="company_name" value="{{ old('company_name', $tenant->company_name) }}" required>
                                        @error('company_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{ __('Tenant Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $tenant->name) }}" required>
                                        @error('name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="domain">{{ __('Domain') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control @error('domain') is-invalid @enderror" 
                                                   id="domain" name="domain" value="{{ old('domain', $tenant->domain) }}" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">.hrsystem.com</span>
                                            </div>
                                        </div>
                                        @error('domain')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="subdomain">{{ __('Subdomain') }}</label>
                                        <input type="text" class="form-control @error('subdomain') is-invalid @enderror" 
                                               id="subdomain" name="subdomain" value="{{ old('subdomain', $tenant->subdomain) }}">
                                        @error('subdomain')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact_email">{{ __('Contact Email') }} <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('contact_email') is-invalid @enderror" 
                                               id="contact_email" name="contact_email" value="{{ old('contact_email', $tenant->contact_email) }}" required>
                                        @error('contact_email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact_phone">{{ __('Contact Phone') }}</label>
                                        <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" 
                                               id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $tenant->contact_phone) }}">
                                        @error('contact_phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="address">{{ __('Address') }}</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3">{{ old('address', $tenant->address) }}</textarea>
                                @error('address')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="subscription_plan">{{ __('Subscription Plan') }} <span class="text-danger">*</span></label>
                                        <select class="form-control @error('subscription_plan') is-invalid @enderror" 
                                                id="subscription_plan" name="subscription_plan" required>
                                            <option value="basic" {{ old('subscription_plan', $tenant->subscription_plan) == 'basic' ? 'selected' : '' }}>{{ __('Basic') }}</option>
                                            <option value="premium" {{ old('subscription_plan', $tenant->subscription_plan) == 'premium' ? 'selected' : '' }}>{{ __('Premium') }}</option>
                                            <option value="enterprise" {{ old('subscription_plan', $tenant->subscription_plan) == 'enterprise' ? 'selected' : '' }}>{{ __('Enterprise') }}</option>
                                        </select>
                                        @error('subscription_plan')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="max_employees">{{ __('Max Employees') }} <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('max_employees') is-invalid @enderror" 
                                               id="max_employees" name="max_employees" value="{{ old('max_employees', $tenant->max_employees) }}" min="1" required>
                                        @error('max_employees')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="monthly_fee">{{ __('Monthly Fee') }} <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="number" class="form-control @error('monthly_fee') is-invalid @enderror" 
                                                   id="monthly_fee" name="monthly_fee" value="{{ old('monthly_fee', $tenant->monthly_fee) }}" step="0.01" min="0" required>
                                            <div class="input-group-append">
                                                <span class="input-group-text">$</span>
                                            </div>
                                        </div>
                                        @error('monthly_fee')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="status">{{ __('Status') }} <span class="text-danger">*</span></label>
                                        <select class="form-control @error('status') is-invalid @enderror" 
                                                id="status" name="status" required>
                                            <option value="active" {{ old('status', $tenant->status) == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                            <option value="inactive" {{ old('status', $tenant->status) == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                            <option value="suspended" {{ old('status', $tenant->status) == 'suspended' ? 'selected' : '' }}>{{ __('Suspended') }}</option>
                                        </select>
                                        @error('status')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="subscription_start_date">{{ __('Subscription Start Date') }} <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('subscription_start_date') is-invalid @enderror" 
                                               id="subscription_start_date" name="subscription_start_date" 
                                               value="{{ old('subscription_start_date', $tenant->subscription_start_date->format('Y-m-d')) }}" required>
                                        @error('subscription_start_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="subscription_end_date">{{ __('Subscription End Date') }} <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('subscription_end_date') is-invalid @enderror" 
                                               id="subscription_end_date" name="subscription_end_date" 
                                               value="{{ old('subscription_end_date', $tenant->subscription_end_date->format('Y-m-d')) }}" required>
                                        @error('subscription_end_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="logo">{{ __('Company Logo') }}</label>
                                @if($tenant->logo)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $tenant->logo) }}" alt="{{ $tenant->company_name }}" class="img-thumbnail" style="max-height: 100px;">
                                        <br>
                                        <small class="text-muted">{{ __('Current logo') }}</small>
                                    </div>
                                @endif
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('logo') is-invalid @enderror" 
                                           id="logo" name="logo" accept="image/*">
                                    <label class="custom-file-label" for="logo">{{ __('Choose new logo') }}</label>
                                </div>
                                @error('logo')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('Update Tenant') }}
                            </button>
                            <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> {{ __('Cancel') }}
                            </a>
                            <a href="{{ route('super-admin.tenants.show', $tenant) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> {{ __('View') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Tenant Statistics') }}</h3>
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
                                        <span class="info-box-number">{{ $tenant->subscription_end_date->diffInDays(now()) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Update subscription end date when start date changes
    $('#subscription_start_date').on('change', function() {
        const startDate = new Date($(this).val());
        const endDate = new Date(startDate.getFullYear() + 1, startDate.getMonth(), startDate.getDate());
        $('#subscription_end_date').val(endDate.toISOString().split('T')[0]);
    });
});
</script>
@endsection

