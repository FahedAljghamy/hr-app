<?php

/**
 * Author: Eng.Fahed
 * Professional Company Settings Seeder for HR System
 * إنشاء إعدادات شركة احترافية لكل مؤسسة
 */

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CompanySetting;
use App\Models\Tenant;

class CompanySettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            // تحقق من وجود إعدادات مسبقة
            if (CompanySetting::where('tenant_id', $tenant->id)->exists()) {
                echo "⚪ Settings already exist for: {$tenant->name}\n";
                continue;
            }

            echo "🏢 Creating company settings for: {$tenant->name}\n";
            
            $settingData = $this->generateCompanySettingData($tenant);
            
            CompanySetting::create($settingData);
            echo "  ✅ Company settings created successfully\n";
        }
        
        echo "\n🎉 All company settings created successfully!\n";
    }

    /**
     * إنشاء بيانات إعدادات الشركة
     */
    private function generateCompanySettingData(Tenant $tenant): array
    {
        $companyTypes = $this->getCompanyType($tenant->name);
        
        return [
            'company_name' => $tenant->name,
            'logo_path' => null, // سيتم إضافة الشعار لاحقاً
            'email' => $this->generateOfficialEmail($tenant),
            'phone' => $this->generatePhoneNumber(),
            'address' => $this->generateCompanyAddress($companyTypes['city']),
            'website' => $this->generateWebsite($tenant),
            'official_working_hours' => $this->generateWorkingHours($companyTypes['type']),
            'timezone' => 'Asia/Damascus',
            'currency' => 'SYP',
            'description' => $this->generateCompanyDescription($tenant, $companyTypes),
            'social_media' => $this->generateSocialMedia($tenant),
            'tax_number' => $this->generateTaxNumber(),
            'registration_number' => $this->generateRegistrationNumber(),
            'tenant_id' => $tenant->id,
        ];
    }

    /**
     * تحديد نوع الشركة والمدينة
     */
    private function getCompanyType(string $companyName): array
    {
        $name = strtolower($companyName);
        
        if (str_contains($name, 'tech') || str_contains($name, 'software')) {
            return ['type' => 'tech', 'city' => 'دمشق'];
        } elseif (str_contains($name, 'medical') || str_contains($name, 'hospital')) {
            return ['type' => 'medical', 'city' => 'حلب'];
        } elseif (str_contains($name, 'education') || str_contains($name, 'school')) {
            return ['type' => 'education', 'city' => 'حمص'];
        } elseif (str_contains($name, 'trading') || str_contains($name, 'commercial')) {
            return ['type' => 'trading', 'city' => 'اللاذقية'];
        } else {
            $cities = ['دمشق', 'حلب', 'حمص', 'اللاذقية', 'السويداء'];
            return ['type' => 'general', 'city' => $cities[array_rand($cities)]];
        }
    }

    /**
     * إنشاء بريد إلكتروني رسمي
     */
    private function generateOfficialEmail(Tenant $tenant): string
    {
        $domain = $tenant->domain ?: 'company.com';
        return 'info@' . $domain;
    }

    /**
     * إنشاء رقم هاتف
     */
    private function generatePhoneNumber(): string
    {
        return '+963-11-' . rand(1000000, 9999999);
    }

    /**
     * إنشاء عنوان الشركة
     */
    private function generateCompanyAddress(string $city): string
    {
        $addresses = [
            'دمشق' => 'شارع الثورة، مبنى رقم 150، الطابق الخامس، دمشق، سوريا',
            'حلب' => 'شارع بغداد، مجمع الأعمال التجاري، الطابق الثالث، حلب، سوريا',
            'حمص' => 'شارع الجلاء، برج الأعمال، الطابق السابع، حمص، سوريا',
            'اللاذقية' => 'شارع الكورنيش، مركز التجارة الدولي، الطابق الرابع، اللاذقية، سوريا',
            'السويداء' => 'الشارع العام، مبنى الإدارة، الطابق الثاني، السويداء، سوريا',
        ];
        
        return $addresses[$city] ?? $addresses['دمشق'];
    }

    /**
     * إنشاء موقع إلكتروني
     */
    private function generateWebsite(Tenant $tenant): string
    {
        $domain = $tenant->domain ?: 'company.com';
        return 'https://www.' . $domain;
    }

    /**
     * إنشاء ساعات العمل حسب نوع الشركة
     */
    private function generateWorkingHours(string $type): array
    {
        $schedules = [
            'tech' => ['start' => '09:00', 'end' => '18:00'],
            'medical' => ['start' => '08:00', 'end' => '20:00'],
            'education' => ['start' => '07:30', 'end' => '15:00'],
            'trading' => ['start' => '08:30', 'end' => '17:30'],
            'general' => ['start' => '09:00', 'end' => '17:00'],
        ];
        
        return $schedules[$type] ?? $schedules['general'];
    }

    /**
     * إنشاء وصف الشركة
     */
    private function generateCompanyDescription(Tenant $tenant, array $companyTypes): string
    {
        $descriptions = [
            'tech' => "شركة {$tenant->name} هي شركة رائدة في مجال تكنولوجيا المعلومات والبرمجيات، تقدم حلول تقنية متطورة ومبتكرة للشركات والمؤسسات في سوريا والمنطقة العربية.",
            'medical' => "مؤسسة {$tenant->name} الطبية تقدم خدمات صحية شاملة ومتميزة مع فريق طبي متخصص وأحدث المعدات الطبية لضمان أفضل رعاية صحية للمرضى.",
            'education' => "مؤسسة {$tenant->name} التعليمية ملتزمة بتقديم تعليم عالي الجودة وتطوير قدرات الطلاب في بيئة تعليمية محفزة ومبتكرة.",
            'trading' => "شركة {$tenant->name} التجارية متخصصة في التجارة والاستيراد والتصدير مع شبكة واسعة من الشركاء التجاريين المحليين والدوليين.",
            'general' => "شركة {$tenant->name} تقدم خدمات متنوعة وحلول شاملة للعملاء مع التزام بالجودة والتميز في الأداء وخدمة العملاء.",
        ];
        
        return $descriptions[$companyTypes['type']] ?? $descriptions['general'];
    }

    /**
     * إنشاء روابط وسائل التواصل
     */
    private function generateSocialMedia(Tenant $tenant): array
    {
        $companySlug = strtolower(str_replace([' ', '.', 'Inc', 'LLC'], ['', '', '', ''], $tenant->name));
        $companySlug = preg_replace('/[^a-z0-9]/', '', $companySlug);
        
        return [
            'facebook' => "https://facebook.com/{$companySlug}",
            'twitter' => "https://twitter.com/{$companySlug}",
            'linkedin' => "https://linkedin.com/company/{$companySlug}",
            'instagram' => "https://instagram.com/{$companySlug}",
        ];
    }

    /**
     * إنشاء رقم ضريبي
     */
    private function generateTaxNumber(): string
    {
        return 'SY-TAX-' . rand(100000, 999999);
    }

    /**
     * إنشاء رقم سجل تجاري
     */
    private function generateRegistrationNumber(): string
    {
        return 'SY-REG-' . rand(10000, 99999) . '-' . date('Y');
    }
}