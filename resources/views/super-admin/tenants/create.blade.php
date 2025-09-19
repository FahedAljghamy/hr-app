@extends('super-admin.layout')

@section('title', __('Add New Tenant'))

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">{{ __('Add New Tenant') }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('super-admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('super-admin.tenants.index') }}">{{ __('Tenants') }}</a></li>
                    <li class="breadcrumb-item active">{{ __('Add New') }}</li>
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
                    <form action="{{ route('super-admin.tenants.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_name">{{ __('Company Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('company_name') is-invalid @enderror" 
                                               id="company_name" name="company_name" value="{{ old('company_name') }}" required>
                                        @error('company_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{ __('Tenant Name') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name') }}" required>
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
                                                   id="domain" name="domain" value="{{ old('domain') }}" required>
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
                                               id="subdomain" name="subdomain" value="{{ old('subdomain') }}">
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
                                               id="contact_email" name="contact_email" value="{{ old('contact_email') }}" required>
                                        @error('contact_email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact_phone">{{ __('Contact Phone') }}</label>
                                        <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" 
                                               id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}">
                                        @error('contact_phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="address">{{ __('Address') }}</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3">{{ old('address') }}</textarea>
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
                                            <option value="basic" {{ old('subscription_plan') == 'basic' ? 'selected' : '' }}>{{ __('Basic') }}</option>
                                            <option value="premium" {{ old('subscription_plan') == 'premium' ? 'selected' : '' }}>{{ __('Premium') }}</option>
                                            <option value="enterprise" {{ old('subscription_plan') == 'enterprise' ? 'selected' : '' }}>{{ __('Enterprise') }}</option>
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
                                               id="max_employees" name="max_employees" value="{{ old('max_employees', 50) }}" min="1" required>
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
                                                   id="monthly_fee" name="monthly_fee" value="{{ old('monthly_fee', 0) }}" step="0.01" min="0" required>
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
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="subscription_start_date">{{ __('Subscription Start Date') }} <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('subscription_start_date') is-invalid @enderror" 
                                               id="subscription_start_date" name="subscription_start_date" 
                                               value="{{ old('subscription_start_date', now()->format('Y-m-d')) }}" required>
                                        @error('subscription_start_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="subscription_end_date">{{ __('Subscription End Date') }} <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('subscription_end_date') is-invalid @enderror" 
                                               id="subscription_end_date" name="subscription_end_date" 
                                               value="{{ old('subscription_end_date', now()->addYear()->format('Y-m-d')) }}" required>
                                        @error('subscription_end_date')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="logo">{{ __('Company Logo') }}</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input @error('logo') is-invalid @enderror" 
                                           id="logo" name="logo" accept="image/*">
                                    <label class="custom-file-label" for="logo">{{ __('Choose file') }}</label>
                                </div>
                                @error('logo')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('Create Tenant') }}
                            </button>
                            <a href="{{ route('super-admin.tenants.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> {{ __('Cancel') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Admin Account') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="admin_name">{{ __('Admin Name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('admin_name') is-invalid @enderror" 
                                   id="admin_name" name="admin_name" value="{{ old('admin_name') }}" required>
                            @error('admin_name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="admin_email">{{ __('Admin Email') }} <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('admin_email') is-invalid @enderror" 
                                   id="admin_email" name="admin_email" value="{{ old('admin_email') }}" required>
                            @error('admin_email')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="admin_password">{{ __('Admin Password') }} <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('admin_password') is-invalid @enderror" 
                                   id="admin_password" name="admin_password" required>
                            @error('admin_password')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="admin_password_confirmation">{{ __('Confirm Password') }} <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="admin_password_confirmation" name="admin_password_confirmation" required>
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
    // Auto-generate domain from company name
    $('#company_name').on('blur', function() {
        if (!$('#domain').val()) {
            const companyName = $(this).val().toLowerCase()
                .replace(/[^a-z0-9\s]/g, '')
                .replace(/\s+/g, '-')
                .substring(0, 20);
            $('#domain').val(companyName);
        }
    });

    // Auto-generate admin email from company name
    $('#company_name').on('blur', function() {
        if (!$('#admin_email').val()) {
            const companyName = $(this).val().toLowerCase()
                .replace(/[^a-z0-9\s]/g, '')
                .replace(/\s+/g, '.')
                .substring(0, 15);
            $('#admin_email').val('admin@' + companyName + '.com');
        }
    });

    // Update subscription end date when start date changes
    $('#subscription_start_date').on('change', function() {
        const startDate = new Date($(this).val());
        const endDate = new Date(startDate.getFullYear() + 1, startDate.getMonth(), startDate.getDate());
        $('#subscription_end_date').val(endDate.toISOString().split('T')[0]);
    });
});
</script>
@endsection

