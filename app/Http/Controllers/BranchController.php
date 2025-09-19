<?php

/**
 * Author: Eng.Fahed
 * Branch Controller for HR System
 * كونترولر إدارة الفروع لنظام الموارد البشرية
 */

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    /**
     * عرض قائمة الفروع
     */
    public function index(Request $request): View
    {
        $query = Branch::with('tenant');

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('manager_name', 'like', "%{$search}%");
            });
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $branches = $query->paginate(10);

        return view('branches.index', compact('branches'));
    }

    /**
     * إظهار صفحة إنشاء فرع جديد
     */
    public function create(): View
    {
        return view('branches.create');
    }

    /**
     * حفظ فرع جديد
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'location' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'working_hours_start' => 'nullable|string',
            'working_hours_end' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // تحضير ساعات العمل
            $workingHours = null;
            if ($request->filled('working_hours_start') && $request->filled('working_hours_end')) {
                $workingHours = [
                    'start' => $request->working_hours_start,
                    'end' => $request->working_hours_end,
                ];
            }

            // إنشاء الفرع
            Branch::create([
                'name' => $validated['name'],
                'address' => $validated['address'],
                'location' => $validated['location'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'manager_name' => $validated['manager_name'],
                'description' => $validated['description'],
                'is_active' => $request->boolean('is_active', true),
                'working_hours' => $workingHours,
                'tenant_id' => auth()->user()->tenant_id,
            ]);

            DB::commit();

            return redirect()->route('branches.index')
                ->with('success', 'Branch created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating branch: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل فرع محدد
     */
    public function show(Branch $branch): View
    {
        $branch->load('tenant');
        
        return view('branches.show', compact('branch'));
    }

    /**
     * إظهار صفحة تعديل الفرع
     */
    public function edit(Branch $branch): View
    {
        return view('branches.edit', compact('branch'));
    }

    /**
     * تحديث الفرع
     */
    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'location' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'manager_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'working_hours_start' => 'nullable|string',
            'working_hours_end' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // تحضير ساعات العمل
            $workingHours = null;
            if ($request->filled('working_hours_start') && $request->filled('working_hours_end')) {
                $workingHours = [
                    'start' => $request->working_hours_start,
                    'end' => $request->working_hours_end,
                ];
            }

            // تحديث الفرع
            $branch->update([
                'name' => $validated['name'],
                'address' => $validated['address'],
                'location' => $validated['location'],
                'phone' => $validated['phone'],
                'email' => $validated['email'],
                'manager_name' => $validated['manager_name'],
                'description' => $validated['description'],
                'is_active' => $request->boolean('is_active', true),
                'working_hours' => $workingHours,
            ]);

            DB::commit();

            return redirect()->route('branches.index')
                ->with('success', 'Branch updated successfully');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating branch: ' . $e->getMessage());
        }
    }

    /**
     * حذف الفرع
     */
    public function destroy(Branch $branch): RedirectResponse
    {
        try {
            $branch->delete();
            
            return redirect()->route('branches.index')
                ->with('success', 'Branch deleted successfully');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting branch: ' . $e->getMessage());
        }
    }
}