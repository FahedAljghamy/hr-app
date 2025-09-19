{{-- 
Author: Eng.Fahed
Company Settings Show View - HR System
عرض تفاصيل إعدادات الشركة
--}}

@extends('layouts.app')

@section('title', trans('messages.Company Settings Details'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Company Settings Details') }}</h1>
        <p class="text-muted">{{ trans('messages.View your company information and settings') }}</p>
    </div>
    <div>
        @can('company.settings.edit')
        <a href="{{ route('company-settings.edit', $companySetting) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm mr-2">
            <i class="fas fa-edit fa-sm text-white-50"></i> {{ trans('messages.Edit Settings') }}
        </a>
        @endcan
        <a href="{{ route('company-settings.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Back to Settings') }}
        </a>
    </div>
</div>

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
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Company Name') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $companySetting->company_name }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Official Email') }}:</label>
                        <p class="text-gray-800 mb-0">
                            <a href="mailto:{{ $companySetting->email }}">{{ $companySetting->email }}</a>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Phone Number') }}:</label>
                        <p class="text-gray-800 mb-0">
                            @if($companySetting->phone)
                                <a href="tel:{{ $companySetting->phone }}">{{ $companySetting->phone }}</a>
                            @else
                                <span class="text-muted">{{ trans('messages.Not specified') }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Website') }}:</label>
                        <p class="text-gray-800 mb-0">
                            @if($companySetting->website)
                                <a href="{{ $companySetting->website }}" target="_blank">{{ $companySetting->website }}</a>
                            @else
                                <span class="text-muted">{{ trans('messages.Not specified') }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Official Address') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $companySetting->address }}</p>
                    </div>
                    @if($companySetting->description)
                    <div class="col-12 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Company Description') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $companySetting->description }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Logo and System Settings -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-image mr-2"></i>{{ trans('messages.Company Logo') }}
                </h6>
            </div>
            <div class="card-body text-center">
                <img src="{{ $companySetting->logo_url }}" alt="{{ $companySetting->company_name }}" 
                     class="img-fluid mb-3" style="max-height: 150px;">
                <p class="text-muted small">{{ trans('messages.Company Logo') }}</p>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-cog mr-2"></i>{{ trans('messages.System Settings') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Timezone') }}:</label>
                    <p class="text-gray-800 mb-0">{{ $companySetting->timezone }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Currency') }}:</label>
                    <p class="text-gray-800 mb-0">{{ $companySetting->currency }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Official Working Hours') }}:</label>
                    <p class="text-gray-800 mb-0">{{ $companySetting->formatted_working_hours }}</p>
                </div>
                @if($companySetting->tax_number)
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Tax Number') }}:</label>
                    <p class="text-gray-800 mb-0">{{ $companySetting->tax_number }}</p>
                </div>
                @endif
                @if($companySetting->registration_number)
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Registration Number') }}:</label>
                    <p class="text-gray-800 mb-0">{{ $companySetting->registration_number }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if($companySetting->social_media && count($companySetting->social_media) > 0)
<!-- Social Media -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-warning">
            <i class="fas fa-share-alt mr-2"></i>{{ trans('messages.Social Media') }}
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            @foreach($companySetting->social_media as $platform => $url)
            @if($url)
            <div class="col-md-3 mb-3">
                <div class="card border-left-primary">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <div class="icon-circle bg-primary text-white mr-3">
                                <i class="fab fa-{{ $platform }}"></i>
                            </div>
                            <div>
                                <div class="font-weight-bold">{{ ucfirst($platform) }}</div>
                                <a href="{{ $url }}" target="_blank" class="text-primary small">{{ trans('messages.Visit Page') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>
@endif

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
                @can('company.settings.edit')
                <a href="{{ route('company-settings.edit', $companySetting) }}" class="btn btn-warning btn-lg mr-3">
                    <i class="fas fa-edit mr-2"></i>{{ trans('messages.Edit Settings') }}
                </a>
                @endcan
                
                <a href="{{ route('branches.index') }}" class="btn btn-info btn-lg mr-3">
                    <i class="fas fa-building mr-2"></i>{{ trans('messages.Manage Branches') }}
                </a>
                
                <a href="{{ route('company-settings.index') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-arrow-left mr-2"></i>{{ trans('messages.Back to Settings') }}
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
