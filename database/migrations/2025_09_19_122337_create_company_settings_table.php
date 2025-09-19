<?php

/**
 * Author: Eng.Fahed
 * Migration for Company Settings table
 * إنشاء جدول إعدادات الشركة
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name'); // اسم الشركة
            $table->string('logo_path')->nullable(); // مسار الشعار
            $table->string('email'); // البريد الإلكتروني الرسمي
            $table->string('phone')->nullable(); // رقم الهاتف
            $table->text('address'); // العنوان الرسمي
            $table->string('website')->nullable(); // الموقع الإلكتروني
            $table->json('official_working_hours'); // ساعات العمل الرسمية
            $table->string('timezone')->default('UTC'); // المنطقة الزمنية
            $table->string('currency')->default('USD'); // العملة
            $table->text('description')->nullable(); // وصف الشركة
            $table->json('social_media')->nullable(); // روابط وسائل التواصل
            $table->string('tax_number')->nullable(); // الرقم الضريبي
            $table->string('registration_number')->nullable(); // رقم السجل التجاري
            
            // Foreign key للـ tenant
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            
            $table->timestamps();
            
            // Unique constraint - one setting per tenant
            $table->unique('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};