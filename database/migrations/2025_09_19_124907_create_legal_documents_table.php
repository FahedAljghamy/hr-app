<?php

/**
 * Author: Eng.Fahed
 * Migration for Legal Documents table
 * إنشاء جدول المستندات القانونية حسب قانون الإمارات
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
        Schema::create('legal_documents', function (Blueprint $table) {
            $table->id();
            
            // معلومات المستند
            $table->string('document_type'); // نوع المستند
            $table->string('document_number'); // رقم المستند
            $table->string('document_name'); // اسم المستند
            $table->text('description')->nullable(); // وصف المستند
            
            // تواريخ مهمة
            $table->date('issue_date'); // تاريخ الإصدار
            $table->date('expiry_date'); // تاريخ انتهاء الصلاحية
            $table->date('renewal_date')->nullable(); // تاريخ التجديد المتوقع
            
            // معلومات الجهة المصدرة
            $table->string('issuing_authority'); // الجهة المصدرة
            $table->string('issuing_location')->nullable(); // مكان الإصدار
            
            // ملفات المستند
            $table->string('file_path')->nullable(); // مسار ملف المستند
            $table->string('file_type')->nullable(); // نوع الملف
            $table->bigInteger('file_size')->nullable(); // حجم الملف
            
            // حالة المستند
            $table->enum('status', ['active', 'expired', 'pending_renewal', 'cancelled'])->default('active');
            $table->boolean('is_mandatory')->default(true); // مستند إجباري أم لا
            $table->integer('renewal_reminder_days')->default(30); // تذكير قبل كم يوم
            
            // تكلفة التجديد
            $table->decimal('renewal_cost', 10, 2)->nullable(); // تكلفة التجديد
            $table->string('currency', 3)->default('AED'); // العملة
            
            // ملاحظات
            $table->text('notes')->nullable(); // ملاحظات إضافية
            $table->json('metadata')->nullable(); // بيانات إضافية (JSON)
            
            // ربط بالشركة أو الفرع
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('cascade');
            $table->foreignId('company_setting_id')->nullable()->constrained('company_settings')->onDelete('cascade');
            
            // تواريخ التتبع
            $table->timestamp('last_notification_sent')->nullable(); // آخر إرسال تنبيه
            $table->timestamp('renewed_at')->nullable(); // تاريخ آخر تجديد
            
            $table->timestamps();
            
            // Indexes
            $table->index(['tenant_id', 'document_type']);
            $table->index(['expiry_date', 'status']);
            $table->index(['tenant_id', 'expiry_date']);
            $table->index('document_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_documents');
    }
};