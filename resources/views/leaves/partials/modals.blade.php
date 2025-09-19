{{-- 
Author: Eng.Fahed
Leave Modals Partial - HR System
نوافذ الموافقة والرفض والإلغاء
--}}

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

<script>
// معالجة الموافقة
$('#approveForm').on('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = $(this).find('button[type="submit"]');
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>{{ trans('messages.Processing') }}...');
    
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
        },
        complete: function() {
            submitBtn.prop('disabled', false).html('{{ trans('messages.Approve Leave') }}');
        }
    });
});

// معالجة الرفض
$('#rejectForm').on('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = $(this).find('button[type="submit"]');
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>{{ trans('messages.Processing') }}...');
    
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
        },
        complete: function() {
            submitBtn.prop('disabled', false).html('{{ trans('messages.Reject Leave') }}');
        }
    });
});

// معالجة الإلغاء
$('#cancelForm').on('submit', function(e) {
    e.preventDefault();
    
    const submitBtn = $(this).find('button[type="submit"]');
    submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>{{ trans('messages.Processing') }}...');
    
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
        },
        complete: function() {
            submitBtn.prop('disabled', false).html('{{ trans('messages.Cancel Leave') }}');
        }
    });
});
</script>
