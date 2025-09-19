<?php

/**
 * Author: Eng.Fahed
 * Leave Controller - HR System
 * تحكم في عمليات إدارة الإجازات مع نظام الموافقات
 */

namespace App\Http\Controllers;

use App\Models\Leave;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class LeaveController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'tenant']);
        $this->middleware('can:leaves.view')->only(['index', 'show']);
        $this->middleware('can:leaves.create')->only(['create', 'store']);
        $this->middleware('can:leaves.edit')->only(['edit', 'update']);
        $this->middleware('can:leaves.delete')->only(['destroy']);
    }

    /**
     * عرض قائمة الإجازات
     */
    public function index(Request $request)
    {
        $query = Leave::with(['employee', 'approvedBy'])
                      ->where('tenant_id', auth()->user()->tenant_id);

        // فلترة حسب نوع المستخدم
        if (auth()->user()->user_type === 'employee') {
            $employee = Employee::where('user_id', auth()->id())->first();
            if ($employee) {
                $query->where('employee_id', $employee->id);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('leave_type')) {
            $query->where('leave_type', $request->get('leave_type'));
        }

        $leaves = $query->orderBy('created_at', 'desc')
                       ->paginate(15)
                       ->withQueryString();

        $stats = [
            'total_this_year' => Leave::where('tenant_id', auth()->user()->tenant_id)
                                    ->currentYear()
                                    ->count(),
            'pending' => Leave::where('tenant_id', auth()->user()->tenant_id)
                              ->pending()
                              ->count(),
            'approved' => Leave::where('tenant_id', auth()->user()->tenant_id)
                               ->approved()
                               ->count(),
        ];

        $employees = Employee::where('tenant_id', auth()->user()->tenant_id)->get();
        $leaveTypes = Leave::getLeaveTypes();
        $statuses = Leave::getStatuses();

        return view('leaves.index', compact('leaves', 'stats', 'employees', 'leaveTypes', 'statuses'));
    }

    /**
     * عرض صفحة تقديم طلب إجازة جديد
     */
    public function create()
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        
        if (!$employee) {
            return redirect()->route('dashboard')
                           ->with('error', 'Employee profile not found. Please contact HR.');
        }

        $employees = Employee::where('tenant_id', auth()->user()->tenant_id)
                            ->where('employment_status', 'active')
                            ->where('id', '!=', $employee->id)
                            ->get();
        
        $leaveTypes = Leave::getLeaveTypes();
        $dayTypes = Leave::getDayTypes();

        return view('leaves.create', compact('employee', 'employees', 'leaveTypes', 'dayTypes'));
    }

    /**
     * حفظ طلب إجازة جديد
     */
    public function store(Request $request): RedirectResponse
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        
        if (!$employee) {
            return redirect()->route('dashboard')
                           ->with('error', 'Employee profile not found.');
        }

        $validated = $request->validate([
            'leave_type' => 'required|in:' . implode(',', array_keys(Leave::getLeaveTypes())),
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'day_type' => 'required|in:full_day,half_day,quarter_day',
            'reason' => 'required|string|max:500',
            'description' => 'nullable|string|max:1000',
            'covering_employee_id' => 'nullable|exists:employees,id',
        ]);

        try {
            DB::beginTransaction();

            $leave = Leave::create([
                'employee_id' => $employee->id,
                'tenant_id' => $employee->tenant_id,
                'leave_type' => $validated['leave_type'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'day_type' => $validated['day_type'],
                'reason' => $validated['reason'],
                'description' => $validated['description'],
                'covering_employee_id' => $validated['covering_employee_id'],
                'status' => 'pending',
            ]);

            DB::commit();

            return redirect()->route('leaves.show', $leave)
                           ->with('success', 'Leave request submitted successfully');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error submitting leave request: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل طلب إجازة
     */
    public function show(Leave $leave)
    {
        try {
        $leave->load(['employee', 'approvedBy', 'rejectedBy', 'cancelledBy', 'coveringEmployee', 'comments.user']);
        
        // التحقق من وجود الموظف
        if (!$leave->employee) {
            return redirect()->route('leaves.index')
                           ->with('error', 'Employee data not found for this leave request');
        }
        
        // التحقق من الصلاحية
        if (auth()->user()->user_type === 'employee') {
            $employee = Employee::where('user_id', auth()->id())->first();
            if (!$employee || $leave->employee_id !== $employee->id) {
                abort(403, 'Unauthorized access to leave request');
            }
        }

        // إعداد البيانات للعرض
        $leaveTypes = [
            'annual' => 'Annual Leave',
            'sick' => 'Sick Leave',
            'emergency' => 'Emergency Leave',
            'maternity' => 'Maternity Leave',
            'paternity' => 'Paternity Leave',
            'study' => 'Study Leave',
            'pilgrimage' => 'Pilgrimage Leave',
            'unpaid' => 'Unpaid Leave'
        ];

        return view('leaves.show', compact('leave', 'leaveTypes'));
    }catch (\Exception $e) {
        return redirect()->route('leaves.index')
                           ->with('error', 'Error showing leave request: ' . $e->getMessage());
    }
    }

    /**
     * تعديل طلب الإجازة
     */
    public function edit(Leave $leave)
    {
        if (!$leave->can_be_edited) {
            return redirect()->route('leaves.show', $leave)
                           ->with('error', 'Cannot edit this leave request');
        }

        $employee = $leave->employee;
        $employees = Employee::where('tenant_id', auth()->user()->tenant_id)
                            ->where('employment_status', 'active')
                            ->where('id', '!=', $employee->id)
                            ->get();
        
        $leaveTypes = Leave::getLeaveTypes();
        $dayTypes = Leave::getDayTypes();

        return view('leaves.edit', compact('leave', 'employee', 'employees', 'leaveTypes', 'dayTypes'));
    }

    /**
     * تحديث طلب الإجازة
     */
    public function update(Request $request, Leave $leave): RedirectResponse
    {
        if (!$leave->can_be_edited) {
            return redirect()->route('leaves.show', $leave)
                           ->with('error', 'Cannot edit this leave request');
        }

        $validated = $request->validate([
            'leave_type' => 'required|in:' . implode(',', array_keys(Leave::getLeaveTypes())),
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'covering_employee_id' => 'nullable|exists:employees,id',
        ]);

        try {
            $leave->update($validated);

            return redirect()->route('leaves.show', $leave)
                           ->with('success', 'Leave request updated successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error updating leave request: ' . $e->getMessage());
        }
    }

    /**
     * الموافقة على طلب الإجازة
     */
    public function approve(Request $request, Leave $leave): JsonResponse
    {
        try {
            $notes = $request->get('notes');
            $leave->approve(auth()->id(), $notes);

            return response()->json([
                'success' => true,
                'message' => 'Leave request approved successfully',
                'status' => 'approved'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving leave request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * رفض طلب الإجازة
     */
    public function reject(Request $request, Leave $leave): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            $leave->reject(auth()->id(), $request->get('reason'));

            return response()->json([
                'success' => true,
                'message' => 'Leave request rejected',
                'status' => 'rejected'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error rejecting leave request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * إلغاء طلب الإجازة
     */
    public function cancel(Request $request, Leave $leave): JsonResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        try {
            $leave->cancel(auth()->id(), $request->get('reason'));

            return response()->json([
                'success' => true,
                'message' => 'Leave request cancelled',
                'status' => 'cancelled'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling leave request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * حذف طلب الإجازة
     */
    public function destroy(Leave $leave): RedirectResponse
    {
        try {
            // حذف التعليقات أولاً
            $leave->comments()->delete();
            
            // حذف الإجازة
            $leave->delete();

            return redirect()->route('leaves.index')
                           ->with('success', 'Leave request deleted successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error deleting leave request: ' . $e->getMessage());
        }
    }
}
