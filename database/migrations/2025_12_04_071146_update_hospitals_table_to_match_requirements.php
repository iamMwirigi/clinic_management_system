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
        Schema::table('hospitals', function (Blueprint $table) {
            // Add new columns
            $table->string('county', 50)->after('address');
            $table->string('logo_url')->nullable()->after('email');
            $table->boolean('is_active')->default(true)->after('subscription_plan');

            // Modify existing columns
            $table->string('subscription_plan', 50)->default('basic')->change();
            $table->string('phone', 15)->nullable()->change();

            // Drop old columns
            $table->dropColumn(['paybill_number', 'subscription_status', 'subscription_expires_at', 'settings']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hospitals', function (Blueprint $table) {
            // Re-add old columns
            $table->string('paybill_number')->nullable();
            $table->enum('subscription_status', ['active', 'inactive', 'suspended'])->default('active');
            $table->timestamp('subscription_expires_at')->nullable();
            $table->longText('settings')->nullable();

            // Drop the new columns
            $table->dropColumn(['county', 'logo_url', 'is_active']);
        });
    }
};
