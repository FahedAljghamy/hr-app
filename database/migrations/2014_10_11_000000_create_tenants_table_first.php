<?php

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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('domain')->unique();
            $table->string('subdomain')->unique()->nullable();
            $table->string('database_name')->unique();
            $table->string('contact_email');
            $table->string('contact_phone')->nullable();
            $table->string('company_name');
            $table->text('address')->nullable();
            $table->string('logo')->nullable();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active');
            $table->enum('subscription_plan', ['basic', 'premium', 'enterprise'])->default('basic');
            $table->date('subscription_start_date');
            $table->date('subscription_end_date');
            $table->decimal('monthly_fee', 10, 2)->default(0.00);
            $table->integer('max_employees')->default(50);
            $table->json('features')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
