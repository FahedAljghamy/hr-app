<?php

/**
 * Author: Eng.Fahed
 * Employee Notifications Migration - HR System
 * جدول إشعارات الموظفين
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
        Schema::create('employee_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            
            // نوع الإشعار
            $table->enum('type', [
                'document_expiry',          // انتهاء صلاحية مستند
                'contract_expiry',          // انتهاء عقد
                'leave_approved',           // موافقة على الإجازة
                'leave_rejected',           // رفض الإجازة
                'payroll_ready',            // الراتب جاهز
                'certificate_ready',        // الشهادة جاهزة
                'system_announcement',      // إعلان من النظام
                'birthday_reminder',        // تذكير عيد ميلاد
                'work_anniversary',         // ذكرى العمل
                'training_reminder',        // تذكير تدريب
                'meeting_reminder',         // تذكير اجتماع
                'general'                   // عام
            ]);
            
            // محتوى الإشعار
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable(); // بيانات إضافية (JSON)
            
            // أولوية الإشعار
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            
            // حالة الإشعار
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_important')->default(false);
            $table->boolean('requires_action')->default(false);
            
            // رابط الإجراء
            $table->string('action_url')->nullable();
            $table->string('action_text')->nullable();
            
            // تواريخ الانتهاء والتذكير
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('remind_at')->nullable();
            
            // تواريخ النظام
            $table->timestamps();
            
            // فهارس للأداء
            $table->index(['employee_id', 'is_read']);
            $table->index(['tenant_id', 'type']);
            $table->index(['created_at', 'priority']);
            $table->index(['expires_at', 'remind_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_notifications');
    }
};