{{-- 
Author: Eng.Fahed
Leaves Index View - HR System
قائمة الإجازات مع البحث والفلترة
--}}

@extends('layouts.app')

@section('title', trans('messages.Leave Management'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">
            @if(auth()->user()->user_type === 'employee')
                {{ trans('messages.My Leaves') }}
            @else
                {{ trans('messages.Leave Management') }}
            @endif
        </h1>
        <p class="text-muted">
            @if(auth()->user()->user_type === 'employee')
                {{ trans('messages.View and manage your leave requests') }}
            @else
                {{ trans('messages.Manage employee leave requests and approvals') }}
            @endif
        </p>
    </div>
    @can('leaves.create')
    <a href="{{ route('leaves.create') }}" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-plus fa-sm text-white-50"></i> {{ trans('messages.Request Leave') }}
    </a>
    @endcan
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            {{ trans('messages.Total This Year') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_this_year'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
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
                            {{ trans('messages.Pending Approval') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                            {{ trans('messages.Approved Leaves') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['approved'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            {{ trans('messages.Current Leaves') }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['current_leaves'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-filter mr-2"></i>{{ trans('messages.Search and Filter') }}
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('leaves.index') }}">
            <div class="row">
                @if(auth()->user()->user_type !== 'employee')
                <div class="col-md-3 mb-3">
                    <label for="employee_id" class="form-label">{{ trans('messages.Employee') }}</label>
                    <select name="employee_id" id="employee_id" class="form-control">
                        <option value="">{{ trans('messages.All Employees') }}</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->full_name }} ({{ $employee->employee_id }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <div class="col-md-3 mb-3">
                    <label for="status" class="form-label">{{ trans('messages.Status') }}</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">{{ trans('messages.All Statuses') }}</option>
                        @foreach($statuses as $key => $status)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                {{ trans('messages.' . $status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label for="leave_type" class="form-label">{{ trans('messages.Leave Type') }}</label>
                    <select name="leave_type" id="leave_type" class="form-control">
                        <option value="">{{ trans('messages.All Types') }}</option>
                        @foreach($leaveTypes as $key => $type)
                            <option value="{{ $key }}" {{ request('leave_type') === $key ? 'selected' : '' }}>
                                {{ trans('messages.' . $type) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3 mb-3">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search mr-1"></i>{{ trans('messages.Search') }}
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <a href="{{ route('leaves.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo mr-1"></i>{{ trans('messages.Clear Filters') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Leaves Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-table mr-2"></i>{{ trans('messages.Leave Requests') }}
        </h6>
    </div>
    <div class="card-body">
        @if($leaves->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="bg-light">
                    <tr>
                        @if(auth()->user()->user_type !== 'employee')
                        <th>{{ trans('messages.Employee') }}</th>
                        @endif
                        <th>{{ trans('messages.Leave Type') }}</th>
                        <th>{{ trans('messages.Period') }}</th>
                        <th>{{ trans('messages.Duration') }}</th>
                        <th>{{ trans('messages.Reason') }}</th>
                        <th>{{ trans('messages.Status') }}</th>
                        <th>{{ trans('messages.Submitted') }}</th>
                        <th>{{ trans('messages.Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($leaves as $leave)
                    <tr>
                        @if(auth()->user()->user_type !== 'employee')
                        <td>
                            <div class="d-flex align-items-center">
                                @if($leave->employee->profile_photo)
                                    <img src="{{ Storage::url($leave->employee->profile_photo) }}" 
                                         alt="{{ $leave->employee->full_name }}" 
                                         class="rounded-circle mr-2" 
                                         width="32" height="32">
                                @else
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-2" 
                                         style="width: 32px; height: 32px; font-size: 14px;">
                                        {{ substr($leave->employee->first_name, 0, 1) }}{{ substr($leave->employee->last_name, 0, 1) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="font-weight-bold">{{ $leave->employee->full_name }}</div>
                                    <small class="text-muted">{{ $leave->employee->job_title }}</small>
                                </div>
                            </div>
                        </td>
                        @endif
                        <td>
                            <span class="badge badge-info">
                                {{ trans('messages.' . $leaveTypes[$leave->leave_type]) }}
                            </span>
                            @if($leave->is_medical)
                                <br><small class="text-warning">{{ trans('messages.Medical') }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="font-weight-bold">{{ $leave->start_date->format('Y-m-d') }}</div>
                            <small class="text-muted">{{ trans('messages.to') }} {{ $leave->end_date->format('Y-m-d') }}</small>
                        </td>
                        <td>
                            <span class="badge badge-secondary">{{ $leave->duration_display }}</span>
                        </td>
                        <td>
                            <span title="{{ $leave->reason }}">
                                {{ Str::limit($leave->reason, 30) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $leave->status_badge_class }}">
                                {{ trans('messages.' . ucfirst($leave->status)) }}
                            </span>
                            @if($leave->is_current)
                                <br><small class="text-success">{{ trans('messages.Currently On Leave') }}</small>
                            @elseif($leave->start_date->isFuture() && $leave->status === 'approved')
                                <br><small class="text-info">{{ trans('messages.Upcoming') }}</small>
                            @endif
                        </td>
                        <td>{{ $leave->created_at->diffForHumans() }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('leaves.show', $leave) }}" 
                                   class="btn btn-sm btn-info" title="{{ trans('messages.View Details') }}">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($leave->can_be_edited)
                                <a href="{{ route('leaves.edit', $leave) }}" 
                                   class="btn btn-sm btn-warning" title="{{ trans('messages.Edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                                
                                @can('leaves.approve')
                                @if($leave->status === 'pending' && auth()->user()->user_type !== 'employee')
                                <button type="button" class="btn btn-sm btn-success" 
                                        onclick="approveLeave({{ $leave->id }})" 
                                        title="{{ trans('messages.Approve') }}">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" 
                                        onclick="rejectLeave({{ $leave->id }})" 
                                        title="{{ trans('messages.Reject') }}">
                                    <i class="fas fa-times"></i>
                                </button>
                                @endif
                                @endcan
                                
                                @if($leave->can_be_cancelled)
                                <button type="button" class="btn btn-sm btn-secondary" 
                                        onclick="cancelLeave({{ $leave->id }})" 
                                        title="{{ trans('messages.Cancel') }}">
                                    <i class="fas fa-ban"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                {{ trans('messages.Showing') }} {{ $leaves->firstItem() ?? 0 }} {{ trans('messages.to') }} 
                {{ $leaves->lastItem() ?? 0 }} {{ trans('messages.of') }} {{ $leaves->total() }} {{ trans('messages.results') }}
            </div>
            <div>
                {{ $leaves->links('pagination.custom') }}
            </div>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fas fa-calendar-plus fa-3x text-gray-300 mb-3"></i>
            <h5 class="text-gray-600">{{ trans('messages.No leave requests found') }}</h5>
            <p class="text-muted">
                @if(auth()->user()->user_type === 'employee')
                    {{ trans('messages.You have not submitted any leave requests yet') }}
                @else
                    {{ trans('messages.No leave requests have been submitted') }}
                @endif
            </p>
            @can('leaves.create')
            <a href="{{ route('leaves.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i>{{ trans('messages.Submit First Leave Request') }}
            </a>
            @endcan
        </div>
        @endif
    </div>
</div>

<!-- Approval Modals -->
@can('leaves.approve')
<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">{{ trans('messages.Approve Leave Request') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="approveForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="approve_notes">{{ trans('messages.Approval Notes') }} ({{ trans('messages.Optional') }})</label>
                        <textarea name="notes" id="approve_notes" rows="3" 
                                  class="form-control" 
                                  placeholder="{{ trans('messages.Enter any notes for the approval') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('messages.Cancel') }}</button>
                    <button type="submit" class="btn btn-success">{{ trans('messages.Approve Leave') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">{{ trans('messages.Reject Leave Request') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="rejectForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="reject_reason">{{ trans('messages.Rejection Reason') }} <span class="text-danger">*</span></label>
                        <textarea name="reason" id="reject_reason" rows="3" 
                                  class="form-control" required
                                  placeholder="{{ trans('messages.Please provide a reason for rejection') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('messages.Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ trans('messages.Reject Leave') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">{{ trans('messages.Cancel Leave Request') }}</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="cancelForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="cancel_reason">{{ trans('messages.Cancellation Reason') }} <span class="text-danger">*</span></label>
                        <textarea name="reason" id="cancel_reason" rows="3" 
                                  class="form-control" required
                                  placeholder="{{ trans('messages.Please provide a reason for cancellation') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ trans('messages.Cancel') }}</button>
                    <button type="submit" class="btn btn-warning">{{ trans('messages.Cancel Leave') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
@endsection

@push('scripts')
<script>
let currentLeaveId = null;

function approveLeave(leaveId) {
    currentLeaveId = leaveId;
    $('#approveModal').modal('show');
}

function rejectLeave(leaveId) {
    currentLeaveId = leaveId;
    $('#rejectModal').modal('show');
}

function cancelLeave(leaveId) {
    currentLeaveId = leaveId;
    $('#cancelModal').modal('show');
}

// معالجة الموافقة
$('#approveForm').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: `/leaves/${currentLeaveId}/approve`,
        method: 'PATCH',
        data: {
            _token: '{{ csrf_token() }}',
            notes: $('#approve_notes').val()
        },
        success: function(response) {
            $('#approveModal').modal('hide');
            location.reload();
        },
        error: function(xhr) {
            alert('Error: ' + xhr.responseJSON.message);
        }
    });
});

// معالجة الرفض
$('#rejectForm').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: `/leaves/${currentLeaveId}/reject`,
        method: 'PATCH',
        data: {
            _token: '{{ csrf_token() }}',
            reason: $('#reject_reason').val()
        },
        success: function(response) {
            $('#rejectModal').modal('hide');
            location.reload();
        },
        error: function(xhr) {
            alert('Error: ' + xhr.responseJSON.message);
        }
    });
});

// معالجة الإلغاء
$('#cancelForm').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: `/leaves/${currentLeaveId}/cancel`,
        method: 'PATCH',
        data: {
            _token: '{{ csrf_token() }}',
            reason: $('#cancel_reason').val()
        },
        success: function(response) {
            $('#cancelModal').modal('hide');
            location.reload();
        },
        error: function(xhr) {
            alert('Error: ' + xhr.responseJSON.message);
        }
    });
});
</script>
@endpush
