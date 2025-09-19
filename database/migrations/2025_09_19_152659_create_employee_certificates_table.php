<?php

/**
 * Author: Eng.Fahed
 * Employee Certificates Migration - HR System
 * جدول طلبات شهادات الراتب والإجازة المرضية
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
        Schema::create('employee_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('tenant_id')->constrained()->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            
            // نوع الشهادة
            $table->enum('certificate_type', [
                'salary_certificate',      // شهادة راتب
                'employment_certificate',  // شهادة عمل
                'experience_certificate',  // شهادة خبرة
                'medical_leave_certificate' // شهادة إجازة مرضية
            ]);
            
            // تفاصيل الطلب
            $table->string('purpose')->nullable(); // الغرض من الشهادة
            $table->text('additional_details')->nullable(); // تفاصيل إضافية
            $table->text('special_requirements')->nullable(); // متطلبات خاصة
            
            // معلومات الشهادة المرضية (إن وجدت)
            $table->date('medical_start_date')->nullable();
            $table->date('medical_end_date')->nullable();
            $table->string('medical_diagnosis')->nullable();
            $table->string('doctor_name')->nullable();
            $table->string('hospital_name')->nullable();
            
            // حالة الطلب
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable();
            
            // معلومات المعالجة
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            
            // ملف الشهادة المُنتجة
            $table->string('certificate_file')->nullable();
            $table->string('certificate_number')->nullable(); // رقم الشهادة
            
            // أولوية الطلب
            $table->enum('priority', ['normal', 'urgent', 'high'])->default('normal');
            
            // تواريخ النظام
            $table->timestamps();
            
            // فهارس للأداء
            $table->index(['employee_id', 'status']);
            $table->index(['tenant_id', 'certificate_type']);
            $table->index(['requested_by', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_certificates');
    }
};