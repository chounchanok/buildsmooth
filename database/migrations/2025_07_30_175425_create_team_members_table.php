<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->foreignUuid('user_id')->constrained('users', 'user_id')->onDelete('cascade');
            $table->foreignUuid('team_id')->constrained('teams', 'team_id')->onDelete('cascade');
            $table->primary(['user_id', 'team_id']); // Composite Primary Key
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_members');
    }
};