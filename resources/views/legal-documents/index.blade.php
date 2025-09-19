{{-- 
Author: Eng.Fahed
Legal Documents Index View - HR System
عرض قائمة المستندات القانونية مع تنبيهات انتهاء الصلاحية
--}}

@extends('layouts.app')

@section('title', trans('messages.Legal Documents'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Legal Documents') }}</h1>
        <p class="text-muted">{{ trans('messages.Manage legal documents and compliance requirements') }}</p>
    </div>
    @can('legal.documents.create')
    <a href="{{ route('legal-documents.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> {{ trans('messages.Add New Document') }}
    </a>
    @endcan
</div>

<!-- Expiry Alerts -->
@if($stats['expiring_soon'] > 0 || $stats['expired'] > 0)
<div class="row mb-4">
    @if($stats['expired'] > 0)
    <div class="col-md-6">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            <strong>{{ trans('messages.Urgent') }}!</strong> 
            {{ $stats['expired'] }} {{ trans('messages.documents have expired') }}.
            <a href="{{ route('legal-documents.index', ['status' => 'expired']) }}" class="alert-link">{{ trans('messages.View expired documents') }}</a>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    </div>
    @endif
    
    @if($stats['expiring_soon'] > 0)
    <div class="col-md-6">
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-clock mr-2"></i>
            <strong>{{ trans('messages.Warning') }}!</strong> 
            {{ $stats['expiring_soon'] }} {{ trans('messages.documents expiring soon') }}.
            <a href="{{ route('legal-documents.index', ['expiring_soon' => 30]) }}" class="alert-link">{{ trans('messages.View expiring documents') }}</a>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    </div>
    @endif
</div>
@endif

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

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            {{ trans('messages.Total Documents') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            {{ trans('messages.Active Documents') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            {{ trans('messages.Expiring Soon') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['expiring_soon'] }}</div>
                        <div class="text-xs text-muted">{{ trans('messages.Next 30 days') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            {{ trans('messages.Expired Documents') }}</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['expired'] }}</div>
                        <div class="text-xs text-muted">{{ trans('messages.Requires immediate action') }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-search mr-2"></i>{{ trans('messages.Search') }} {{ trans('messages.and') }} {{ trans('messages.Filter') }}
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('legal-documents.index') }}">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="search" class="form-label">{{ trans('messages.Search') }}</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           class="form-control" placeholder="{{ trans('messages.Search by name or number...') }}">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="document_type" class="form-label">{{ trans('messages.Document Type') }}</label>
                    <select name="document_type" id="document_type" class="form-control">
                        <option value="">{{ trans('messages.All Types') }}</option>
                        @foreach($documentTypes as $key => $type)
                            <option value="{{ $key }}" {{ request('document_type') === $key ? 'selected' : '' }}>
                                {{ trans('messages.' . explode(' (', $type)[0]) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2 mb-3">
                    <label for="status" class="form-label">{{ trans('messages.Status') }}</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">{{ trans('messages.All Statuses') }}</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ trans('messages.Active') }}</option>
                        <option value="expiring_soon" {{ request('expiring_soon') ? 'selected' : '' }}>{{ trans('messages.Expiring Soon') }}</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>{{ trans('messages.Expired') }}</option>
                        <option value="pending_renewal" {{ request('status') === 'pending_renewal' ? 'selected' : '' }}>{{ trans('messages.Pending Renewal') }}</option>
                    </select>
                </div>
                
                <div class="col-md-2 mb-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> {{ trans('messages.Search') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Documents Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">{{ trans('messages.Legal Documents') }} ({{ $documents->total() }})</h6>
        <div class="dropdown no-arrow">
            <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown">
                <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                @can('legal.documents.create')
                <a class="dropdown-item" href="{{ route('legal-documents.create') }}">
                    <i class="fas fa-plus fa-sm fa-fw mr-2 text-gray-400"></i>
                    {{ trans('messages.Add New Document') }}
                </a>
                @endcan
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#" onclick="window.print()">
                    <i class="fas fa-print fa-sm fa-fw mr-2 text-gray-400"></i>
                    {{ trans('messages.Print') }}
                </a>
                <a class="dropdown-item" href="#" onclick="runExpiryCheck()">
                    <i class="fas fa-sync fa-sm fa-fw mr-2 text-gray-400"></i>
                    {{ trans('messages.Check Expiry Now') }}
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if($documents->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>{{ trans('messages.Document') }}</th>
                        <th>{{ trans('messages.Type') }}</th>
                        <th>{{ trans('messages.Issuing Authority') }}</th>
                        <th>{{ trans('messages.Issue Date') }}</th>
                        <th>{{ trans('messages.Expiry Date') }}</th>
                        <th>{{ trans('messages.Status') }}</th>
                        <th>{{ trans('messages.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documents as $document)
                    <tr class="{{ $document->status === 'expired' ? 'table-danger' : ($document->is_expiring_soon ? 'table-warning' : '') }}">
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-{{ $document->status === 'expired' ? 'danger' : ($document->is_expiring_soon ? 'warning' : 'primary') }} text-white mr-3">
                                    <i class="fas fa-{{ $document->is_mandatory ? 'exclamation' : 'file-alt' }}"></i>
                                </div>
                                <div>
                                    <div class="font-weight-bold">{{ $document->document_name }}</div>
                                    <small class="text-muted">{{ $document->document_number }}</small>
                                    @if($document->is_mandatory)
                                        <span class="badge badge-danger badge-sm ml-1">{{ trans('messages.Mandatory') }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-info">
                                {{ trans('messages.' . str_replace('_', ' ', ucwords($document->document_type, '_'))) }}
                            </span>
                        </td>
                        <td>{{ $document->issuing_authority }}</td>
                        <td>{{ $document->issue_date->format('Y-m-d') }}</td>
                        <td>
                            <div>
                                {{ $document->expiry_date->format('Y-m-d') }}
                                @if($document->is_expiring_soon)
                                    <br><small class="text-warning">
                                        <i class="fas fa-clock"></i> {{ $document->days_until_expiry }} {{ trans('messages.days left') }}
                                    </small>
                                @elseif($document->status === 'expired')
                                    <br><small class="text-danger">
                                        <i class="fas fa-exclamation-triangle"></i> {{ trans('messages.Expired') }}
                                    </small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $document->status_badge_class }}">
                                {{ trans('messages.' . $document->status_text) }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                @can('legal.documents.view')
                                <a href="{{ route('legal-documents.show', $document) }}" 
                                   class="btn btn-info btn-sm" title="{{ trans('messages.View Details') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan

                                @if($document->file_path)
                                @can('legal.documents.download')
                                <a href="{{ route('legal-documents.download', $document) }}" 
                                   class="btn btn-success btn-sm" title="{{ trans('messages.Download Document') }}">
                                    <i class="fas fa-download"></i>
                                </a>
                                @endcan
                                @endif

                                @can('legal.documents.edit')
                                <a href="{{ route('legal-documents.edit', $document) }}" 
                                   class="btn btn-warning btn-sm" title="{{ trans('messages.Edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endcan

                                @can('legal.documents.delete')
                                <form method="POST" action="{{ route('legal-documents.destroy', $document) }}" 
                                      class="d-inline"
                                      onsubmit="return confirm('{{ trans('messages.Are you sure you want to delete this document?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" title="{{ trans('messages.Delete') }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($documents->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted">
                    {{ trans('messages.Showing') }} {{ $documents->firstItem() }} {{ trans('messages.to') }} {{ $documents->lastItem() }} 
                    {{ trans('messages.of') }} {{ $documents->total() }} {{ trans('messages.results') }}
                </div>
                <div>
                    {{ $documents->links() }}
                </div>
            </div>
        </div>
        @endif

        @else
        <!-- Empty State -->
        <div class="text-center py-4">
            <div class="mb-3">
                <i class="fas fa-file-alt fa-3x text-gray-300"></i>
            </div>
            <h5 class="text-gray-600">{{ trans('messages.No Legal Documents') }}</h5>
            <p class="text-muted">{{ trans('messages.Start by adding required legal documents for compliance') }}</p>
            @can('legal.documents.create')
            <a href="{{ route('legal-documents.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ trans('messages.Add New Document') }}
            </a>
            @endcan
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function runExpiryCheck() {
    if (confirm('{{ trans('messages.This will check all documents for expiry. Continue?') }}')) {
        // إرسال طلب لفحص انتهاء الصلاحية
        fetch('/api/legal-documents/check-expiry', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  alert(data.message);
                  location.reload();
              } else {
                  alert('Error: ' + data.message);
              }
          }).catch(error => {
              console.error('Error:', error);
              alert('An error occurred while checking document expiry.');
          });
    }
}
</script>
@endpush
