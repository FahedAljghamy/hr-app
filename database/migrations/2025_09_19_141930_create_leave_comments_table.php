<?php

/**
 * Author: Eng.Fahed
 * Leave Comments Migration - HR System
 * جدول تعليقات الإجازات - نظام شات داخلي
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
        Schema::create('leave_comments', function (Blueprint $table) {
            $table->id();
            
            // ربط بالإجازة والمستخدم
            $table->unsignedBigInteger('leave_id')->comment('معرف الإجازة');
            $table->unsignedBigInteger('user_id')->comment('معرف المستخدم المعلق');
            $table->unsignedBigInteger('tenant_id')->comment('معرف المؤسسة');
            
            // محتوى التعليق
            $table->text('comment')->comment('نص التعليق');
            $table->enum('comment_type', ['general', 'approval', 'rejection', 'modification', 'cancellation'])->default('general')->comment('نوع التعليق');
            
            // المستندات المرفقة
            $table->json('attachments')->nullable()->comment('المستندات المرفقة JSON');
            
            // معلومات التعديل
            $table->boolean('is_edited')->default(false)->comment('هل تم تعديل التعليق');
            $table->timestamp('edited_at')->nullable()->comment('تاريخ آخر تعديل');
            $table->text('edit_history')->nullable()->comment('تاريخ التعديلات JSON');
            
            // معلومات الرؤية والإشعارات
            $table->boolean('is_internal')->default(false)->comment('تعليق داخلي (للإدارة فقط)');
            $table->boolean('notify_employee')->default(true)->comment('إشعار الموظف');
            $table->boolean('notify_manager')->default(true)->comment('إشعار المدير');
            $table->json('read_by')->nullable()->comment('قُرأ من قبل JSON');
            
            // معلومات الحالة
            $table->enum('visibility', ['public', 'private', 'managers_only'])->default('public')->comment('مستوى الرؤية');
            $table->boolean('is_system_generated')->default(false)->comment('تعليق تلقائي من النظام');
            $table->string('system_action', 100)->nullable()->comment('الإجراء الذي ولد التعليق');
            
            // تواريخ النظام
            $table->timestamps();
            $table->softDeletes()->comment('حذف ناعم');
            
            // فهارس
            $table->index(['leave_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['tenant_id', 'comment_type']);
            $table->index(['is_internal', 'visibility']);
            $table->index(['created_at', 'comment_type']);
            
            // المفاتيح الخارجية
            $table->foreign('leave_id')->references('id')->on('leaves')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_comments');
    }
};