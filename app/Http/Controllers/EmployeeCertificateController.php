<?php

/**
 * Author: Eng.Fahed
 * Employee Certificate Controller - HR System
 * تحكم في طلبات شهادات الموظفين
 */

namespace App\Http\Controllers;

use App\Models\EmployeeCertificate;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class EmployeeCertificateController extends Controller
{
    /**
     * عرض قائمة الشهادات للموظف
     */
    public function index(Request $request)
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        
        if (!$employee) {
            return redirect()->route('dashboard')
                           ->with('error', 'Employee profile not found');
        }

        $query = EmployeeCertificate::where('employee_id', $employee->id)
                                  ->with(['processedBy']);

        // فلترة حسب النوع
        if ($request->filled('type')) {
            $query->where('certificate_type', $request->get('type'));
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // فلترة حسب التاريخ
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->get('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->get('date_to'));
        }

        $certificates = $query->orderBy('created_at', 'desc')
                             ->paginate(15)
                             ->withQueryString();

        // إحصائيات
        $stats = [
            'total' => EmployeeCertificate::where('employee_id', $employee->id)->count(),
            'pending' => EmployeeCertificate::where('employee_id', $employee->id)->pending()->count(),
            'completed' => EmployeeCertificate::where('employee_id', $employee->id)->completed()->count(),
            'this_month' => EmployeeCertificate::where('employee_id', $employee->id)
                                              ->whereMonth('created_at', date('m'))
                                              ->count()
        ];

        $certificateTypes = EmployeeCertificate::getCertificateTypes();
        $statuses = EmployeeCertificate::getStatuses();

        return view('employee-dashboard.certificates.index', compact(
            'employee', 'certificates', 'stats', 'certificateTypes', 'statuses'
        ));
    }

    /**
     * عرض نموذج طلب شهادة جديدة
     */
    public function create()
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        
        if (!$employee) {
            return redirect()->route('dashboard')
                           ->with('error', 'Employee profile not found');
        }

        $certificateTypes = EmployeeCertificate::getCertificateTypes();
        $priorities = EmployeeCertificate::getPriorities();

        return view('employee-dashboard.certificates.create', compact(
            'employee', 'certificateTypes', 'priorities'
        ));
    }

    /**
     * حفظ طلب شهادة جديد
     */
    public function store(Request $request): RedirectResponse
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        
        if (!$employee) {
            return redirect()->route('dashboard')
                           ->with('error', 'Employee profile not found');
        }

        $validated = $request->validate([
            'certificate_type' => 'required|in:' . implode(',', array_keys(EmployeeCertificate::getCertificateTypes())),
            'purpose' => 'required|string|max:255',
            'additional_details' => 'nullable|string|max:1000',
            'special_requirements' => 'nullable|string|max:500',
            'priority' => 'required|in:' . implode(',', array_keys(EmployeeCertificate::getPriorities())),
            
            // حقول الشهادة المرضية
            'medical_start_date' => 'nullable|required_if:certificate_type,medical_leave_certificate|date',
            'medical_end_date' => 'nullable|required_if:certificate_type,medical_leave_certificate|date|after_or_equal:medical_start_date',
            'medical_diagnosis' => 'nullable|required_if:certificate_type,medical_leave_certificate|string|max:255',
            'doctor_name' => 'nullable|string|max:255',
            'hospital_name' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $certificate = EmployeeCertificate::create([
                'employee_id' => $employee->id,
                'tenant_id' => $employee->tenant_id,
                'requested_by' => auth()->id(),
                'certificate_type' => $validated['certificate_type'],
                'purpose' => $validated['purpose'],
                'additional_details' => $validated['additional_details'],
                'special_requirements' => $validated['special_requirements'],
                'priority' => $validated['priority'],
                'medical_start_date' => $validated['medical_start_date'] ?? null,
                'medical_end_date' => $validated['medical_end_date'] ?? null,
                'medical_diagnosis' => $validated['medical_diagnosis'] ?? null,
                'doctor_name' => $validated['doctor_name'] ?? null,
                'hospital_name' => $validated['hospital_name'] ?? null,
                'status' => 'pending'
            ]);

            // إرسال إشعار للـ HR
            $hrUsers = \App\Models\User::whereHas('roles', function($query) {
                $query->whereIn('name', ['Admin', 'Manager']);
            })->where('tenant_id', $employee->tenant_id)->get();

            foreach ($hrUsers as $hrUser) {
                \App\Models\EmployeeNotification::create([
                    'employee_id' => $employee->id,
                    'tenant_id' => $employee->tenant_id,
                    'created_by' => auth()->id(),
                    'type' => 'certificate_request',
                    'title' => 'New Certificate Request',
                    'message' => "{$employee->full_name} has requested a {$certificate->type_display_name}",
                    'priority' => $validated['priority'] === 'urgent' ? 'high' : 'normal',
                    'requires_action' => true,
                    'action_url' => route('certificates.show', $certificate),
                    'action_text' => 'Review Request'
                ]);
            }

            DB::commit();

            return redirect()->route('employee-dashboard.certificates.show', $certificate)
                           ->with('success', 'Certificate request submitted successfully');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error submitting certificate request: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل شهادة
     */
    public function show(EmployeeCertificate $certificate)
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        
        if (!$employee || $certificate->employee_id !== $employee->id) {
            abort(403, 'Unauthorized access to certificate');
        }

        $certificate->load(['processedBy', 'requestedBy']);

        return view('employee-dashboard.certificates.show', compact('certificate', 'employee'));
    }

    /**
     * تحميل الشهادة
     */
    public function download(EmployeeCertificate $certificate)
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        
        if (!$employee || $certificate->employee_id !== $employee->id) {
            abort(403, 'Unauthorized access to certificate');
        }

        if (!$certificate->certificate_file || !Storage::exists($certificate->certificate_file)) {
            return redirect()->back()
                           ->with('error', 'Certificate file not found');
        }

        return Storage::download($certificate->certificate_file, 
            $certificate->certificate_number . '_' . $certificate->type_display_name . '.pdf');
    }

    /**
     * إلغاء طلب الشهادة
     */
    public function cancel(EmployeeCertificate $certificate): JsonResponse
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        
        if (!$employee || $certificate->employee_id !== $employee->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (!$certificate->canBeCancelled()) {
            return response()->json(['success' => false, 'message' => 'Cannot cancel this certificate request'], 400);
        }

        try {
            $certificate->update(['status' => 'cancelled']);

            return response()->json([
                'success' => true,
                'message' => 'Certificate request cancelled successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error cancelling certificate request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: الحصول على إحصائيات الشهادات
     */
    public function getStats(): JsonResponse
    {
        $employee = Employee::where('user_id', auth()->id())->first();
        
        if (!$employee) {
            return response()->json(['error' => 'Employee not found'], 404);
        }

        $stats = [
            'total' => $employee->certificates()->count(),
            'pending' => $employee->certificates()->pending()->count(),
            'approved' => $employee->certificates()->approved()->count(),
            'completed' => $employee->certificates()->completed()->count(),
            'rejected' => $employee->certificates()->where('status', 'rejected')->count(),
        ];

        return response()->json($stats);
    }
}