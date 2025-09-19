{{-- 
Author: Eng.Fahed
Company Settings Index View - HR System
عرض إعدادات الشركة مع إمكانية التعديل
--}}

@extends('layouts.app')

@section('title', trans('messages.Company Settings'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Company Settings') }}</h1>
        <p class="text-muted">{{ trans('messages.Manage your company information and settings') }}</p>
    </div>
    @if(!$setting)
    <a href="{{ route('company-settings.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> {{ trans('messages.Setup Company Settings') }}
    </a>
    @else
    <a href="{{ route('company-settings.edit', $setting) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm">
        <i class="fas fa-edit fa-sm text-white-50"></i> {{ trans('messages.Edit Settings') }}
    </a>
    @endif
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

@if($setting)
<!-- Company Information Cards -->
<div class="row">
    <!-- Basic Information -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-building mr-2"></i>{{ trans('messages.Company Information') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">{{ trans('messages.Company Name') }}:</label>
                        <p class="text-gray-800">{{ $setting->company_name }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">{{ trans('messages.Official Email') }}:</label>
                        <p class="text-gray-800">{{ $setting->email }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">{{ trans('messages.Phone Number') }}:</label>
                        <p class="text-gray-800">{{ $setting->phone ?: trans('messages.Not specified') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold">{{ trans('messages.Website') }}:</label>
                        <p class="text-gray-800">
                            @if($setting->website)
                                <a href="{{ $setting->website }}" target="_blank">{{ $setting->website }}</a>
                            @else
                                {{ trans('messages.Not specified') }}
                            @endif
                        </p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label font-weight-bold">{{ trans('messages.Official Address') }}:</label>
                        <p class="text-gray-800">{{ $setting->address }}</p>
                    </div>
                    @if($setting->description)
                    <div class="col-12 mb-3">
                        <label class="form-label font-weight-bold">{{ trans('messages.Company Description') }}:</label>
                        <p class="text-gray-800">{{ $setting->description }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Logo and Additional Info -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-image mr-2"></i>{{ trans('messages.Company Logo') }}
                </h6>
            </div>
            <div class="card-body text-center">
                <img src="{{ $setting->logo_url }}" alt="{{ $setting->company_name }}" 
                     class="img-fluid mb-3" style="max-height: 150px;">
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-cog mr-2"></i>{{ trans('messages.System Settings') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <strong>{{ trans('messages.Timezone') }}:</strong>
                    <span class="text-gray-800">{{ $setting->timezone }}</span>
                </div>
                <div class="mb-2">
                    <strong>{{ trans('messages.Currency') }}:</strong>
                    <span class="text-gray-800">{{ $setting->currency }}</span>
                </div>
                <div class="mb-2">
                    <strong>{{ trans('messages.Official Working Hours') }}:</strong>
                    <span class="text-gray-800">{{ $setting->formatted_working_hours }}</span>
                </div>
                @if($setting->tax_number)
                <div class="mb-2">
                    <strong>{{ trans('messages.Tax Number') }}:</strong>
                    <span class="text-gray-800">{{ $setting->tax_number }}</span>
                </div>
                @endif
                @if($setting->registration_number)
                <div class="mb-2">
                    <strong>{{ trans('messages.Registration Number') }}:</strong>
                    <span class="text-gray-800">{{ $setting->registration_number }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($setting->social_media && count($setting->social_media) > 0)
<!-- Social Media -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-warning">
            <i class="fas fa-share-alt mr-2"></i>{{ trans('messages.Social Media') }}
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($setting->social_media as $platform => $url)
            @if($url)
            <div class="col-md-3 mb-2">
                <a href="{{ $url }}" target="_blank" class="btn btn-outline-primary btn-block">
                    <i class="fab fa-{{ $platform }} mr-2"></i>{{ ucfirst($platform) }}
                </a>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>
@endif

@else
<!-- No Settings State -->
<div class="card shadow">
    <div class="card-body text-center py-5">
        <div class="mb-4">
            <i class="fas fa-building fa-4x text-gray-300"></i>
        </div>
        <h4 class="text-gray-600 mb-3">{{ trans('messages.No Company Settings') }}</h4>
        <p class="text-muted mb-4">{{ trans('messages.Setup your company information to get started') }}</p>
        @can('company.settings.edit')
        <a href="{{ route('company-settings.create') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-plus mr-2"></i>{{ trans('messages.Setup Company Settings') }}
        </a>
        @endcan
    </div>
</div>
@endif

@endsection
