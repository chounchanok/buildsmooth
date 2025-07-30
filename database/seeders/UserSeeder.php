<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = Str::uuid()->toString();
        $pmId = Str::uuid()->toString();
        $staffId1 = Str::uuid()->toString();
        $staffId2 = Str::uuid()->toString();

        DB::table('users')->insert([
            [
                'user_id' => $adminId,
                'first_name' => 'แอดมิน',
                'last_name' => 'สูงสุด',
                'email' => 'admin@buildsmoot.com',
                'phone_number' => '0810000001',
                'password' => Hash::make('password'),
                'role_id' => 1, // Super Admin
                'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'user_id' => $pmId,
                'first_name' => 'สมชาย',
                'last_name' => 'ใจดี',
                'email' => 'pm@buildsmoot.com',
                'phone_number' => '0810000002',
                'password' => Hash::make('password'),
                'role_id' => 2, // Project Manager
                'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
            [
                'user_id' => $staffId1,
                'first_name' => 'สมศรี',
                'last_name' => 'ขยันยิ่ง',
                'email' => 'staff1@buildsmoot.com',
                'phone_number' => '0810000003',
                'password' => Hash::make('password'),
                'role_id' => 4, // Staff
                'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
             [
                'user_id' => $staffId2,
                'first_name' => 'มานะ',
                'last_name' => 'อดทน',
                'email' => 'staff2@buildsmoot.com',
                'phone_number' => '0810000004',
                'password' => Hash::make('password'),
                'role_id' => 4, // Staff
                'is_active' => true,
                'created_at' => now(), 'updated_at' => now(),
            ],
        ]);
    }
}