{{-- 
Author: Eng.Fahed
Branch Create View - HR System
صفحة إنشاء فرع جديد
--}}

@extends('layouts.app')

@section('title', trans('messages.Create Branch'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Create Branch') }}</h1>
        <p class="text-muted">{{ trans('messages.Add a new branch to your company') }}</p>
    </div>
    <a href="{{ route('branches.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
        <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Back to List') }}
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

<!-- Create Branch Form -->
<form action="{{ route('branches.store') }}" method="POST">
    @csrf
    
    <div class="row">
        <!-- Basic Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-building mr-2"></i>{{ trans('messages.Branch Information') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">{{ trans('messages.Branch Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   placeholder="{{ trans('messages.Enter branch name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="manager_name" class="form-label">{{ trans('messages.Manager Name') }}</label>
                            <input type="text" name="manager_name" id="manager_name" value="{{ old('manager_name') }}" 
                                   class="form-control @error('manager_name') is-invalid @enderror" 
                                   placeholder="{{ trans('messages.Enter manager name') }}">
                            @error('manager_name')
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
                            <label for="email" class="form-label">{{ trans('messages.Email Address') }}</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   placeholder="{{ trans('messages.Enter email address') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="address" class="form-label">{{ trans('messages.Branch Address') }} <span class="text-danger">*</span></label>
                            <textarea name="address" id="address" rows="3" 
                                      class="form-control @error('address') is-invalid @enderror" 
                                      placeholder="{{ trans('messages.Enter complete branch address') }}" required>{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">{{ trans('messages.Branch Location') }}</label>
                            <input type="text" name="location" id="location" value="{{ old('location') }}" 
                                   class="form-control @error('location') is-invalid @enderror" 
                                   placeholder="{{ trans('messages.Enter location description') }}">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" name="is_active" value="1" 
                                           class="custom-control-input" id="is_active" 
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">
                                        {{ trans('messages.Branch is Active') }}
                                    </label>
                                </div>
                                <small class="form-text text-muted">{{ trans('messages.Active branches are visible to employees') }}</small>
                            </div>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label for="description" class="form-label">{{ trans('messages.Branch Description') }}</label>
                            <textarea name="description" id="description" rows="3" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="{{ trans('messages.Enter branch description') }}">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Working Hours -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-clock mr-2"></i>{{ trans('messages.Working Hours') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">{{ trans('messages.Branch Working Hours') }}</label>
                        <div class="row">
                            <div class="col-6">
                                <label for="working_hours_start" class="form-label small">{{ trans('messages.Start Time') }}</label>
                                <input type="time" name="working_hours_start" id="working_hours_start"
                                       class="form-control @error('working_hours_start') is-invalid @enderror" 
                                       value="{{ old('working_hours_start', '09:00') }}">
                                @error('working_hours_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6">
                                <label for="working_hours_end" class="form-label small">{{ trans('messages.End Time') }}</label>
                                <input type="time" name="working_hours_end" id="working_hours_end"
                                       class="form-control @error('working_hours_end') is-invalid @enderror" 
                                       value="{{ old('working_hours_end', '17:00') }}">
                                @error('working_hours_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <small class="form-text text-muted">{{ trans('messages.Leave empty to use company default hours') }}</small>
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
                        <i class="fas fa-save mr-2"></i>{{ trans('messages.Create Branch') }}
                    </button>
                    <a href="{{ route('branches.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times mr-2"></i>{{ trans('messages.Cancel') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
