<?php

/**
 * Author: Eng.Fahed
 * Payroll Controller - HR System
 * تحكم في عمليات إدارة الرواتب
 */

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'tenant']);
        $this->middleware('can:payrolls.view')->only(['index', 'show']);
        $this->middleware('can:payrolls.create')->only(['create', 'store']);
        $this->middleware('can:payrolls.edit')->only(['edit', 'update']);
        $this->middleware('can:payrolls.delete')->only(['destroy']);
    }

    /**
     * عرض قائمة الرواتب
     */
    public function index(Request $request): View
    {
        $query = Payroll::with(['employee'])
                        ->where('tenant_id', auth()->user()->tenant_id);

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->get('employee_id'));
        }

        if ($request->filled('year')) {
            $query->where('pay_year', $request->get('year'));
        }

        if ($request->filled('month')) {
            $query->where('pay_month', $request->get('month'));
        }

        if ($request->filled('status')) {
            $query->where('payment_status', $request->get('status'));
        }

        $payrolls = $query->orderBy('pay_date', 'desc')
                         ->paginate(15)
                         ->withQueryString();

        $currentYear = date('Y');
        $currentMonth = date('n');
        
        $stats = [
            'total_this_month' => Payroll::where('tenant_id', auth()->user()->tenant_id)
                                        ->forPeriod($currentYear, $currentMonth)
                                        ->count(),
            'paid_this_month' => Payroll::where('tenant_id', auth()->user()->tenant_id)
                                       ->forPeriod($currentYear, $currentMonth)
                                       ->paid()
                                       ->count(),
            'pending_this_month' => Payroll::where('tenant_id', auth()->user()->tenant_id)
                                          ->forPeriod($currentYear, $currentMonth)
                                          ->pending()
                                          ->count(),
            'total_amount_this_month' => Payroll::where('tenant_id', auth()->user()->tenant_id)
                                               ->forPeriod($currentYear, $currentMonth)
                                               ->sum('net_salary'),
        ];

        $employees = Employee::where('tenant_id', auth()->user()->tenant_id)
                            ->where('employment_status', 'active')
                            ->get();
        
        $years = range(date('Y') - 2, date('Y') + 1);
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        $statuses = Payroll::getPaymentStatuses();

        return view('payrolls.index', compact(
            'payrolls', 'stats', 'employees', 'years', 'months', 'statuses'
        ));
    }

    /**
     * عرض صفحة إنشاء راتب جديد
     */
    public function create(Request $request): View
    {
        $employees = Employee::where('tenant_id', auth()->user()->tenant_id)
                            ->where('employment_status', 'active')
                            ->get();

        $selectedEmployee = null;
        if ($request->filled('employee_id')) {
            $selectedEmployee = Employee::find($request->get('employee_id'));
        }

        $paymentMethods = Payroll::getPaymentMethods();
        $currentYear = date('Y');
        $currentMonth = date('n');

        return view('payrolls.create', compact(
            'employees', 'selectedEmployee', 'paymentMethods', 'currentYear', 'currentMonth'
        ));
    }

    /**
     * حفظ راتب جديد
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'pay_year' => 'required|integer|min:2020|max:2030',
            'pay_month' => 'required|integer|min:1|max:12',
            'basic_salary' => 'required|numeric|min:0',
            'housing_allowance' => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'food_allowance' => 'nullable|numeric|min:0',
            'working_days' => 'required|integer|min:1|max:31',
            'attended_days' => 'required|integer|min:0|max:31',
            'payment_method' => 'required|in:bank_transfer,cash,cheque',
        ]);

        try {
            DB::beginTransaction();

            $existingPayroll = Payroll::where('employee_id', $validated['employee_id'])
                                     ->where('pay_year', $validated['pay_year'])
                                     ->where('pay_month', $validated['pay_month'])
                                     ->first();

            if ($existingPayroll) {
                return redirect()->back()
                               ->withInput()
                               ->with('error', 'Payroll already exists for this employee and period');
            }

            $payPeriodStart = Carbon::create($validated['pay_year'], $validated['pay_month'], 1);
            $payPeriodEnd = $payPeriodStart->copy()->endOfMonth();
            $payDate = $payPeriodEnd->copy()->addDays(5);

            $validated['pay_period_start'] = $payPeriodStart;
            $validated['pay_period_end'] = $payPeriodEnd;
            $validated['pay_date'] = $payDate;
            $validated['currency'] = 'AED';
            $validated['payment_status'] = 'pending';

            $payroll = Payroll::create($validated);

            DB::commit();

            return redirect()->route('payrolls.index')
                           ->with('success', trans('messages.Payroll created successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error creating payroll: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل راتب محدد
     */
    public function show(Payroll $payroll): View
    {
        $payroll->load(['employee']);
        return view('payrolls.show', compact('payroll'));
    }

    /**
     * عرض صفحة تعديل الراتب
     */
    public function edit(Payroll $payroll): View
    {
        if ($payroll->isPaid()) {
            return redirect()->route('payrolls.show', $payroll)
                           ->with('error', 'Cannot edit paid payroll');
        }

        $employees = Employee::where('tenant_id', auth()->user()->tenant_id)
                            ->where('employment_status', 'active')
                            ->get();
        $paymentMethods = Payroll::getPaymentMethods();

        return view('payrolls.edit', compact('payroll', 'employees', 'paymentMethods'));
    }

    /**
     * تحديث بيانات الراتب
     */
    public function update(Request $request, Payroll $payroll): RedirectResponse
    {
        if ($payroll->isPaid()) {
            return redirect()->route('payrolls.show', $payroll)
                           ->with('error', 'Cannot edit paid payroll');
        }

        $validated = $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'housing_allowance' => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'food_allowance' => 'nullable|numeric|min:0',
            'working_days' => 'required|integer|min:1|max:31',
            'attended_days' => 'required|integer|min:0|max:31',
            'payment_method' => 'required|in:bank_transfer,cash,cheque',
        ]);

        try {
            DB::beginTransaction();
            
            $payroll->update($validated);
            
            DB::commit();

            return redirect()->route('payrolls.show', $payroll)
                           ->with('success', trans('messages.Payroll updated successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Error updating payroll: ' . $e->getMessage());
        }
    }

    /**
     * حذف الراتب
     */
    public function destroy(Payroll $payroll): RedirectResponse
    {
        if ($payroll->isPaid()) {
            return redirect()->route('payrolls.index')
                           ->with('error', 'Cannot delete paid payroll');
        }

        try {
            $payroll->delete();

            return redirect()->route('payrolls.index')
                           ->with('success', trans('messages.Payroll deleted successfully'));

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error deleting payroll: ' . $e->getMessage());
        }
    }

    /**
     * تسديد الراتب
     */
    public function markAsPaid(Request $request, Payroll $payroll): RedirectResponse
    {
        try {
            $payroll->markAsPaid($request->get('payment_reference'));

            return redirect()->route('payrolls.show', $payroll)
                           ->with('success', 'Payroll marked as paid successfully');

        } catch (\Exception $e) {
            return redirect()->back()
                           ->with('error', 'Error marking payroll as paid: ' . $e->getMessage());
        }
    }
}