{{-- 
Author: Eng.Fahed
Company Settings Create View - HR System
صفحة إنشاء إعدادات الشركة
--}}

@extends('layouts.app')

@section('title', trans('messages.Setup Company Settings'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Setup Company Settings') }}</h1>
        <p class="text-muted">{{ trans('messages.Configure your company information and preferences') }}</p>
    </div>
    <a href="{{ route('company-settings.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Back to Settings') }}
    </a>
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

<!-- Create Company Settings Form -->
<form action="{{ route('company-settings.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
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
                            <label for="company_name" class="form-label">{{ trans('messages.Company Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}" 
                                   class="form-control @error('company_name') is-invalid @enderror" 
                                   placeholder="{{ trans('messages.Enter company name') }}" required>
                            @error('company_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">{{ trans('messages.Official Email') }} <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   placeholder="{{ trans('messages.Enter official email') }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">{{ trans('messages.Phone Number') }}</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   placeholder="{{ trans('messages.Enter phone number') }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="website" class="form-label">{{ trans('messages.Website') }}</label>
                            <input type="url" name="website" id="website" value="{{ old('website') }}" 
                                   class="form-control @error('website') is-invalid @enderror" 
                                   placeholder="{{ trans('messages.Enter website URL') }}">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="address" class="form-label">{{ trans('messages.Official Address') }} <span class="text-danger">*</span></label>
                            <textarea name="address" id="address" rows="3" 
                                      class="form-control @error('address') is-invalid @enderror" 
                                      placeholder="{{ trans('messages.Enter complete address') }}" required>{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">{{ trans('messages.Company Description') }}</label>
                            <textarea name="description" id="description" rows="4" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="{{ trans('messages.Enter company description') }}">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
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
                <div class="card-body">
                    <div class="form-group">
                        <label for="logo" class="form-label">{{ trans('messages.Upload Logo') }}</label>
                        <input type="file" name="logo" id="logo" 
                               class="form-control-file @error('logo') is-invalid @enderror" 
                               accept="image/*">
                        <small class="form-text text-muted">{{ trans('messages.Accepted formats: JPG, PNG, GIF. Max size: 2MB') }}</small>
                        @error('logo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-cog mr-2"></i>{{ trans('messages.System Settings') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="timezone" class="form-label">{{ trans('messages.Timezone') }} <span class="text-danger">*</span></label>
                        <select name="timezone" id="timezone" class="form-control @error('timezone') is-invalid @enderror" required>
                            <option value="Asia/Damascus" {{ old('timezone') === 'Asia/Damascus' ? 'selected' : '' }}>{{ trans('messages.Damascus Time') }} (GMT+3)</option>
                            <option value="UTC" {{ old('timezone') === 'UTC' ? 'selected' : '' }}>UTC (GMT+0)</option>
                            <option value="Europe/London" {{ old('timezone') === 'Europe/London' ? 'selected' : '' }}>{{ trans('messages.London Time') }} (GMT+1)</option>
                        </select>
                        @error('timezone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="currency" class="form-label">{{ trans('messages.Currency') }} <span class="text-danger">*</span></label>
                        <select name="currency" id="currency" class="form-control @error('currency') is-invalid @enderror" required>
                            <option value="SYP" {{ old('currency') === 'SYP' ? 'selected' : '' }}>{{ trans('messages.Syrian Pound') }} (SYP)</option>
                            <option value="USD" {{ old('currency') === 'USD' ? 'selected' : '' }}>{{ trans('messages.US Dollar') }} (USD)</option>
                            <option value="EUR" {{ old('currency') === 'EUR' ? 'selected' : '' }}>{{ trans('messages.Euro') }} (EUR)</option>
                        </select>
                        @error('currency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">{{ trans('messages.Official Working Hours') }}</label>
                        <div class="row">
                            <div class="col-6">
                                <input type="time" name="working_hours_start" 
                                       class="form-control @error('working_hours_start') is-invalid @enderror" 
                                       value="{{ old('working_hours_start', '09:00') }}">
                            </div>
                            <div class="col-6">
                                <input type="time" name="working_hours_end" 
                                       class="form-control @error('working_hours_end') is-invalid @enderror" 
                                       value="{{ old('working_hours_end', '17:00') }}">
                            </div>
                        </div>
                        <small class="form-text text-muted">{{ trans('messages.Start time - End time') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-share-alt mr-2"></i>{{ trans('messages.Social Media') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="facebook" class="form-label">{{ trans('messages.Facebook') }}</label>
                        <input type="url" name="facebook" id="facebook" value="{{ old('facebook') }}" 
                               class="form-control @error('facebook') is-invalid @enderror" 
                               placeholder="https://facebook.com/company">
                        @error('facebook')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="twitter" class="form-label">{{ trans('messages.Twitter') }}</label>
                        <input type="url" name="twitter" id="twitter" value="{{ old('twitter') }}" 
                               class="form-control @error('twitter') is-invalid @enderror" 
                               placeholder="https://twitter.com/company">
                        @error('twitter')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="linkedin" class="form-label">{{ trans('messages.LinkedIn') }}</label>
                        <input type="url" name="linkedin" id="linkedin" value="{{ old('linkedin') }}" 
                               class="form-control @error('linkedin') is-invalid @enderror" 
                               placeholder="https://linkedin.com/company/company">
                        @error('linkedin')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="instagram" class="form-label">{{ trans('messages.Instagram') }}</label>
                        <input type="url" name="instagram" id="instagram" value="{{ old('instagram') }}" 
                               class="form-control @error('instagram') is-invalid @enderror" 
                               placeholder="https://instagram.com/company">
                        @error('instagram')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">
                        <i class="fas fa-file-alt mr-2"></i>{{ trans('messages.Legal Information') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="tax_number" class="form-label">{{ trans('messages.Tax Number') }}</label>
                        <input type="text" name="tax_number" id="tax_number" value="{{ old('tax_number') }}" 
                               class="form-control @error('tax_number') is-invalid @enderror" 
                               placeholder="{{ trans('messages.Enter tax number') }}">
                        @error('tax_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="registration_number" class="form-label">{{ trans('messages.Registration Number') }}</label>
                        <input type="text" name="registration_number" id="registration_number" value="{{ old('registration_number') }}" 
                               class="form-control @error('registration_number') is-invalid @enderror" 
                               placeholder="{{ trans('messages.Enter registration number') }}">
                        @error('registration_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center">
                    <button type="submit" class="btn btn-primary btn-lg mr-3">
                        <i class="fas fa-save mr-2"></i>{{ trans('messages.Save Settings') }}
                    </button>
                    <a href="{{ route('company-settings.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times mr-2"></i>{{ trans('messages.Cancel') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
