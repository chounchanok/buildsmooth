<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $projects = [];
        $projectTypes = ['โครงการ', 'บ้าน', 'อื่นๆ'];

        for ($i = 1; $i <= 10; $i++) {
            $startDate = Carbon::now()->subDays(rand(10, 60));
            $endDate = $startDate->copy()->addDays(rand(30, 180));
            $projectType = $projectTypes[array_rand($projectTypes)];

            $projects[] = [
                'project_id' => Str::uuid(),
                'project_type' => $projectType,
                'project_type_other' => $projectType === 'อื่นๆ' ? 'โครงการพิเศษ ' . $i : null,
                'project_code' => 'SR-E' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'reference_code' => 'SD' . rand(100, 500),
                'project_name' => 'โครงการ ' . Str::random(5) . ' วิลเลจ เฟส ' . ($i % 3 + 1),
                'po_number' => 'PO' . date('Y') . '-' . rand(1000, 9999),
                'location_address' => 'ที่อยู่ตัวอย่าง ' . $i . ', กรุงเทพมหานคร',
                'location_map_link' => 'https://maps.app.goo.gl/example',
                'is_subscribed' => rand(0, 1) == 1,
                'team_members' => json_encode([
                    'สมชาย ใจดี (หัวหน้าโครงการ)',
                    'สมหญิง มุ่งมั่น (สมาชิก 1)',
                    'ภานุ สุขสงบ (สมาชิก 2)',
                ]),
                'customer_contacts' => json_encode([
                    'มานี มีสุข (เจ้าของบ้าน)',
                    'มานะ อดทน (ผู้ติดต่อ)',
                ]),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'progress' => rand(5, 95),
                'description' => 'รายละเอียดงานสำหรับโครงการ ' . $i . ' ที่สร้างขึ้นโดยอัตโนมัติ',
                'image_description' => 'คำอธิบายรูปภาพสำหรับโครงการ ' . $i,
                'image_paths' => json_encode([]), // ใส่ path รูปภาพตัวอย่างได้ที่นี่
                'document_paths' => json_encode([]), // ใส่ path เอกสารตัวอย่างได้ที่นี่
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('projects')->insert($projects);
    }
}
