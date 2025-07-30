<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['role_id' => 1, 'role_name' => 'Super Admin', 'description' => 'จัดการได้ทุกส่วนของระบบ'],
            ['role_id' => 2, 'role_name' => 'Project Manager', 'description' => 'จัดการโครงการและทีมงาน'],
            ['role_id' => 3, 'role_name' => 'Team Lead', 'description' => 'จัดการทีมและ Timesheet'],
            ['role_id' => 4, 'role_name' => 'Staff', 'description' => 'ดูโครงการ, บันทึก Timesheet'],
            ['role_id' => 5, 'role_name' => 'Client', 'description' => 'ดูรายงานความคืบหน้า'],
        ]);
    }
}