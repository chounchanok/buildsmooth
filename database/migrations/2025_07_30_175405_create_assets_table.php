<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->uuid('asset_id')->primary();
            $table->string('asset_name');
            $table->string('asset_code', 100)->unique()->nullable();
            $table->text('description')->nullable();
            $table->string('status', 50)->default('Available');
            $table->foreignUuid('project_id')->nullable()->constrained('projects', 'project_id')->onDelete('set null');
            $table->foreignUuid('assigned_to_user_id')->nullable()->constrained('users', 'user_id')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};