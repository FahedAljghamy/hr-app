{{-- 
Author: Eng.Fahed
Legal Documents Edit View - HR System
صفحة تعديل المستند القانوني
--}}

@extends('layouts.app')

@section('title', trans('messages.Edit Document') . ': ' . $legalDocument->document_name)

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Edit Document') }}</h1>
        <p class="text-muted">{{ trans('messages.Update document information and settings') }}</p>
    </div>
    <div>
        <a href="{{ route('legal-documents.show', $legalDocument) }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm mr-2">
            <i class="fas fa-eye fa-sm text-white-50"></i> {{ trans('messages.View Details') }}
        </a>
        <a href="{{ route('legal-documents.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-info shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Back to List') }}
        </a>
    </div>
</div>

<!-- Error Messages -->
@if($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle"></i>
    <strong>{{ trans('messages.Please correct the following errors:') }}</strong>
    <ul class="mb-0 mt-2">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
@endif

<!-- Edit Document Form -->
<form action="{{ route('legal-documents.update', $legalDocument) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
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
                            <label for="document_type" class="form-label">{{ trans('messages.Document Type') }} <span class="text-danger">*</span></label>
                            <select name="document_type" id="document_type" class="form-control @error('document_type') is-invalid @enderror" required>
                                <option value="">{{ trans('messages.Choose Document Type') }}</option>
                                @foreach($documentTypes as $key => $type)
                                    <option value="{{ $key }}" {{ old('document_type', $legalDocument->document_type) === $key ? 'selected' : '' }}>
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                            @error('document_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="document_number" class="form-label">{{ trans('messages.Document Number') }} <span class="text-danger">*</span></label>
                            <input type="text" name="document_number" id="document_number" 
                                   value="{{ old('document_number', $legalDocument->document_number) }}" 
                                   class="form-control @error('document_number') is-invalid @enderror" 
                                   placeholder="{{ trans('messages.Enter document number') }}" required>
                            @error('document_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="document_name" class="form-label">{{ trans('messages.Document Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="document_name" id="document_name" 
                                   value="{{ old('document_name', $legalDocument->document_name) }}" 
                                   class="form-control @error('document_name') is-invalid @enderror" 
                                   placeholder="{{ trans('messages.Enter document name') }}" required>
                            @error('document_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="issuing_authority" class="form-label">{{ trans('messages.Issuing Authority') }} <span class="text-danger">*</span></label>
                            <input type="text" name="issuing_authority" id="issuing_authority" 
                                   value="{{ old('issuing_authority', $legalDocument->issuing_authority) }}" 
                                   class="form-control @error('issuing_authority') is-invalid @enderror" 
                                   placeholder="{{ trans('messages.Enter issuing authority') }}" required>
                            @error('issuing_authority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="issuing_location" class="form-label">{{ trans('messages.Issuing Location') }}</label>
                            <input type="text" name="issuing_location" id="issuing_location" 
                                   value="{{ old('issuing_location', $legalDocument->issuing_location) }}" 
                                   class="form-control @error('issuing_location') is-invalid @enderror" 
                                   placeholder="{{ trans('messages.Enter issuing location') }}">
                            @error('issuing_location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">{{ trans('messages.Description') }}</label>
                            <textarea name="description" id="description" rows="3" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="{{ trans('messages.Enter document description') }}">{{ old('description', $legalDocument->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dates and Settings -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-calendar mr-2"></i>{{ trans('messages.Important Dates') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="issue_date" class="form-label">{{ trans('messages.Issue Date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="issue_date" id="issue_date" 
                               value="{{ old('issue_date', $legalDocument->issue_date->format('Y-m-d')) }}" 
                               class="form-control @error('issue_date') is-invalid @enderror" required>
                        @error('issue_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="expiry_date" class="form-label">{{ trans('messages.Expiry Date') }} <span class="text-danger">*</span></label>
                        <input type="date" name="expiry_date" id="expiry_date" 
                               value="{{ old('expiry_date', $legalDocument->expiry_date->format('Y-m-d')) }}" 
                               class="form-control @error('expiry_date') is-invalid @enderror" required>
                        @error('expiry_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="renewal_date" class="form-label">{{ trans('messages.Renewal Date') }}</label>
                        <input type="date" name="renewal_date" id="renewal_date" 
                               value="{{ old('renewal_date', $legalDocument->renewal_date?->format('Y-m-d')) }}" 
                               class="form-control @error('renewal_date') is-invalid @enderror">
                        <small class="form-text text-muted">{{ trans('messages.Expected renewal date') }}</small>
                        @error('renewal_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-cog mr-2"></i>{{ trans('messages.Document Settings') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="renewal_reminder_days" class="form-label">{{ trans('messages.Reminder Days') }} <span class="text-danger">*</span></label>
                        <input type="number" name="renewal_reminder_days" id="renewal_reminder_days" 
                               value="{{ old('renewal_reminder_days', $legalDocument->renewal_reminder_days) }}" 
                               class="form-control @error('renewal_reminder_days') is-invalid @enderror" 
                               min="1" max="365" required>
                        <small class="form-text text-muted">{{ trans('messages.Days before expiry to send notification') }}</small>
                        @error('renewal_reminder_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="renewal_cost" class="form-label">{{ trans('messages.Renewal Cost') }}</label>
                        <div class="input-group">
                            <input type="number" name="renewal_cost" id="renewal_cost" 
                                   value="{{ old('renewal_cost', $legalDocument->renewal_cost) }}" 
                                   class="form-control @error('renewal_cost') is-invalid @enderror" 
                                   step="0.01" min="0" placeholder="0.00">
                            <div class="input-group-append">
                                <select name="currency" class="form-control @error('currency') is-invalid @enderror" required>
                                    <option value="AED" {{ old('currency', $legalDocument->currency) === 'AED' ? 'selected' : '' }}>AED</option>
                                    <option value="USD" {{ old('currency', $legalDocument->currency) === 'USD' ? 'selected' : '' }}>USD</option>
                                    <option value="EUR" {{ old('currency', $legalDocument->currency) === 'EUR' ? 'selected' : '' }}>EUR</option>
                                </select>
                            </div>
                        </div>
                        @error('renewal_cost')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('currency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="is_mandatory" value="1" 
                                   class="custom-control-input" id="is_mandatory" 
                                   {{ old('is_mandatory', $legalDocument->is_mandatory) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_mandatory">
                                {{ trans('messages.Mandatory Document') }}
                            </label>
                        </div>
                        <small class="form-text text-muted">{{ trans('messages.Required for legal compliance') }}</small>
                    </div>
                </div>
            </div>

            @if($legalDocument->file_path)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-file mr-2"></i>{{ trans('messages.Current File') }}
                    </h6>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-file-{{ $legalDocument->file_type === 'pdf' ? 'pdf' : 'alt' }} fa-3x text-gray-400"></i>
                    </div>
                    <p class="font-weight-bold">{{ $legalDocument->document_name }}.{{ $legalDocument->file_type }}</p>
                    <p class="text-muted small">{{ $legalDocument->formatted_file_size }}</p>
                    
                    <div class="form-group">
                        <label for="document_file" class="form-label">{{ trans('messages.Upload New File') }}</label>
                        <input type="file" name="document_file" id="document_file" 
                               class="form-control-file @error('document_file') is-invalid @enderror" 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <small class="form-text text-muted">{{ trans('messages.Leave empty to keep current file') }}</small>
                        @error('document_file')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            @else
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-upload mr-2"></i>{{ trans('messages.Upload File') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="document_file" class="form-label">{{ trans('messages.Document File') }}</label>
                        <input type="file" name="document_file" id="document_file" 
                               class="form-control-file @error('document_file') is-invalid @enderror" 
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <small class="form-text text-muted">{{ trans('messages.Accepted formats: PDF, DOC, DOCX, JPG, PNG. Max size: 10MB') }}</small>
                        @error('document_file')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Form Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center">
                    <button type="submit" class="btn btn-primary btn-lg mr-3">
                        <i class="fas fa-save mr-2"></i>{{ trans('messages.Update Document') }}
                    </button>
                    <a href="{{ route('legal-documents.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times mr-2"></i>{{ trans('messages.Cancel') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
