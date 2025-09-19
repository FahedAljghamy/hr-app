<?php

/**
 * Author: Eng.Fahed
 * Legal Documents Seeder for HR System
 * إنشاء مستندات قانونية واقعية حسب قانون الإمارات
 */

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LegalDocument;
use App\Models\Tenant;
use App\Models\CompanySetting;
use Carbon\Carbon;

class LegalDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            echo "📄 Creating legal documents for: {$tenant->name}\n";
            
            $companySetting = CompanySetting::where('tenant_id', $tenant->id)->first();
            
            if (!$companySetting) {
                echo "  ⚠️ No company settings found, skipping...\n";
                continue;
            }

            // إنشاء المستندات الإجبارية للشركة
            $this->createMandatoryDocuments($tenant, $companySetting);
            
            // إنشاء مستندات إضافية حسب نوع الشركة
            $this->createOptionalDocuments($tenant, $companySetting);
        }
        
        echo "\n🎉 All legal documents created successfully!\n";
    }

    /**
     * إنشاء المستندات الإجبارية
     */
    private function createMandatoryDocuments(Tenant $tenant, CompanySetting $companySetting): void
    {
        $mandatoryDocs = [
            [
                'type' => 'trade_license',
                'name' => 'Trade License',
                'authority' => 'Department of Economic Development (DED)',
                'validity_years' => 1,
                'cost' => 5000,
                'reminder_days' => 60,
            ],
            [
                'type' => 'commercial_registration',
                'name' => 'Commercial Registration',
                'authority' => 'DED Commercial Registration',
                'validity_years' => 1,
                'cost' => 2000,
                'reminder_days' => 45,
            ],
            [
                'type' => 'establishment_card',
                'name' => 'Establishment Card',
                'authority' => 'Ministry of Human Resources',
                'validity_years' => 2,
                'cost' => 1500,
                'reminder_days' => 30,
            ],
            [
                'type' => 'vat_certificate',
                'name' => 'VAT Registration Certificate',
                'authority' => 'Federal Tax Authority (FTA)',
                'validity_years' => 5,
                'cost' => 0,
                'reminder_days' => 90,
            ],
        ];

        foreach ($mandatoryDocs as $docData) {
            $this->createDocument($tenant, $companySetting, $docData, true);
            echo "  ✅ Created mandatory: {$docData['name']}\n";
        }
    }

    /**
     * إنشاء مستندات إضافية حسب نوع الشركة
     */
    private function createOptionalDocuments(Tenant $tenant, CompanySetting $companySetting): void
    {
        $companyType = $this->getCompanyType($tenant->name);
        
        $optionalDocs = [];
        
        if ($companyType === 'tech') {
            $optionalDocs = [
                [
                    'type' => 'professional_license',
                    'name' => 'IT Professional License',
                    'authority' => 'Telecommunications Regulatory Authority',
                    'validity_years' => 3,
                    'cost' => 3000,
                    'reminder_days' => 45,
                ],
            ];
        } elseif ($companyType === 'medical') {
            $optionalDocs = [
                [
                    'type' => 'health_insurance',
                    'name' => 'Health Insurance Certificate',
                    'authority' => 'Dubai Health Authority',
                    'validity_years' => 1,
                    'cost' => 8000,
                    'reminder_days' => 30,
                ],
                [
                    'type' => 'safety_certificate',
                    'name' => 'Medical Safety Certificate',
                    'authority' => 'Dubai Municipality',
                    'validity_years' => 2,
                    'cost' => 2500,
                    'reminder_days' => 45,
                ],
            ];
        } elseif ($companyType === 'trading') {
            $optionalDocs = [
                [
                    'type' => 'import_export_license',
                    'name' => 'Import/Export License',
                    'authority' => 'UAE Customs',
                    'validity_years' => 2,
                    'cost' => 4000,
                    'reminder_days' => 60,
                ],
            ];
        }

        foreach ($optionalDocs as $docData) {
            $this->createDocument($tenant, $companySetting, $docData, false);
            echo "  ✅ Created optional: {$docData['name']}\n";
        }
    }

    /**
     * إنشاء مستند واحد
     */
    private function createDocument(Tenant $tenant, CompanySetting $companySetting, array $docData, bool $isMandatory): void
    {
        // تواريخ واقعية
        $issueDate = Carbon::now()->subMonths(rand(1, 12));
        $expiryDate = $issueDate->copy()->addYears($docData['validity_years']);
        $renewalDate = $expiryDate->copy()->addDays(30);

        // رقم مستند واقعي
        $documentNumber = $this->generateDocumentNumber($docData['type']);

        LegalDocument::create([
            'document_type' => $docData['type'],
            'document_number' => $documentNumber,
            'document_name' => $docData['name'],
            'description' => "Official {$docData['name']} for {$tenant->name}",
            'issue_date' => $issueDate,
            'expiry_date' => $expiryDate,
            'renewal_date' => $renewalDate,
            'issuing_authority' => $docData['authority'],
            'issuing_location' => 'Dubai, UAE',
            'status' => $expiryDate->isPast() ? 'expired' : ($expiryDate->diffInDays(now()) <= $docData['reminder_days'] ? 'pending_renewal' : 'active'),
            'is_mandatory' => $isMandatory,
            'renewal_reminder_days' => $docData['reminder_days'],
            'renewal_cost' => $docData['cost'],
            'currency' => 'AED',
            'notes' => $this->generateNotes($docData['type'], $tenant),
            'tenant_id' => $tenant->id,
            'company_setting_id' => $companySetting->id,
        ]);
    }

    /**
     * تحديد نوع الشركة
     */
    private function getCompanyType(string $companyName): string
    {
        $name = strtolower($companyName);
        
        if (str_contains($name, 'tech') || str_contains($name, 'software')) {
            return 'tech';
        } elseif (str_contains($name, 'medical') || str_contains($name, 'hospital')) {
            return 'medical';
        } elseif (str_contains($name, 'trading') || str_contains($name, 'commercial')) {
            return 'trading';
        } else {
            return 'general';
        }
    }

    /**
     * إنشاء رقم مستند واقعي
     */
    private function generateDocumentNumber(string $type): string
    {
        $prefixes = [
            'trade_license' => 'TL',
            'commercial_registration' => 'CR',
            'establishment_card' => 'EC',
            'vat_certificate' => 'VAT',
            'professional_license' => 'PL',
            'health_insurance' => 'HI',
            'import_export_license' => 'IE',
            'safety_certificate' => 'SC',
        ];

        $prefix = $prefixes[$type] ?? 'DOC';
        $year = date('Y');
        $number = rand(100000, 999999);

        return "{$prefix}-{$year}-{$number}";
    }

    /**
     * إنشاء ملاحظات للمستند
     */
    private function generateNotes(string $type, Tenant $tenant): string
    {
        $notes = [
            'trade_license' => "Trade license issued for {$tenant->name}. Must be renewed annually before expiry.",
            'commercial_registration' => "Commercial registration certificate for business operations.",
            'establishment_card' => "Establishment card for labor and employment activities.",
            'vat_certificate' => "VAT registration certificate for tax compliance.",
            'professional_license' => "Professional license for specialized activities.",
            'health_insurance' => "Health insurance coverage for employees.",
            'import_export_license' => "License for import and export activities.",
            'safety_certificate' => "Safety compliance certificate for workplace.",
        ];

        return $notes[$type] ?? "Legal document for {$tenant->name} business operations.";
    }
}