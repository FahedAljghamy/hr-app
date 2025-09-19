<?php

/**
 * Author: Eng.Fahed
 * Employees Migration - HR System
 * جدول الموظفين مع جميع البيانات الشخصية والمهنية والقانونية
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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            
            // معرف الموظف الفريد
            $table->string('employee_id', 20)->unique()->comment('معرف الموظف الفريد');
            
            // ربط بالـ tenant والفرع
            $table->unsignedBigInteger('tenant_id')->comment('معرف المؤسسة');
            $table->unsignedBigInteger('branch_id')->nullable()->comment('معرف الفرع');
            $table->unsignedBigInteger('user_id')->nullable()->comment('حساب المستخدم إذا كان موجود');
            
            // البيانات الشخصية
            $table->string('first_name', 100)->comment('الاسم الأول');
            $table->string('last_name', 100)->comment('الاسم الأخير');
            $table->string('middle_name', 100)->nullable()->comment('الاسم الأوسط');
            $table->string('full_name_ar', 255)->nullable()->comment('الاسم الكامل بالعربية');
            $table->string('email')->unique()->comment('البريد الإلكتروني');
            $table->string('phone', 20)->comment('رقم الهاتف');
            $table->string('phone_secondary', 20)->nullable()->comment('رقم هاتف ثانوي');
            $table->date('date_of_birth')->comment('تاريخ الميلاد');
            $table->enum('gender', ['male', 'female'])->comment('الجنس');
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed'])->comment('الحالة الاجتماعية');
            $table->string('nationality', 100)->comment('الجنسية');
            $table->text('address')->comment('العنوان الحالي');
            $table->text('emergency_contact')->nullable()->comment('بيانات الاتصال الطارئ JSON');
            
            // بيانات الهوية والإقامة
            $table->string('passport_number', 50)->unique()->comment('رقم جواز السفر');
            $table->date('passport_expiry')->comment('تاريخ انتهاء جواز السفر');
            $table->string('passport_country', 100)->comment('دولة إصدار جواز السفر');
            $table->string('visa_number', 50)->nullable()->comment('رقم الفيزا/الإقامة');
            $table->date('visa_expiry')->nullable()->comment('تاريخ انتهاء الفيزا/الإقامة');
            $table->string('emirates_id', 20)->nullable()->comment('رقم الهوية الإماراتية');
            $table->date('emirates_id_expiry')->nullable()->comment('تاريخ انتهاء الهوية الإماراتية');
            $table->string('labor_card_number', 50)->nullable()->comment('رقم بطاقة العمل');
            $table->date('labor_card_expiry')->nullable()->comment('تاريخ انتهاء بطاقة العمل');
            
            // بيانات العقد والعمل
            $table->string('job_title', 200)->comment('المسمى الوظيفي');
            $table->string('department', 100)->comment('القسم');
            $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'intern'])->comment('نوع التوظيف');
            $table->enum('employment_status', ['active', 'inactive', 'terminated', 'resigned'])->default('active')->comment('حالة التوظيف');
            $table->date('hire_date')->comment('تاريخ التوظيف');
            $table->date('contract_start_date')->comment('تاريخ بداية العقد');
            $table->date('contract_end_date')->nullable()->comment('تاريخ انتهاء العقد');
            $table->integer('probation_period_months')->default(6)->comment('فترة التجربة بالأشهر');
            $table->date('probation_end_date')->nullable()->comment('تاريخ انتهاء فترة التجربة');
            $table->integer('annual_leave_days')->default(30)->comment('أيام الإجازة السنوية');
            $table->integer('sick_leave_days')->default(90)->comment('أيام الإجازة المرضية');
            
            // بيانات الراتب
            $table->decimal('basic_salary', 10, 2)->comment('الراتب الأساسي');
            $table->string('salary_currency', 3)->default('AED')->comment('عملة الراتب');
            $table->enum('salary_frequency', ['monthly', 'weekly', 'daily'])->default('monthly')->comment('تكرار دفع الراتب');
            $table->decimal('housing_allowance', 8, 2)->default(0)->comment('بدل السكن');
            $table->decimal('transport_allowance', 8, 2)->default(0)->comment('بدل المواصلات');
            $table->decimal('food_allowance', 8, 2)->default(0)->comment('بدل الطعام');
            $table->decimal('other_allowances', 8, 2)->default(0)->comment('بدلات أخرى');
            
            // بيانات بنكية
            $table->string('bank_name', 100)->nullable()->comment('اسم البنك');
            $table->string('bank_account_number', 50)->nullable()->comment('رقم الحساب البنكي');
            $table->string('iban', 50)->nullable()->comment('رقم الآيبان');
            $table->string('swift_code', 20)->nullable()->comment('رمز السويفت');
            
            // المستندات والملفات
            $table->string('profile_photo')->nullable()->comment('صورة شخصية');
            $table->string('passport_copy')->nullable()->comment('نسخة من جواز السفر');
            $table->string('visa_copy')->nullable()->comment('نسخة من الفيزا/الإقامة');
            $table->string('emirates_id_copy')->nullable()->comment('نسخة من الهوية الإماراتية');
            $table->string('labor_card_copy')->nullable()->comment('نسخة من بطاقة العمل');
            $table->string('contract_copy')->nullable()->comment('نسخة من العقد');
            $table->text('educational_certificates')->nullable()->comment('الشهادات التعليمية JSON');
            $table->text('other_documents')->nullable()->comment('مستندات أخرى JSON');
            
            // معلومات إضافية
            $table->text('skills')->nullable()->comment('المهارات JSON');
            $table->text('languages')->nullable()->comment('اللغات JSON');
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->boolean('is_manager')->default(false)->comment('هل هو مدير');
            $table->unsignedBigInteger('manager_id')->nullable()->comment('معرف المدير المباشر');
            
            // تواريخ النظام
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes()->comment('حذف ناعم');
            
            // فهارس
            $table->index(['tenant_id', 'employment_status']);
            $table->index(['branch_id', 'employment_status']);
            $table->index(['hire_date', 'employment_status']);
            $table->index(['contract_end_date']);
            $table->index(['visa_expiry']);
            $table->index(['passport_expiry']);
            $table->index(['emirates_id_expiry']);
            $table->index(['labor_card_expiry']);
            
            // المفاتيح الخارجية
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('manager_id')->references('id')->on('employees')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};