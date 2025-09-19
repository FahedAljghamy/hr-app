<?php

/**
 * Author: Eng.Fahed
 * Employee Controller - HR System
 * تحكم في عمليات إدارة الموظفين
 */

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'tenant']);
        $this->middleware('can:employees.view')->only(['index', 'show']);
        $this->middleware('can:employees.create')->only(['create', 'store']);
        $this->middleware('can:employees.edit')->only(['edit', 'update']);
        $this->middleware('can:employees.delete')->only(['destroy']);
    }

    /**
     * عرض قائمة الموظفين
     */
    public function index(Request $request): View
    {
        $query = Employee::with(['branch', 'manager'])
                         ->where('tenant_id', auth()->user()->tenant_id);

        // البحث والفلترة
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('employment_status', $request->get('status'));
        }

        if ($request->filled('department')) {
            $query->where('department', $request->get('department'));
        }

        $employees = $query->paginate(15)->withQueryString();

        // الإحصائيات
        $stats = [
            'total' => Employee::where('tenant_id', auth()->user()->tenant_id)->count(),
            'active' => Employee::where('tenant_id', auth()->user()->tenant_id)
                               ->where('employment_status', 'active')->count(),
            'contract_expiring' => Employee::where('tenant_id', auth()->user()->tenant_id)
                                          ->contractExpiringSoon(30)->count(),
            'visa_expiring' => Employee::where('tenant_id', auth()->user()->tenant_id)
                                      ->visaExpiringSoon(30)->count(),
        ];

        $branches = Branch::where('tenant_id', auth()->user()->tenant_id)->get();
        $departments = Employee::getDepartments();
        $statuses = Employee::getEmploymentStatuses();

        return view('employees.index', compact(
            'employees', 'stats', 'branches', 'departments', 'statuses'
        ));
    }

    /**
     * عرض صفحة إنشاء موظف جديد
     */
    public function create(): View
    {
        $branches = Branch::where('tenant_id', auth()->user()->tenant_id)->get();
        $managers = Employee::where('tenant_id', auth()->user()->tenant_id)
                           ->where('is_manager', true)
                           ->where('employment_status', 'active')
                           ->get();
        $departments = Employee::getDepartments();
        $employmentTypes = Employee::getEmploymentTypes();
        $employmentStatuses = Employee::getEmploymentStatuses();

        return view('employees.create', compact(
            'branches', 'managers', 'departments', 'employmentTypes', 'employmentStatuses'
        ));
    }

    /**
     * حفظ موظف جديد
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'nationality' => 'required|string|max:100',
            'passport_number' => 'required|string|max:50|unique:employees,passport_number',
            'passport_expiry' => 'required|date|after:today',
            'job_title' => 'required|string|max:200',
            'department' => 'required|string|max:100',
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'hire_date' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        try {
            DB::beginTransaction();
            
            $employee = Employee::create($validated);
            
            DB::commit();

            return redirect()->route('employees.index')
                           ->with('success', trans('messages.Employee created successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error creating employee: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل موظف محدد
     */
    public function show(Employee $employee): View
    {
        $employee->load(['branch', 'manager', 'subordinates']);
        return view('employees.show', compact('employee'));
    }

    /**
     * عرض صفحة تعديل الموظف
     */
    public function edit(Employee $employee): View
    {
        $branches = Branch::where('tenant_id', auth()->user()->tenant_id)->get();
        $managers = Employee::where('tenant_id', auth()->user()->tenant_id)
                           ->where('is_manager', true)
                           ->where('id', '!=', $employee->id)
                           ->get();
        $departments = Employee::getDepartments();
        $employmentTypes = Employee::getEmploymentTypes();
        $employmentStatuses = Employee::getEmploymentStatuses();

        return view('employees.edit', compact(
            'employee', 'branches', 'managers', 'departments', 'employmentTypes', 'employmentStatuses'
        ));
    }

    /**
     * تحديث بيانات الموظف
     */
    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'required|string|max:20',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female',
            'nationality' => 'required|string|max:100',
            'passport_number' => 'required|string|max:50|unique:employees,passport_number,' . $employee->id,
            'passport_expiry' => 'required|date|after:today',
            'job_title' => 'required|string|max:200',
            'department' => 'required|string|max:100',
            'employment_type' => 'required|in:full_time,part_time,contract,intern',
            'hire_date' => 'required|date',
            'basic_salary' => 'required|numeric|min:0',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        try {
            DB::beginTransaction();
            
            $employee->update($validated);
            
            DB::commit();

            return redirect()->route('employees.show', $employee)
                           ->with('success', trans('messages.Employee updated successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error updating employee: ' . $e->getMessage());
        }
    }

    /**
     * حذف الموظف
     */
    public function destroy(Employee $employee): RedirectResponse
    {
        try {
            $employee->delete();

            return redirect()->route('employees.index')
                           ->with('success', trans('messages.Employee deleted successfully'));

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error deleting employee: ' . $e->getMessage());
        }
    }
}
