<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('user_id')->primary();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('phone_number', 20)->unique()->nullable();
            $table->string('password');
            $table->text('profile_image_url')->nullable();
            $table->string('otp_code', 6)->nullable();
            $table->timestamp('otp_expiry')->nullable();
            $table->foreignId('role_id')->constrained('roles', 'role_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps(); // สร้าง created_at และ updated_at
            $table->softDeletes(); // <-- เพิ่มบรรทัดนี้
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};