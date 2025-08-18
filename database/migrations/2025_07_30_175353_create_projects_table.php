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
        Schema::create('projects', function (Blueprint $table) {
            $table->id(); // ใช้ id() แบบมาตรฐาน
            
            // ข้อมูลหลัก
            $table->string('project_type'); // งานโครงการ, บ้าน, อื่นๆ
            $table->string('project_type_other')->nullable();
            $table->string('project_code')->unique();
            $table->string('reference_code')->nullable();
            $table->string('name'); // ชื่อโครงการ / บ้าน / อื่นๆ
            $table->string('po_number')->nullable();
            
            // สถานที่
            $table->text('location_address')->nullable();
            $table->string('location_map_link')->nullable();
            
            // การตั้งค่า
            $table->boolean('is_subscribed')->default(false);
            
            // ข้อมูลบุคคล (เก็บเป็น JSON)
            $table->json('team_members')->nullable();
            $table->json('customer_contacts')->nullable();
            
            // วันที่และสถานะ
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('progress')->default(0);
            
            // รายละเอียดและไฟล์
            $table->text('description')->nullable(); // รายละเอียดงาน
            $table->text('image_description')->nullable();
            $table->json('image_paths')->nullable();
            $table->json('document_paths')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
};
