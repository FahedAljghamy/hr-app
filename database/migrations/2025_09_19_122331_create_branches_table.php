<?php

/**
 * Author: Eng.Fahed
 * Migration for Branches table
 * إنشاء جدول الفروع للنظام
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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الفرع
            $table->text('address'); // العنوان
            $table->string('location')->nullable(); // الموقع (coordinates أو وصف)
            $table->json('working_hours')->nullable(); // ساعات العمل
            $table->string('phone')->nullable(); // رقم الهاتف
            $table->string('email')->nullable(); // البريد الإلكتروني
            $table->string('manager_name')->nullable(); // اسم المدير
            $table->boolean('is_active')->default(true); // حالة الفرع
            $table->text('description')->nullable(); // وصف الفرع
            
            // Foreign key للـ tenant
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['tenant_id', 'is_active']);
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};