{{-- 
Author: Eng.Fahed
Leaves Show View - HR System
عرض تفاصيل الإجازة مع نظام التعليقات التفاعلي
--}}

@extends('layouts.app')

@section('title', trans('messages.Leave Request Details'))

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">{{ trans('messages.Leave Request Details') }}</h1>
        <p class="text-muted">{{ $leave->employee?->full_name ?? 'Unknown Employee' }} - {{ trans('messages.' . $leave->leave_type) }}</p>
    </div>
    <div>
        @if($leave->can_be_edited)
        <a href="{{ route('leaves.edit', $leave) }}" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm mr-2">
            <i class="fas fa-edit fa-sm text-white-50"></i> {{ trans('messages.Edit Request') }}
        </a>
        @endif
        
        <a href="{{ route('leaves.index') }}" class="d-none d-sm-inline-block btn btn-sm btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> {{ trans('messages.Back to List') }}
        </a>
    </div>
</div>

<!-- Status Alert -->
@if($leave->status === 'rejected')
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-times-circle mr-2"></i>
    <strong>{{ trans('messages.Leave Request Rejected') }}!</strong> 
    {{ $leave->rejection_reason }}
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
@elseif($leave->status === 'approved')
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle mr-2"></i>
    <strong>{{ trans('messages.Leave Request Approved') }}!</strong> 
    {{ trans('messages.Your leave has been approved and will be processed') }}.
    @if($leave->admin_notes)
        <br><strong>{{ trans('messages.Notes') }}:</strong> {{ $leave->admin_notes }}
    @endif
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
@elseif($leave->is_current)
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <i class="fas fa-info-circle mr-2"></i>
    <strong>{{ trans('messages.Currently On Leave') }}!</strong> 
    {{ trans('messages.This leave is currently active') }}.
    <button type="button" class="close" data-dismiss="alert">
        <span>&times;</span>
    </button>
</div>
@endif

<!-- Main Content -->
<div class="row">
    <!-- Left Column -->
    <div class="col-lg-8">
        <!-- Leave Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calendar-alt mr-2"></i>{{ trans('messages.Leave Information') }}
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Leave Type') }}:</label>
                        <p class="text-gray-800 mb-0">
                            <span class="badge badge-info p-2">
                                {{ trans('messages.' . $leaveTypes[$leave->leave_type] ?? $leave->leave_type) }}
                            </span>
                            @if($leave->is_medical)
                                <span class="badge badge-warning ml-1">{{ trans('messages.Medical') }}</span>
                            @endif
                        </p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Duration') }}:</label>
                        <p class="text-gray-800 mb-0">
                            <span class="badge badge-secondary p-2">{{ $leave->duration_display }}</span>
                        </p>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Start Date') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $leave->start_date->format('Y-m-d') }}</p>
                        @if($leave->start_time)
                            <small class="text-muted">{{ $leave->start_time->format('H:i') }}</small>
                        @endif
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.End Date') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $leave->end_date->format('Y-m-d') }}</p>
                        @if($leave->end_time)
                            <small class="text-muted">{{ $leave->end_time->format('H:i') }}</small>
                        @endif
                    </div>
                    
                    <div class="col-12 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Reason') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $leave->reason }}</p>
                    </div>
                    
                    @if($leave->description)
                    <div class="col-12 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Additional Details') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $leave->description }}</p>
                    </div>
                    @endif
                    
                    @if($leave->covering_employee_id)
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Covering Employee') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $leave->coveringEmployee?->full_name ?? trans('messages.Not Specified') }}</p>
                    </div>
                    @endif
                    
                    @if($leave->emergency_contact)
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Emergency Contact') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $leave->emergency_contact }}</p>
                    </div>
                    @endif
                    
                    @if($leave->handover_notes)
                    <div class="col-12 mb-3">
                        <label class="form-label font-weight-bold text-gray-700">{{ trans('messages.Handover Notes') }}:</label>
                        <p class="text-gray-800 mb-0">{{ $leave->handover_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-comments mr-2"></i>{{ trans('messages.Comments & Discussion') }}
                    <span class="badge badge-secondary ml-2" id="comments-count">{{ $leave->comments->count() }}</span>
                </h6>
            </div>
            <div class="card-body">
                <!-- Comments List -->
                <div id="comments-container" style="max-height: 400px; overflow-y: auto;">
                    <!-- Comments will be loaded here via AJAX -->
                </div>
                
                <!-- Add Comment Form -->
                <hr>
                <form id="add-comment-form">
                    @csrf
                    <div class="form-group">
                        <label for="comment">{{ trans('messages.Add Comment') }}</label>
                        <textarea name="comment" id="comment" rows="3" 
                                  class="form-control" 
                                  placeholder="{{ trans('messages.Write your comment here') }}" required></textarea>
                    </div>
                    
                    @if(auth()->user()->hasRole(['Admin', 'Manager']))
                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="is_internal" value="1" 
                                   class="custom-control-input" id="is_internal">
                            <label class="custom-control-label" for="is_internal">
                                {{ trans('messages.Internal Comment') }} ({{ trans('messages.Visible to managers only') }})
                            </label>
                        </div>
                    </div>
                    @endif
                    
                    <div class="text-right">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-comment mr-2"></i>{{ trans('messages.Add Comment') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-4">
        <!-- Employee Information -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-user mr-2"></i>{{ trans('messages.Employee Information') }}
                </h6>
            </div>
            <div class="card-body text-center">
                @if($leave->employee?->profile_photo)
                    <img src="{{ Storage::url($leave->employee->profile_photo) }}" 
                         alt="{{ $leave->employee->full_name }}" 
                         class="img-fluid rounded-circle mb-3" 
                         style="width: 100px; height: 100px;">
                @else
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 100px; height: 100px; font-size: 32px;">
                        {{ $leave->employee ? substr($leave->employee->first_name, 0, 1) . substr($leave->employee->last_name, 0, 1) : '?' }}
                    </div>
                @endif
                
                <h5 class="font-weight-bold">{{ $leave->employee?->full_name ?? 'Unknown Employee' }}</h5>
                <p class="text-muted mb-1">{{ $leave->employee?->job_title ?? 'N/A' }}</p>
                <p class="text-muted mb-3">{{ $leave->employee?->employee_id ?? 'N/A' }}</p>
                
                <div class="text-left">
                    <p class="mb-1"><strong>{{ trans('messages.Department') }}:</strong> {{ $leave->employee ? trans('messages.' . ucfirst(str_replace('_', ' ', $leave->employee->department))) : 'N/A' }}</p>
                    <p class="mb-1"><strong>{{ trans('messages.Branch') }}:</strong> {{ $leave->employee?->branch?->name ?? trans('messages.No Branch') }}</p>
                    <p class="mb-1"><strong>{{ trans('messages.Manager') }}:</strong> {{ $leave->employee?->manager?->full_name ?? trans('messages.No Manager') }}</p>
                </div>
            </div>
        </div>

        <!-- Status Card -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-secondary">
                    <i class="fas fa-info-circle mr-2"></i>{{ trans('messages.Request Status') }}
                </h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <span class="badge {{ $leave->status_badge_class }} badge-lg p-3">
                        <i class="fas fa-{{ $leave->status === 'approved' ? 'check-circle' : 
                                           ($leave->status === 'rejected' ? 'times-circle' : 
                                           ($leave->status === 'cancelled' ? 'ban' : 'clock')) }} mr-2"></i>
                        {{ trans('messages.' . ucfirst($leave->status)) }}
                    </span>
                </div>
                
                <div class="mb-2">
                    <strong>{{ trans('messages.Submitted') }}:</strong><br>
                    {{ $leave->created_at->format('Y-m-d H:i') }}
                </div>
                
                @if($leave->approved_at)
                <div class="mb-2">
                    <strong>{{ trans('messages.Approved By') }}:</strong><br>
                    {{ $leave->approvedBy->name }}<br>
                    <small class="text-muted">{{ $leave->approved_at->format('Y-m-d H:i') }}</small>
                </div>
                @endif
                
                @if($leave->rejected_at)
                <div class="mb-2">
                    <strong>{{ trans('messages.Rejected By') }}:</strong><br>
                    {{ $leave->rejectedBy->name }}<br>
                    <small class="text-muted">{{ $leave->rejected_at->format('Y-m-d H:i') }}</small>
                </div>
                @endif
                
                @if($leave->cancelled_at)
                <div class="mb-2">
                    <strong>{{ trans('messages.Cancelled By') }}:</strong><br>
                    {{ $leave->cancelledBy->name }}<br>
                    <small class="text-muted">{{ $leave->cancelled_at->format('Y-m-d H:i') }}</small>
                </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        @can('leaves.approve')
        @if($leave->status === 'pending' && auth()->user()->user_type !== 'employee')
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">
                    <i class="fas fa-gavel mr-2"></i>{{ trans('messages.Manager Actions') }}
                </h6>
            </div>
            <div class="card-body text-center">
                <button type="button" class="btn btn-success btn-lg btn-block mb-2" 
                        onclick="approveLeave({{ $leave->id }})">
                    <i class="fas fa-check mr-2"></i>{{ trans('messages.Approve Leave') }}
                </button>
                
                <button type="button" class="btn btn-danger btn-lg btn-block" 
                        onclick="rejectLeave({{ $leave->id }})">
                    <i class="fas fa-times mr-2"></i>{{ trans('messages.Reject Leave') }}
                </button>
            </div>
        </div>
        @endif
        @endcan
        
        @if($leave->can_be_cancelled)
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-secondary">
                    <i class="fas fa-ban mr-2"></i>{{ trans('messages.Employee Actions') }}
                </h6>
            </div>
            <div class="card-body text-center">
                <button type="button" class="btn btn-warning btn-block" 
                        onclick="cancelLeave({{ $leave->id }})">
                    <i class="fas fa-ban mr-2"></i>{{ trans('messages.Cancel Request') }}
                </button>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Approval/Rejection/Cancel Modals -->
@include('leaves.partials.modals')
@endsection

@push('scripts')
<script>
let currentLeaveId = {{ $leave->id }};

$(document).ready(function() {
    loadComments();
    
    // تحديث التعليقات كل 30 ثانية
    setInterval(loadComments, 30000);
});

// تحميل التعليقات
function loadComments() {
    $.get(`/leaves/${currentLeaveId}/comments`)
        .done(function(response) {
            if (response.success) {
                displayComments(response.comments);
                $('#comments-count').text(response.comments.length);
            }
        })
        .fail(function(xhr) {
            console.error('Error loading comments:', xhr.responseJSON);
        });
}

// عرض التعليقات
function displayComments(comments) {
    const container = $('#comments-container');
    container.empty();
    
    if (comments.length === 0) {
        container.html(`
            <div class="text-center text-muted py-3">
                <i class="fas fa-comment fa-2x mb-2"></i>
                <p>{{ trans('messages.No comments yet') }}</p>
            </div>
        `);
        return;
    }
    
    comments.forEach(function(comment) {
        const commentHtml = `
            <div class="comment-item mb-3 p-3 ${comment.is_system_generated ? 'bg-light' : 'border'} rounded" data-comment-id="${comment.id}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-2">
                            <strong class="mr-2">${comment.user_name}</strong>
                            <span class="badge badge-${comment.comment_type === 'approval' ? 'success' : 
                                                     (comment.comment_type === 'rejection' ? 'danger' : 'secondary')} badge-sm">
                                ${comment.comment_type}
                            </span>
                            ${comment.is_system_generated ? '<span class="badge badge-info badge-sm ml-1">System</span>' : ''}
                            ${comment.visibility !== 'public' ? '<span class="badge badge-warning badge-sm ml-1">' + comment.visibility + '</span>' : ''}
                        </div>
                        <div class="comment-text">${comment.comment}</div>
                        <div class="text-muted small mt-2">
                            ${comment.created_at}
                            ${comment.is_edited ? ' • <em>{{ trans('messages.Edited') }} ' + comment.edited_at + '</em>' : ''}
                        </div>
                    </div>
                    <div class="comment-actions">
                        ${comment.can_be_edited ? `<button type="button" class="btn btn-sm btn-outline-warning mr-1" onclick="editComment(${comment.id})"><i class="fas fa-edit"></i></button>` : ''}
                        ${comment.can_be_deleted ? `<button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteComment(${comment.id})"><i class="fas fa-trash"></i></button>` : ''}
                    </div>
                </div>
            </div>
        `;
        container.append(commentHtml);
    });
    
    // التمرير لأسفل
    container.scrollTop(container[0].scrollHeight);
}

// إضافة تعليق
$('#add-comment-form').on('submit', function(e) {
    e.preventDefault();
    
    const comment = $('#comment').val().trim();
    if (!comment) return;
    
    const submitBtn = $(this).find('button[type="submit"]');
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>{{ trans('messages.Adding') }}...');
    
    $.post(`/leaves/${currentLeaveId}/comments`, {
        _token: '{{ csrf_token() }}',
        comment: comment,
        is_internal: $('#is_internal').is(':checked')
    })
    .done(function(response) {
        if (response.success) {
            $('#comment').val('');
            $('#is_internal').prop('checked', false);
            loadComments();
        }
    })
    .fail(function(xhr) {
        alert('Error: ' + xhr.responseJSON.message);
    })
    .always(function() {
        submitBtn.prop('disabled', false).html('<i class="fas fa-comment mr-2"></i>{{ trans('messages.Add Comment') }}');
    });
});

// الموافقة على الإجازة
function approveLeave(leaveId) {
    $('#approveModal').modal('show');
}

// رفض الإجازة
function rejectLeave(leaveId) {
    $('#rejectModal').modal('show');
}

// إلغاء الإجازة
function cancelLeave(leaveId) {
    $('#cancelModal').modal('show');
}

// تعديل تعليق
function editComment(commentId) {
    // سيتم تنفيذها لاحقاً
    console.log('Edit comment:', commentId);
}

// حذف تعليق
function deleteComment(commentId) {
    if (confirm('{{ trans('messages.Are you sure you want to delete this comment?') }}')) {
        $.ajax({
            url: `/leave-comments/${commentId}`,
            method: 'DELETE',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    loadComments();
                }
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseJSON.message);
            }
        });
    }
}
</script>
@endpush
