<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_assignments', function (Blueprint $table) {
            $table->id('assignment_id');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('team_member_id');
            $table->timestamps();

            // แก้ไขจาก references('project_id') เป็น references('id')
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('team_member_id')->references('member_id')->on('team_members')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_assignments');
    }
};
