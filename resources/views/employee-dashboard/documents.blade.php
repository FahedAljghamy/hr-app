{{-- 
Author: Eng.Fahed
Employee Dashboard Documents - HR System
مستندات الموظف
--}}

@extends('layouts.app')

@section('title', trans('messages.My Documents'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.My Documents') }}</h1>
        <p class="text-muted">{{ trans('messages.View your personal documents and expiry dates') }}</p>
    </div>
    <a href="{{ route('employee-dashboard.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Back to Dashboard') }}
    </a>
</div>

<!-- Document Status Overview -->
<div class="row mb-4">
    @php
        $validDocs = 0;
        $expiringDocs = 0;
        $expiredDocs = 0;
        
        foreach($documents as $doc) {
            if($doc['status'] === 'valid') $validDocs++;
            elseif($doc['status'] === 'expiring') $expiringDocs++;
            elseif($doc['status'] === 'expired') $expiredDocs++;
        }
    @endphp
    
    <div class="col-xl-4 col-md-4 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            {{ trans('messages.Valid Documents') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $validDocs }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-4 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            {{ trans('messages.Expiring Soon') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $expiringDocs }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-4 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            {{ trans('messages.Expired Documents') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $expiredDocs }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Documents List -->
<div class="row">
    @foreach($documents as $key => $document)
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100 
                    {{ $document['status'] === 'expired' ? 'border-left-danger' : 
                       ($document['status'] === 'expiring' ? 'border-left-warning' : 'border-left-success') }}">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold 
                          {{ $document['status'] === 'expired' ? 'text-danger' : 
                             ($document['status'] === 'expiring' ? 'text-warning' : 'text-success') }}">
                    <i class="fas fa-{{ $key === 'passport' ? 'passport' : 
                                       ($key === 'visa' ? 'id-card' : 
                                       ($key === 'emirates_id' ? 'address-card' : 'file-contract')) }} mr-2"></i>
                    {{ trans('messages.' . $document['name']) }}
                </h6>
            </div>
            <div class="card-body">
                @if($document['number'])
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Document Number') }}:</label>
                    <p class="text-gray-800 mb-0">
                        <code>{{ $document['number'] }}</code>
                    </p>
                </div>
                @endif
                
                @if($document['expiry'])
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Expiry Date') }}:</label>
                    <p class="text-gray-800 mb-0">
                        {{ $document['expiry']->format('Y-m-d') }}
                        
                        @if($document['status'] === 'expired')
                            <span class="badge badge-danger ml-1">{{ trans('messages.Expired') }}</span>
                        @elseif($document['status'] === 'expiring')
                            <span class="badge badge-warning ml-1">
                                {{ $document['expiry']->diffInDays(now(), false) }} {{ trans('messages.days left') }}
                            </span>
                        @else
                            <span class="badge badge-success ml-1">{{ trans('messages.Valid') }}</span>
                        @endif
                    </p>
                </div>
                @endif
                
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Status') }}:</label>
                    <p class="text-gray-800 mb-0">
                        <span class="badge {{ $document['status'] === 'expired' ? 'badge-danger' : 
                                             ($document['status'] === 'expiring' ? 'badge-warning' : 'badge-success') }} p-2">
                            <i class="fas fa-{{ $document['status'] === 'expired' ? 'times-circle' : 
                                               ($document['status'] === 'expiring' ? 'exclamation-triangle' : 'check-circle') }} mr-1"></i>
                            {{ trans('messages.' . ucfirst(str_replace('_', ' ', $document['status']))) }}
                        </span>
                    </p>
                </div>
                
                @if($document['file'])
                <div class="text-center">
                    <a href="{{ Storage::url($document['file']) }}" 
                       class="btn btn-outline-primary btn-sm" 
                       target="_blank">
                        <i class="fas fa-download mr-2"></i>{{ trans('messages.Download Copy') }}
                    </a>
                </div>
                @else
                <div class="text-center">
                    <span class="text-muted">{{ trans('messages.No file uploaded') }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Important Notice -->
@if($expiredDocs > 0 || $expiringDocs > 0)
<div class="card shadow mt-4">
    <div class="card-header py-3 bg-warning text-white">
        <h6 class="m-0 font-weight-bold text-white">
            <i class="fas fa-exclamation-triangle mr-2"></i>{{ trans('messages.Important Notice') }}
        </h6>
    </div>
    <div class="card-body">
        <p class="mb-2">{{ trans('messages.You have documents that require attention') }}:</p>
        <ul class="mb-0">
            @if($expiredDocs > 0)
                <li class="text-danger">{{ $expiredDocs }} {{ trans('messages.expired documents that need immediate renewal') }}</li>
            @endif
            @if($expiringDocs > 0)
                <li class="text-warning">{{ $expiringDocs }} {{ trans('messages.documents expiring soon') }}</li>
            @endif
        </ul>
        <p class="mt-3 mb-0">
            <strong>{{ trans('messages.Action Required') }}:</strong> 
            {{ trans('messages.Please contact HR department to update your documents') }}
        </p>
    </div>
</div>
@endif
@endsection
