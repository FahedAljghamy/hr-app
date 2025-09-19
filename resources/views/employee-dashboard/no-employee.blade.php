{{-- 
Author: Eng.Fahed
Employee Dashboard No Employee - HR System
صفحة عدم وجود بيانات موظف
--}}

@extends('layouts.app')

@section('title', trans('messages.Employee Profile Not Found'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="text-center">
                <div class="error mx-auto" data-text="404">
                    <i class="fas fa-user-slash fa-5x text-gray-300 mb-4"></i>
                </div>
                <p class="lead text-gray-800 mb-5">{{ trans('messages.Employee Profile Not Found') }}</p>
                <p class="text-gray-500 mb-4">
                    {{ trans('messages.Your user account is not linked to an employee record') }}. 
                    {{ trans('messages.Please contact your HR department to set up your employee profile') }}.
                </p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-left mr-2"></i>{{ trans('messages.Go to Main Dashboard') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
