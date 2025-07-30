<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id('role_id'); // ใช้ id() ตาม Laravel convention และตั้งชื่อ PK เอง
            $table->string('role_name', 50);
            $table->text('description')->nullable();
            // Laravel ไม่ต้องใส่ created_at, updated_at ใน migration
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};