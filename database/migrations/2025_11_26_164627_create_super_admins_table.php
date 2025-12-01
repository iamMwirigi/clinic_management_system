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
        Schema::create('super_admins', function (Blueprint $table) {
            $table->id(); // BIGINT (Primary Key), Unique identifier.
            $table->string('name', 100); // Full name.
            $table->string('email', 100)->unique(); // Login email.
            $table->string('phone', 15)->nullable(); // Contact number.
            $table->string('password'); // Securely hashed password. Laravel's default is 255.
            $table->timestamp('last_login_at')->nullable(); // Timestamp of last login.
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('super_admins');
    }
};