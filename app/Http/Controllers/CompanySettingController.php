<?php

/**
 * Author: Eng.Fahed
 * Company Settings Controller for HR System
 * كونترولر إعدادات الشركة لنظام الموارد البشرية
 */

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CompanySettingController extends Controller
{
    /**
     * عرض إعدادات الشركة
     */
    public function index(): View
    {
        $setting = CompanySetting::where('tenant_id', auth()->user()->tenant_id)->first();
        
        return view('company-settings.index', compact('setting'));
    }

    /**
     * إظهار صفحة إنشاء/تعديل الإعدادات
     */
    public function create(): View
    {
        $setting = CompanySetting::where('tenant_id', auth()->user()->tenant_id)->first();
        
        if ($setting) {
            return redirect()->route('company-settings.edit', $setting);
        }
        
        return view('company-settings.create');
    }

    /**
     * حفظ إعدادات جديدة
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string',
            'website' => 'nullable|url|max:255',
            'timezone' => 'required|string|max:50',
            'currency' => 'required|string|max:10',
            'description' => 'nullable|string',
            'tax_number' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'working_hours_start' => 'nullable|string',
            'working_hours_end' => 'nullable|string',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'linkedin' => 'nullable|url',
            'instagram' => 'nullable|url',
        ]);

        try {
            DB::beginTransaction();

            // رفع الشعار
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('company-logos', 'public');
            }

            // تحضير ساعات العمل
            $workingHours = null;
            if ($request->filled('working_hours_start') && $request->filled('working_hours_end')) {
                $workingHours = [
                    'start' => $request->working_hours_start,
                    'end' => $request->working_hours_end,
                ];
            }

            // تحضير وسائل التواصل
            $socialMedia = array_filter([
                'facebook' => $request->facebook,
                'twitter' => $request->twitter,
                'linkedin' => $request->linkedin,
                'instagram' => $request->instagram,
            ]);

            // إنشاء الإعدادات
            CompanySetting::create([
                'company_name' => $validated['company_name'],
                'logo_path' => $logoPath,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'website' => $validated['website'],
                'official_working_hours' => $workingHours,
                'timezone' => $validated['timezone'],
                'currency' => $validated['currency'],
                'description' => $validated['description'],
                'social_media' => $socialMedia,
                'tax_number' => $validated['tax_number'],
                'registration_number' => $validated['registration_number'],
                'tenant_id' => auth()->user()->tenant_id,
            ]);

            DB::commit();

            return redirect()->route('company-settings.index')
                ->with('success', 'Company settings created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating settings: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل الإعدادات
     */
    public function show(CompanySetting $companySetting): View
    {
        return view('company-settings.show', compact('companySetting'));
    }

    /**
     * إظهار صفحة تعديل الإعدادات
     */
    public function edit(CompanySetting $companySetting): View
    {
        return view('company-settings.edit', compact('companySetting'));
    }

    /**
     * تحديث الإعدادات
     */
    public function update(Request $request, CompanySetting $companySetting): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string',
            'website' => 'nullable|url|max:255',
            'timezone' => 'required|string|max:50',
            'currency' => 'required|string|max:10',
            'description' => 'nullable|string',
            'tax_number' => 'nullable|string|max:50',
            'registration_number' => 'nullable|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'working_hours_start' => 'nullable|string',
            'working_hours_end' => 'nullable|string',
            'facebook' => 'nullable|url',
            'twitter' => 'nullable|url',
            'linkedin' => 'nullable|url',
            'instagram' => 'nullable|url',
        ]);

        try {
            DB::beginTransaction();

            // رفع الشعار الجديد
            $logoPath = $companySetting->logo_path;
            if ($request->hasFile('logo')) {
                // حذف الشعار القديم
                if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                    Storage::disk('public')->delete($logoPath);
                }
                $logoPath = $request->file('logo')->store('company-logos', 'public');
            }

            // تحضير ساعات العمل
            $workingHours = $companySetting->official_working_hours;
            if ($request->filled('working_hours_start') && $request->filled('working_hours_end')) {
                $workingHours = [
                    'start' => $request->working_hours_start,
                    'end' => $request->working_hours_end,
                ];
            }

            // تحضير وسائل التواصل
            $socialMedia = array_filter([
                'facebook' => $request->facebook,
                'twitter' => $request->twitter,
                'linkedin' => $request->linkedin,
                'instagram' => $request->instagram,
            ]);

            // تحديث الإعدادات
            $companySetting->update([
                'company_name' => $validated['company_name'],
                'logo_path' => $logoPath,
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'website' => $validated['website'],
                'official_working_hours' => $workingHours,
                'timezone' => $validated['timezone'],
                'currency' => $validated['currency'],
                'description' => $validated['description'],
                'social_media' => $socialMedia,
                'tax_number' => $validated['tax_number'],
                'registration_number' => $validated['registration_number'],
            ]);

            DB::commit();

            return redirect()->route('company-settings.index')
                ->with('success', 'Company settings updated successfully');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating settings: ' . $e->getMessage());
        }
    }

    /**
     * حذف الإعدادات
     */
    public function destroy(CompanySetting $companySetting): RedirectResponse
    {
        try {
            // حذف الشعار
            if ($companySetting->logo_path && Storage::disk('public')->exists($companySetting->logo_path)) {
                Storage::disk('public')->delete($companySetting->logo_path);
            }
            
            $companySetting->delete();
            
            return redirect()->route('company-settings.index')
                ->with('success', 'Company settings deleted successfully');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting settings: ' . $e->getMessage());
        }
    }
}