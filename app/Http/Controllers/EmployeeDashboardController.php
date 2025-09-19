<?php

/**
 * Author: Eng.Fahed
 * Employee Dashboard Controller - HR System
 * dashboard خاص للموظف يعرض بياناته وإحصائياته فقط
 */

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Payroll;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class EmployeeDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'tenant']);
    }

    /**
     * عرض dashboard الموظف
     */
    public function index(): View
    {
        $user = auth()->user();
        
        // البحث عن بيانات الموظف
        $employee = Employee::where('user_id', $user->id)
                           ->where('tenant_id', $user->tenant_id)
                           ->with(['branch', 'manager', 'payrolls' => function($query) {
                               $query->latest('pay_date')->take(6);
                           }])
                           ->first();

        if (!$employee) {
            return view('employee-dashboard.no-employee');
        }

        // إحصائيات الموظف
        $currentYear = date('Y');
        $currentMonth = date('n');
        
        $stats = [
            'years_of_service' => $employee->years_of_service,
            'total_salary' => $employee->total_salary,
            'contract_status' => $employee->contract_status,
            'document_alerts' => count($employee->document_expiry_alerts),
            
            // إحصائيات الرواتب
            'payrolls_this_year' => $employee->payrolls()
                                             ->where('pay_year', $currentYear)
                                             ->count(),
            'total_earned_this_year' => $employee->payrolls()
                                                 ->where('pay_year', $currentYear)
                                                 ->where('payment_status', 'paid')
                                                 ->sum('net_salary'),
            'pending_payrolls' => $employee->payrolls()
                                           ->where('payment_status', 'pending')
                                           ->count(),
            'last_payroll_amount' => optional($employee->getLatestPayroll())->net_salary ?? 0,
        ];

        // الراتب الحالي للشهر
        $currentPayroll = $employee->getPayrollForMonth($currentYear, $currentMonth);

        // التنبيهات
        $alerts = $employee->document_expiry_alerts;
        
        // إضافة تنبيه العقد إذا كان قريب الانتهاء
        if ($employee->contract_end_date && $employee->contract_end_date->diffInDays(now(), false) <= 30) {
            $alerts[] = [
                'type' => 'contract',
                'message' => 'Your contract expires on ' . $employee->contract_end_date->format('Y-m-d'),
                'days_left' => $employee->contract_end_date->diffInDays(now(), false),
                'urgency' => $employee->contract_end_date->diffInDays(now(), false) <= 7 ? 'high' : 'medium'
            ];
        }

        // الرواتب الأخيرة
        $recentPayrolls = $employee->payrolls()
                                  ->with(['approvedBy', 'processedBy'])
                                  ->latest('pay_date')
                                  ->take(6)
                                  ->get();

        // إحصائيات شهرية للرسم البياني
        $monthlyEarnings = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $amount = $employee->payrolls()
                              ->where('pay_year', $date->year)
                              ->where('pay_month', $date->month)
                              ->where('payment_status', 'paid')
                              ->sum('net_salary');
            
            $monthlyEarnings[] = [
                'month' => $date->format('M Y'),
                'amount' => $amount
            ];
        }

        return view('employee-dashboard.index', compact(
            'employee', 'stats', 'currentPayroll', 'alerts', 'recentPayrolls', 'monthlyEarnings'
        ));
    }

    /**
     * عرض الملف الشخصي للموظف
     */
    public function profile(): View
    {
        $user = auth()->user();
        
        $employee = Employee::where('user_id', $user->id)
                           ->where('tenant_id', $user->tenant_id)
                           ->with(['branch', 'manager'])
                           ->first();

        if (!$employee) {
            return view('employee-dashboard.no-employee');
        }

        return view('employee-dashboard.profile', compact('employee'));
    }

    /**
     * عرض الرواتب الخاصة بالموظف
     */
    public function payrolls(Request $request): View
    {
        $user = auth()->user();
        
        $employee = Employee::where('user_id', $user->id)
                           ->where('tenant_id', $user->tenant_id)
                           ->first();

        if (!$employee) {
            return view('employee-dashboard.no-employee');
        }

        $query = $employee->payrolls()->with(['approvedBy', 'processedBy']);

        // فلترة حسب السنة
        if ($request->filled('year')) {
            $query->where('pay_year', $request->get('year'));
        }

        // فلترة حسب الشهر
        if ($request->filled('month')) {
            $query->where('pay_month', $request->get('month'));
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('payment_status', $request->get('status'));
        }

        $payrolls = $query->orderBy('pay_date', 'desc')
                         ->paginate(12)
                         ->withQueryString();

        // الإحصائيات
        $stats = [
            'total_payrolls' => $employee->payrolls()->count(),
            'paid_payrolls' => $employee->payrolls()->paid()->count(),
            'pending_payrolls' => $employee->payrolls()->pending()->count(),
            'total_earned' => $employee->payrolls()->paid()->sum('net_salary'),
            'average_salary' => $employee->payrolls()->paid()->avg('net_salary'),
        ];

        $years = $employee->payrolls()->distinct()->pluck('pay_year')->sort()->values();
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        $statuses = Payroll::getPaymentStatuses();

        return view('employee-dashboard.payrolls', compact(
            'employee', 'payrolls', 'stats', 'years', 'months', 'statuses'
        ));
    }

    /**
     * عرض تفاصيل راتب محدد
     */
    public function payrollDetails(Payroll $payroll): View
    {
        $user = auth()->user();
        
        $employee = Employee::where('user_id', $user->id)
                           ->where('tenant_id', $user->tenant_id)
                           ->first();

        if (!$employee || $payroll->employee_id !== $employee->id) {
            abort(403, 'Unauthorized access to payroll details');
        }

        $payroll->load(['employee', 'approvedBy', 'processedBy']);

        return view('employee-dashboard.payroll-details', compact('payroll', 'employee'));
    }

    /**
     * عرض المستندات الشخصية
     */
    public function documents(): View
    {
        $user = auth()->user();
        
        $employee = Employee::where('user_id', $user->id)
                           ->where('tenant_id', $user->tenant_id)
                           ->first();

        if (!$employee) {
            return view('employee-dashboard.no-employee');
        }

        // تجميع المستندات مع معلومات الانتهاء
        $documents = [
            'passport' => [
                'name' => 'Passport',
                'number' => $employee->passport_number,
                'expiry' => $employee->passport_expiry,
                'file' => $employee->passport_copy,
                'status' => $employee->passport_expiry && $employee->passport_expiry->isPast() ? 'expired' : 
                           ($employee->passport_expiry && $employee->passport_expiry->diffInDays(now(), false) <= 90 ? 'expiring' : 'valid')
            ],
            'visa' => [
                'name' => 'Visa/Residence',
                'number' => $employee->visa_number,
                'expiry' => $employee->visa_expiry,
                'file' => $employee->visa_copy,
                'status' => $employee->visa_expiry && $employee->visa_expiry->isPast() ? 'expired' : 
                           ($employee->visa_expiry && $employee->visa_expiry->diffInDays(now(), false) <= 30 ? 'expiring' : 'valid')
            ],
            'emirates_id' => [
                'name' => 'Emirates ID',
                'number' => $employee->emirates_id,
                'expiry' => $employee->emirates_id_expiry,
                'file' => $employee->emirates_id_copy,
                'status' => $employee->emirates_id_expiry && $employee->emirates_id_expiry->isPast() ? 'expired' : 
                           ($employee->emirates_id_expiry && $employee->emirates_id_expiry->diffInDays(now(), false) <= 30 ? 'expiring' : 'valid')
            ],
            'contract' => [
                'name' => 'Employment Contract',
                'number' => $employee->employee_id,
                'expiry' => $employee->contract_end_date,
                'file' => $employee->contract_copy,
                'status' => $employee->contract_end_date && $employee->contract_end_date->isPast() ? 'expired' : 
                           ($employee->contract_end_date && $employee->contract_end_date->diffInDays(now(), false) <= 30 ? 'expiring' : 'valid')
            ],
        ];

        return view('employee-dashboard.documents', compact('employee', 'documents'));
    }

    /**
     * عرض الإشعارات
     */
    public function notifications(): View
    {
        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)
                           ->where('tenant_id', $user->tenant_id)
                           ->first();

        if (!$employee) {
            return view('employee-dashboard.no-employee');
        }

        $notifications = $employee->notifications()
                                 ->notExpired()
                                 ->orderBy('created_at', 'desc')
                                 ->paginate(20);

        // إحصائيات الإشعارات
        $stats = [
            'total' => $employee->notifications()->count(),
            'unread' => $employee->notifications()->unread()->count(),
            'urgent' => $employee->notifications()->where('priority', 'urgent')->unread()->count(),
            'requires_action' => $employee->notifications()->requiresAction()->unread()->count(),
        ];

        return view('employee-dashboard.notifications', compact('employee', 'notifications', 'stats'));
    }

    /**
     * تحديد إشعار كمقروء
     */
    public function markNotificationAsRead($notificationId): JsonResponse
    {
        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
        }

        $notification = $employee->notifications()->find($notificationId);
        
        if (!$notification) {
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * تحديد جميع الإشعارات كمقروءة
     */
    public function markAllNotificationsAsRead(): JsonResponse
    {
        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
        }

        EmployeeNotification::markAllAsReadForEmployee($employee->id);

        return response()->json(['success' => true]);
    }

    /**
     * الحصول على عدد الإشعارات غير المقروءة
     */
    public function getUnreadNotificationsCount(): JsonResponse
    {
        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            return response()->json(['count' => 0]);
        }

        $count = $employee->getUnreadNotificationsCount();

        return response()->json(['count' => $count]);
    }

    /**
     * عرض رصيد الإجازات
     */
    public function leaveBalance(): View
    {
        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)
                           ->where('tenant_id', $user->tenant_id)
                           ->first();

        if (!$employee) {
            return view('employee-dashboard.no-employee');
        }

        $leaveBalances = $employee->getAllLeaveBalances();
        
        // تاريخ الإجازات هذا العام
        $currentYear = date('Y');
        $leaveHistory = $employee->leaves()
                               ->whereYear('start_date', $currentYear)
                               ->orderBy('start_date', 'desc')
                               ->get()
                               ->groupBy('leave_type');

        return view('employee-dashboard.leave-balance', compact('employee', 'leaveBalances', 'leaveHistory'));
    }
}