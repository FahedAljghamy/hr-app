<?php

/**
 * Author: Eng.Fahed
 * Payrolls Migration - HR System
 * جدول الرواتب مع تفاصيل الراتب والبدلات والخصومات
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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            
            // ربط بالموظف والمؤسسة
            $table->unsignedBigInteger('employee_id')->comment('معرف الموظف');
            $table->unsignedBigInteger('tenant_id')->comment('معرف المؤسسة');
            
            // معلومات الراتب الشهري
            $table->year('pay_year')->comment('سنة الراتب');
            $table->tinyInteger('pay_month')->comment('شهر الراتب (1-12)');
            $table->date('pay_date')->comment('تاريخ دفع الراتب');
            $table->date('pay_period_start')->comment('بداية فترة الراتب');
            $table->date('pay_period_end')->comment('نهاية فترة الراتب');
            
            // الراتب الأساسي
            $table->decimal('basic_salary', 10, 2)->comment('الراتب الأساسي');
            $table->string('currency', 3)->default('AED')->comment('العملة');
            
            // البدلات
            $table->decimal('housing_allowance', 8, 2)->default(0)->comment('بدل السكن');
            $table->decimal('transport_allowance', 8, 2)->default(0)->comment('بدل المواصلات');
            $table->decimal('food_allowance', 8, 2)->default(0)->comment('بدل الطعام');
            $table->decimal('overtime_allowance', 8, 2)->default(0)->comment('بدل العمل الإضافي');
            $table->decimal('performance_bonus', 8, 2)->default(0)->comment('مكافأة الأداء');
            $table->decimal('commission', 8, 2)->default(0)->comment('العمولة');
            $table->decimal('other_allowances', 8, 2)->default(0)->comment('بدلات أخرى');
            $table->text('allowances_details')->nullable()->comment('تفاصيل البدلات JSON');
            
            // الخصومات
            $table->decimal('tax_deduction', 8, 2)->default(0)->comment('خصم الضرائب');
            $table->decimal('insurance_deduction', 8, 2)->default(0)->comment('خصم التأمين');
            $table->decimal('loan_deduction', 8, 2)->default(0)->comment('خصم القروض');
            $table->decimal('advance_deduction', 8, 2)->default(0)->comment('خصم السلف');
            $table->decimal('absence_deduction', 8, 2)->default(0)->comment('خصم الغياب');
            $table->decimal('late_deduction', 8, 2)->default(0)->comment('خصم التأخير');
            $table->decimal('other_deductions', 8, 2)->default(0)->comment('خصومات أخرى');
            $table->text('deductions_details')->nullable()->comment('تفاصيل الخصومات JSON');
            
            // الحضور والغياب
            $table->integer('working_days')->comment('أيام العمل في الشهر');
            $table->integer('attended_days')->comment('أيام الحضور');
            $table->integer('absent_days')->default(0)->comment('أيام الغياب');
            $table->integer('late_days')->default(0)->comment('أيام التأخير');
            $table->decimal('overtime_hours', 5, 2)->default(0)->comment('ساعات العمل الإضافي');
            $table->decimal('overtime_rate', 5, 2)->default(0)->comment('معدل ساعة العمل الإضافي');
            
            // إجمالي الراتب
            $table->decimal('gross_salary', 10, 2)->comment('إجمالي الراتب قبل الخصومات');
            $table->decimal('total_allowances', 10, 2)->comment('إجمالي البدلات');
            $table->decimal('total_deductions', 10, 2)->comment('إجمالي الخصومات');
            $table->decimal('net_salary', 10, 2)->comment('صافي الراتب');
            
            // معلومات الدفع
            $table->enum('payment_method', ['bank_transfer', 'cash', 'cheque'])->default('bank_transfer')->comment('طريقة الدفع');
            $table->string('payment_reference', 100)->nullable()->comment('مرجع الدفع');
            $table->enum('payment_status', ['pending', 'paid', 'cancelled'])->default('pending')->comment('حالة الدفع');
            $table->timestamp('paid_at')->nullable()->comment('تاريخ الدفع الفعلي');
            
            // ملاحظات
            $table->text('notes')->nullable()->comment('ملاحظات');
            $table->text('payslip_data')->nullable()->comment('بيانات قسيمة الراتب JSON');
            
            // معلومات المعتمد والمحاسب
            $table->unsignedBigInteger('approved_by')->nullable()->comment('معتمد من قبل');
            $table->timestamp('approved_at')->nullable()->comment('تاريخ الاعتماد');
            $table->unsignedBigInteger('processed_by')->nullable()->comment('معالج من قبل');
            $table->timestamp('processed_at')->nullable()->comment('تاريخ المعالجة');
            
            // تواريخ النظام
            $table->timestamps();
            $table->softDeletes()->comment('حذف ناعم');
            
            // فهارس فريدة
            $table->unique(['employee_id', 'pay_year', 'pay_month'], 'unique_employee_payroll');
            
            // فهارس للبحث
            $table->index(['tenant_id', 'pay_year', 'pay_month']);
            $table->index(['pay_date', 'payment_status']);
            $table->index(['employee_id', 'pay_date']);
            $table->index(['payment_status', 'pay_date']);
            
            // المفاتيح الخارجية
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};