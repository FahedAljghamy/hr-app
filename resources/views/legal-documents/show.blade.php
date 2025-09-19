{{-- 
Author: Eng.Fahed
Legal Documents Show View - HR System
عرض تفاصيل المستند القانوني
--}}

@extends('layouts.app')

@section('title', trans('messages.Document Details') . ': ' . $legalDocument->document_name)

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Document Details') }}</h1>
        <p class="text-muted">{{ trans('messages.View detailed information about') }} "{{ $legalDocument->document_name }}"</p>
    </div>
    <div>
        @can('legal.documents.edit')
        <a href="{{ route('legal-documents.edit', $legalDocument) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm mr-2">
            <i class="fas fa-edit fa-sm text-white-50"></i> {{ trans('messages.Edit Document') }}
        </a>
        @endcan
        
        @if($legalDocument->file_path)
        @can('legal.documents.download')
        <a href="{{ route('legal-documents.download', $legalDocument) }}" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm mr-2">
            <i class="fas fa-download fa-sm text-white-50"></i> {{ trans('messages.Download') }}
        </a>
        @endcan
        @endif
        
        <a href="{{ route('legal-documents.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Back to List') }}
        </a>
    </div>
</div>

<!-- Expiry Alert -->
@if($legalDocument->status === 'expired')
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle mr-2"></i>
    <strong>{{ trans('messages.Document Expired') }}!</strong> 
    {{ trans('messages.This document expired on') }} {{ $legalDocument->expiry_date->format('Y-m-d') }}.
    {{ trans('messages.Please renew immediately to maintain compliance') }}.
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
@elseif($legalDocument->is_expiring_soon)
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="fas fa-clock mr-2"></i>
    <strong>{{ trans('messages.Document Expiring Soon') }}!</strong> 
    {{ trans('messages.This document will expire in') }} {{ $legalDocument->days_until_expiry }} {{ trans('messages.days') }} 
    ({{ $legalDocument->expiry_date->format('Y-m-d') }}).
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
@endif

<!-- Document Information -->
<div class="row">
    <!-- Basic Information -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-file-alt mr-2"></i>{{ trans('messages.Document Information') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Document Name') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $legalDocument->document_name }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Document Number') }}:</label>
                        <p class="text-gray-800 mb-0">
                            <code>{{ $legalDocument->document_number }}</code>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Document Type') }}:</label>
                        <p class="text-gray-800 mb-0">
                            <span class="badge badge-info">
                                {{ trans('messages.' . str_replace('_', ' ', ucwords($legalDocument->document_type, '_'))) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Status') }}:</label>
                        <p class="text-gray-800 mb-0">
                            <span class="badge {{ $legalDocument->status_badge_class }}">
                                {{ trans('messages.' . $legalDocument->status_text) }}
                            </span>
                            @if($legalDocument->is_mandatory)
                                <span class="badge badge-danger ml-1">{{ trans('messages.Mandatory') }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Issuing Authority') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $legalDocument->issuing_authority }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Issuing Location') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $legalDocument->issuing_location ?: trans('messages.Not specified') }}</p>
                    </div>
                    @if($legalDocument->description)
                    <div class="col-12 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Description') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $legalDocument->description }}</p>
                    </div>
                    @endif
                    @if($legalDocument->notes)
                    <div class="col-12 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Notes') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $legalDocument->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Dates and Status -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-calendar mr-2"></i>{{ trans('messages.Important Dates') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Issue Date') }}:</label>
                    <p class="text-gray-800 mb-0">{{ $legalDocument->issue_date->format('Y-m-d') }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Expiry Date') }}:</label>
                    <p class="text-gray-800 mb-0">
                        {{ $legalDocument->expiry_date->format('Y-m-d') }}
                        @if($legalDocument->is_expiring_soon)
                            <br><small class="text-warning">
                                <i class="fas fa-clock"></i> {{ $legalDocument->days_until_expiry }} {{ trans('messages.days left') }}
                            </small>
                        @elseif($legalDocument->status === 'expired')
                            <br><small class="text-danger">
                                <i class="fas fa-exclamation-triangle"></i> {{ trans('messages.Expired') }}
                            </small>
                        @endif
                    </p>
                </div>
                @if($legalDocument->renewal_date)
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Renewal Date') }}:</label>
                    <p class="text-gray-800 mb-0">{{ $legalDocument->renewal_date->format('Y-m-d') }}</p>
                </div>
                @endif
            </div>
        </div>

        @if($legalDocument->file_path)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-file mr-2"></i>{{ trans('messages.Document File') }}
                </h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="fas fa-file-{{ $legalDocument->file_type === 'pdf' ? 'pdf' : 'alt' }} fa-3x text-gray-400"></i>
                </div>
                <p class="font-weight-bold">{{ $legalDocument->document_name }}.{{ $legalDocument->file_type }}</p>
                <p class="text-muted small">{{ trans('messages.File Size') }}: {{ $legalDocument->formatted_file_size }}</p>
                @can('legal.documents.download')
                <a href="{{ route('legal-documents.download', $legalDocument) }}" class="btn btn-success btn-block">
                    <i class="fas fa-download mr-2"></i>{{ trans('messages.Download Document') }}
                </a>
                @endcan
            </div>
        </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">
                    <i class="fas fa-cog mr-2"></i>{{ trans('messages.Document Settings') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Reminder Days') }}:</label>
                    <p class="text-gray-800 mb-0">{{ $legalDocument->renewal_reminder_days }} {{ trans('messages.days') }}</p>
                </div>
                @if($legalDocument->renewal_cost)
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Renewal Cost') }}:</label>
                    <p class="text-gray-800 mb-0">{{ number_format($legalDocument->renewal_cost, 2) }} {{ $legalDocument->currency }}</p>
                </div>
                @endif
                <div class="mb-3">
                    <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Assignment') }}:</label>
                    <p class="text-gray-800 mb-0">
                        @if($legalDocument->branch)
                            <i class="fas fa-building mr-1"></i>{{ trans('messages.Branch') }}: {{ $legalDocument->branch->name }}
                        @else
                            <i class="fas fa-globe mr-1"></i>{{ trans('messages.Company Level') }}
                        @endif
                    </p>
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
                @can('legal.documents.edit')
                <a href="{{ route('legal-documents.edit', $legalDocument) }}" class="btn btn-warning btn-lg mr-3">
                    <i class="fas fa-edit mr-2"></i>{{ trans('messages.Edit Document') }}
                </a>
                @endcan
                
                @if($legalDocument->file_path)
                @can('legal.documents.download')
                <a href="{{ route('legal-documents.download', $legalDocument) }}" class="btn btn-success btn-lg mr-3">
                    <i class="fas fa-download mr-2"></i>{{ trans('messages.Download Document') }}
                </a>
                @endcan
                @endif
                
                @can('legal.documents.delete')
                <form action="{{ route('legal-documents.destroy', $legalDocument) }}" method="POST" class="d-inline mr-3"
                      onsubmit="return confirm('{{ trans('messages.Are you sure you want to delete this document?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-lg">
                        <i class="fas fa-trash mr-2"></i>{{ trans('messages.Delete Document') }}
                    </button>
                </form>
                @endcan
                
                <a href="{{ route('legal-documents.index') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-arrow-left mr-2"></i>{{ trans('messages.Back to List') }}
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
