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
        Schema::create('hospitals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('paybill_number')->nullable(); // M-Pesa paybill
            $table->enum('subscription_status', ['active', 'inactive', 'suspended'])->default('active');
            $table->enum('subscription_plan', ['basic', 'premium', 'enterprise'])->default('basic');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->json('settings')->nullable(); // Hospital-specific settings
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hospitals');
    }
};
