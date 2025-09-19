<?php

/**
 * Author: Eng.Fahed
 * Leaves Migration - HR System
 * جدول الإجازات مع جميع التفاصيل والموافقات
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
        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            
            // ربط بالموظف والمؤسسة
            $table->unsignedBigInteger('employee_id')->comment('معرف الموظف');
            $table->unsignedBigInteger('tenant_id')->comment('معرف المؤسسة');
            
            // معلومات الطلب
            $table->string('leave_type', 50)->comment('نوع الإجازة');
            $table->date('start_date')->comment('تاريخ بداية الإجازة');
            $table->date('end_date')->comment('تاريخ نهاية الإجازة');
            $table->integer('total_days')->comment('إجمالي أيام الإجازة');
            $table->enum('day_type', ['full_day', 'half_day', 'quarter_day'])->default('full_day')->comment('نوع اليوم');
            $table->time('start_time')->nullable()->comment('وقت البداية (للإجازات الجزئية)');
            $table->time('end_time')->nullable()->comment('وقت النهاية (للإجازات الجزئية)');
            
            // سبب الإجازة
            $table->text('reason')->comment('سبب الإجازة');
            $table->text('description')->nullable()->comment('وصف تفصيلي');
            $table->string('emergency_contact', 255)->nullable()->comment('جهة الاتصال في حالة الطوارئ');
            
            // المستندات المرفقة
            $table->json('attachments')->nullable()->comment('المستندات المرفقة JSON');
            $table->boolean('is_medical')->default(false)->comment('إجازة طبية');
            $table->string('medical_certificate')->nullable()->comment('الشهادة الطبية');
            
            // حالة الطلب
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending')->comment('حالة الطلب');
            $table->text('rejection_reason')->nullable()->comment('سبب الرفض');
            $table->text('admin_notes')->nullable()->comment('ملاحظات الإدارة');
            
            // معلومات الموافقة
            $table->unsignedBigInteger('approved_by')->nullable()->comment('معتمد من قبل');
            $table->timestamp('approved_at')->nullable()->comment('تاريخ الاعتماد');
            $table->unsignedBigInteger('rejected_by')->nullable()->comment('مرفوض من قبل');
            $table->timestamp('rejected_at')->nullable()->comment('تاريخ الرفض');
            
            // معلومات التغطية
            $table->unsignedBigInteger('covering_employee_id')->nullable()->comment('الموظف البديل');
            $table->text('handover_notes')->nullable()->comment('ملاحظات تسليم المهام');
            $table->boolean('is_paid')->default(true)->comment('إجازة مدفوعة الأجر');
            
            // إعدادات الإشعارات
            $table->boolean('notify_manager')->default(true)->comment('إشعار المدير');
            $table->boolean('notify_hr')->default(true)->comment('إشعار الموارد البشرية');
            $table->boolean('notify_covering_employee')->default(true)->comment('إشعار الموظف البديل');
            
            // معلومات الإلغاء
            $table->unsignedBigInteger('cancelled_by')->nullable()->comment('ملغى من قبل');
            $table->timestamp('cancelled_at')->nullable()->comment('تاريخ الإلغاء');
            $table->text('cancellation_reason')->nullable()->comment('سبب الإلغاء');
            
            // إحصائيات
            $table->integer('remaining_annual_days')->nullable()->comment('أيام الإجازة السنوية المتبقية');
            $table->integer('remaining_sick_days')->nullable()->comment('أيام الإجازة المرضية المتبقية');
            
            // تواريخ النظام
            $table->timestamps();
            $table->softDeletes()->comment('حذف ناعم');
            
            // فهارس
            $table->index(['employee_id', 'status']);
            $table->index(['tenant_id', 'status', 'start_date']);
            $table->index(['start_date', 'end_date']);
            $table->index(['leave_type', 'status']);
            $table->index(['approved_by', 'approved_at']);
            $table->index(['status', 'created_at']);
            
            // المفاتيح الخارجية
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('rejected_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('cancelled_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('covering_employee_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaves');
    }
};