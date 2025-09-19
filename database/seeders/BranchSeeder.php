<?php

/**
 * Author: Eng.Fahed
 * Professional Branch Seeder for HR System
 * إنشاء فروع احترافية لكل مؤسسة
 */

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Branch;
use App\Models\Tenant;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            echo "🏢 Creating branches for: {$tenant->name}\n";
            
            // تحديد عدد الفروع حسب نوع المؤسسة
            $branchCount = $this->getBranchCountForTenant($tenant);
            
            for ($i = 1; $i <= $branchCount; $i++) {
                $branchData = $this->generateBranchData($tenant, $i);
                
                Branch::create($branchData);
                echo "  ✅ Created: {$branchData['name']}\n";
            }
        }
        
        echo "\n🎉 All branches created successfully!\n";
    }

    /**
     * تحديد عدد الفروع حسب المؤسسة
     */
    private function getBranchCountForTenant(Tenant $tenant): int
    {
        // حسب حجم المؤسسة أو نوعها
        $companyName = strtolower($tenant->name);
        
        if (str_contains($companyName, 'tech') || str_contains($companyName, 'software')) {
            return rand(2, 4); // شركات التكنولوجيا: 2-4 فروع
        } elseif (str_contains($companyName, 'medical') || str_contains($companyName, 'hospital')) {
            return rand(3, 6); // المؤسسات الطبية: 3-6 فروع
        } elseif (str_contains($companyName, 'education') || str_contains($companyName, 'school')) {
            return rand(2, 5); // المؤسسات التعليمية: 2-5 فروع
        } else {
            return rand(1, 3); // باقي المؤسسات: 1-3 فروع
        }
    }

    /**
     * إنشاء بيانات الفرع
     */
    private function generateBranchData(Tenant $tenant, int $branchNumber): array
    {
        $cities = ['دمشق', 'حلب', 'حمص', 'اللاذقية', 'طرطوس', 'درعا', 'السويداء', 'القنيطرة'];
        $city = $cities[array_rand($cities)];
        
        $branchTypes = [
            'الفرع الرئيسي',
            'فرع ' . $city,
            'مركز ' . $city,
            'مكتب ' . $city,
            'فرع ' . $city . ' التجاري'
        ];
        
        $managers = [
            'أحمد محمد السويداء',
            'فاطمة علي الحسن', 
            'محمد سامر الكرم',
            'نور الدين العلي',
            'سارة أحمد النجار',
            'عمار محمود الشام',
            'ريم سعد الدين',
            'طارق عبد الله'
        ];

        $workingHours = [
            ['start' => '08:00', 'end' => '16:00'],
            ['start' => '09:00', 'end' => '17:00'],
            ['start' => '08:30', 'end' => '16:30'],
            ['start' => '09:00', 'end' => '18:00'],
        ];

        return [
            'name' => $branchNumber === 1 ? 'الفرع الرئيسي' : $branchTypes[array_rand($branchTypes)],
            'address' => $this->generateAddress($city),
            'location' => $city . ', سوريا',
            'phone' => '+963-' . rand(11, 99) . '-' . rand(1000000, 9999999),
            'email' => strtolower(str_replace(' ', '', $city)) . $branchNumber . '@' . $this->getDomainFromTenant($tenant),
            'manager_name' => $managers[array_rand($managers)],
            'working_hours' => $workingHours[array_rand($workingHours)],
            'is_active' => $branchNumber <= 2 ? true : (rand(1, 10) > 2), // الفروع الأولى نشطة، الباقي 80% نشطة
            'description' => $this->generateDescription($tenant, $branchNumber),
            'tenant_id' => $tenant->id,
        ];
    }

    /**
     * إنشاء عنوان واقعي
     */
    private function generateAddress(string $city): string
    {
        $streets = [
            'شارع الثورة',
            'شارع بغداد', 
            'شارع الجلاء',
            'شارع النصر',
            'شارع الوحدة',
            'شارع العروبة',
            'شارع الكورنيش',
            'شارع الجامعة'
        ];
        
        $street = $streets[array_rand($streets)];
        $building = rand(1, 200);
        
        return "{$street}، مبنى رقم {$building}، {$city}، سوريا";
    }

    /**
     * استخراج domain من الـ tenant
     */
    private function getDomainFromTenant(Tenant $tenant): string
    {
        return $tenant->domain ?: 'company.com';
    }

    /**
     * إنشاء وصف للفرع
     */
    private function generateDescription(Tenant $tenant, int $branchNumber): string
    {
        if ($branchNumber === 1) {
            return "الفرع الرئيسي لشركة {$tenant->name} ويضم الإدارة العامة والأقسام الرئيسية.";
        }
        
        $descriptions = [
            "فرع متخصص في خدمة العملاء وتقديم الاستشارات.",
            "مركز إقليمي يخدم المنطقة ويوفر جميع الخدمات.",
            "فرع تجاري متخصص في المبيعات والتسويق.",
            "مكتب إداري يضم قسم الموارد البشرية والمالية.",
            "فرع حديث مجهز بأحدث التقنيات والمعدات."
        ];
        
        return $descriptions[array_rand($descriptions)];
    }
}