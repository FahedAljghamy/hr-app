<?php

/**
 * Author: Eng.Fahed
 * Legal Document Controller for HR System
 * كونترولر إدارة المستندات القانونية
 */

namespace App\Http\Controllers;

use App\Models\LegalDocument;
use App\Models\Branch;
use App\Models\CompanySetting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LegalDocumentController extends Controller
{
    /**
     * عرض قائمة المستندات القانونية
     */
    public function index(Request $request): View
    {
        $query = LegalDocument::with(['branch', 'companySetting']);

        // البحث
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('document_name', 'like', "%{$search}%")
                  ->orWhere('document_number', 'like', "%{$search}%")
                  ->orWhere('issuing_authority', 'like', "%{$search}%");
            });
        }

        // فلترة حسب النوع
        if ($request->filled('document_type')) {
            $query->where('document_type', $request->document_type);
        }

        // فلترة حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة المستندات المنتهية الصلاحية قريباً
        if ($request->filled('expiring_soon')) {
            $query->expiringSoon($request->expiring_soon);
        }

        $documents = $query->orderBy('expiry_date', 'asc')->paginate(15);
        
        // إحصائيات
        $stats = [
            'total' => LegalDocument::count(),
            'active' => LegalDocument::where('status', 'active')->count(),
            'expiring_soon' => LegalDocument::expiringSoon(30)->count(),
            'expired' => LegalDocument::expired()->count(),
        ];

        $documentTypes = LegalDocument::getUAEDocumentTypes();

        return view('legal-documents.index', compact('documents', 'stats', 'documentTypes'));
    }

    /**
     * إظهار صفحة إنشاء مستند جديد
     */
    public function create(): View
    {
        $documentTypes = LegalDocument::getUAEDocumentTypes();
        $branches = Branch::where('tenant_id', auth()->user()->tenant_id)->get();
        $companySetting = CompanySetting::where('tenant_id', auth()->user()->tenant_id)->first();

        return view('legal-documents.create', compact('documentTypes', 'branches', 'companySetting'));
    }

    /**
     * حفظ مستند جديد
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'document_type' => 'required|string',
            'document_number' => 'required|string|max:100',
            'document_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'renewal_date' => 'nullable|date|after:expiry_date',
            'issuing_authority' => 'required|string|max:255',
            'issuing_location' => 'nullable|string|max:255',
            'is_mandatory' => 'boolean',
            'renewal_reminder_days' => 'required|integer|min:1|max:365',
            'renewal_cost' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:3',
            'notes' => 'nullable|string',
            'branch_id' => 'nullable|exists:branches,id',
            'company_setting_id' => 'nullable|exists:company_settings,id',
            'document_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB
        ]);

        try {
            DB::beginTransaction();

            // رفع الملف
            $filePath = null;
            $fileType = null;
            $fileSize = null;
            
            if ($request->hasFile('document_file')) {
                $file = $request->file('document_file');
                $filePath = $file->store('legal-documents', 'public');
                $fileType = $file->getClientOriginalExtension();
                $fileSize = $file->getSize();
            }

            // إنشاء المستند
            LegalDocument::create([
                'document_type' => $validated['document_type'],
                'document_number' => $validated['document_number'],
                'document_name' => $validated['document_name'],
                'description' => $validated['description'],
                'issue_date' => $validated['issue_date'],
                'expiry_date' => $validated['expiry_date'],
                'renewal_date' => $validated['renewal_date'],
                'issuing_authority' => $validated['issuing_authority'],
                'issuing_location' => $validated['issuing_location'],
                'file_path' => $filePath,
                'file_type' => $fileType,
                'file_size' => $fileSize,
                'is_mandatory' => $request->boolean('is_mandatory', true),
                'renewal_reminder_days' => $validated['renewal_reminder_days'],
                'renewal_cost' => $validated['renewal_cost'],
                'currency' => $validated['currency'],
                'notes' => $validated['notes'],
                'branch_id' => $validated['branch_id'],
                'company_setting_id' => $validated['company_setting_id'],
                'tenant_id' => auth()->user()->tenant_id,
                'status' => 'active',
            ]);

            DB::commit();

            return redirect()->route('legal-documents.index')
                ->with('success', 'Legal document created successfully');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating document: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل مستند محدد
     */
    public function show(LegalDocument $legalDocument): View
    {
        $legalDocument->load(['branch', 'companySetting', 'tenant']);
        
        return view('legal-documents.show', compact('legalDocument'));
    }

    /**
     * إظهار صفحة تعديل المستند
     */
    public function edit(LegalDocument $legalDocument): View
    {
        $documentTypes = LegalDocument::getUAEDocumentTypes();
        $branches = Branch::where('tenant_id', auth()->user()->tenant_id)->get();
        $companySetting = CompanySetting::where('tenant_id', auth()->user()->tenant_id)->first();

        return view('legal-documents.edit', compact('legalDocument', 'documentTypes', 'branches', 'companySetting'));
    }

    /**
     * تحديث المستند
     */
    public function update(Request $request, LegalDocument $legalDocument): RedirectResponse
    {
        $validated = $request->validate([
            'document_type' => 'required|string',
            'document_number' => 'required|string|max:100',
            'document_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'renewal_date' => 'nullable|date|after:expiry_date',
            'issuing_authority' => 'required|string|max:255',
            'issuing_location' => 'nullable|string|max:255',
            'is_mandatory' => 'boolean',
            'renewal_reminder_days' => 'required|integer|min:1|max:365',
            'renewal_cost' => 'nullable|numeric|min:0',
            'currency' => 'required|string|max:3',
            'notes' => 'nullable|string',
            'branch_id' => 'nullable|exists:branches,id',
            'company_setting_id' => 'nullable|exists:company_settings,id',
            'document_file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240', // 10MB
        ]);

        try {
            DB::beginTransaction();

            // رفع ملف جديد إذا تم اختياره
            $filePath = $legalDocument->file_path;
            $fileType = $legalDocument->file_type;
            $fileSize = $legalDocument->file_size;
            
            if ($request->hasFile('document_file')) {
                // حذف الملف القديم
                if ($filePath && Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath);
                }
                
                $file = $request->file('document_file');
                $filePath = $file->store('legal-documents', 'public');
                $fileType = $file->getClientOriginalExtension();
                $fileSize = $file->getSize();
            }

            // تحديث المستند
            $legalDocument->update([
                'document_type' => $validated['document_type'],
                'document_number' => $validated['document_number'],
                'document_name' => $validated['document_name'],
                'description' => $validated['description'],
                'issue_date' => $validated['issue_date'],
                'expiry_date' => $validated['expiry_date'],
                'renewal_date' => $validated['renewal_date'],
                'issuing_authority' => $validated['issuing_authority'],
                'issuing_location' => $validated['issuing_location'],
                'file_path' => $filePath,
                'file_type' => $fileType,
                'file_size' => $fileSize,
                'is_mandatory' => $request->boolean('is_mandatory', true),
                'renewal_reminder_days' => $validated['renewal_reminder_days'],
                'renewal_cost' => $validated['renewal_cost'],
                'currency' => $validated['currency'],
                'notes' => $validated['notes'],
                'branch_id' => $validated['branch_id'],
                'company_setting_id' => $validated['company_setting_id'],
            ]);

            DB::commit();

            return redirect()->route('legal-documents.index')
                ->with('success', 'Legal document updated successfully');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating document: ' . $e->getMessage());
        }
    }

    /**
     * حذف المستند
     */
    public function destroy(LegalDocument $legalDocument): RedirectResponse
    {
        try {
            // حذف الملف
            if ($legalDocument->file_path && Storage::disk('public')->exists($legalDocument->file_path)) {
                Storage::disk('public')->delete($legalDocument->file_path);
            }
            
            $legalDocument->delete();
            
            return redirect()->route('legal-documents.index')
                ->with('success', 'Legal document deleted successfully');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting document: ' . $e->getMessage());
        }
    }

    /**
     * تحديث حالة المستند
     */
    public function updateStatus(Request $request, LegalDocument $legalDocument): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:active,expired,pending_renewal,cancelled'
        ]);

        $legalDocument->update([
            'status' => $validated['status'],
            'renewed_at' => $validated['status'] === 'active' ? now() : $legalDocument->renewed_at,
        ]);

        return redirect()->back()
            ->with('success', 'Document status updated successfully');
    }

    /**
     * تنزيل المستند
     */
    public function download(LegalDocument $legalDocument)
    {
        if (!$legalDocument->file_path || !Storage::disk('public')->exists($legalDocument->file_path)) {
            return redirect()->back()->with('error', 'Document file not found');
        }

        return Storage::disk('public')->download($legalDocument->file_path, $legalDocument->document_name . '.' . $legalDocument->file_type);
    }
}