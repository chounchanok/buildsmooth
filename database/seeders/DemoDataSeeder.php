<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User; // Import User model
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $pmUser = User::where('email', 'pm@buildsmoot.com')->first();
        $staffUser1 = User::where('email', 'staff1@buildsmoot.com')->first();
        $staffUser2 = User::where('email', 'staff2@buildsmoot.com')->first();

        $teamAlphaId = Str::uuid()->toString();
        DB::table('teams')->insert([
            'team_id' => $teamAlphaId,
            'team_name' => 'ทีม Alpha (ก่อสร้าง)',
            'team_lead_id' => $pmUser->user_id,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        DB::table('team_members')->insert([
            ['user_id' => $pmUser->user_id, 'team_id' => $teamAlphaId],
            ['user_id' => $staffUser1->user_id, 'team_id' => $teamAlphaId],
        ]);
        
        $projectAId = Str::uuid()->toString();
        $projectBId = Str::uuid()->toString();

        DB::table('projects')->insert([
            [
                'project_id' => $projectAId,
                'project_name' => 'โครงการคอนโด The Grand Buildsmoot',
                'address' => '123 ถ.สุขุมวิท กรุงเทพฯ',
                'status' => 'In Progress',
                'created_by_user_id' => $pmUser->user_id,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'project_id' => $projectBId,
                'project_name' => 'โครงการหมู่บ้าน Buildsmoot Ville',
                'address' => '456 ถ.รามคำแหง กรุงเทพฯ',
                'status' => 'Not Started',
                'created_by_user_id' => $pmUser->user_id,
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
        
        DB::table('project_assignments')->insert([
            'assignment_id' => Str::uuid()->toString(),
            'user_id' => $staffUser1->user_id,
            'project_id' => $projectAId,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('project_assignments')->insert([
            'assignment_id' => Str::uuid()->toString(),
            'user_id' => $staffUser2->user_id,
            'project_id' => $projectAId,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        
        DB::table('assets')->insert([
            [
                'asset_id' => Str::uuid()->toString(),
                'asset_name' => 'รถแบคโฮ CAT-01',
                'asset_code' => 'BK-CAT-001',
                'status' => 'In Use',
                'project_id' => $projectAId,
                'assigned_to_user_id' => $staffUser1->user_id,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'asset_id' => Str::uuid()->toString(),
                'asset_name' => 'เครื่องปั่นไฟ',
                'asset_code' => 'GEN-005',
                'status' => 'Available',
                'project_id' => null,
                'assigned_to_user_id' => null,
                'created_at' => now(), 'updated_at' => now(),
            ]
        ]);
        
         DB::table('timesheets')->insert([
            [
                'timesheet_id' => Str::uuid()->toString(),
                'user_id' => $staffUser1->user_id,
                'project_id' => $projectAId,
                'date_worked' => '2025-07-30',
                'hours_worked' => 8.00,
                'task_description' => 'ควบคุมการเทปูนฐานรากอาคาร A',
                'created_at' => now(), 'updated_at' => now(),
            ],
             [
                'timesheet_id' => Str::uuid()->toString(),
                'user_id' => $staffUser2->user_id,
                'project_id' => $projectAId,
                'date_worked' => '2025-07-30',
                'hours_worked' => 6.50,
                'task_description' => 'ตรวจสอบความปลอดภัยไซต์งาน',
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }
}