<?php

namespace App\Main;
use Illuminate\Support\Facades\Auth;

class TopMenu
{
    /**
     * List of side menu items.
     *
     * @return \Illuminate\Http\Response
     */
    public static function menu()
    {
        $menu = [];
        $user = Auth::user();
        $role_id = $user ? $user->role_id : null;

        // ถ้าไม่มี role_id (ยังไม่ได้ login) ให้ return array ว่าง
        if (!$role_id) {
            return $menu;
        }

        // ========== เมนูพื้นฐานสำหรับทุกคนที่ Login ==========
        $menu['dashboard'] = [
            'icon' => 'home',
            'route_name' => 'dashboard',
            'params' => ['layout' => 'side-menu'],
            'title' => 'หน้าหลัก'
        ];


        // ========== เมนูสำหรับผู้ดูแลระบบและผู้จัดการโครงการ (Role 1, 2) ==========
        if (in_array($role_id, [1, 2])) {
            $menu['devider-1'] = 'devider'; // <-- แก้ไขตรงนี้
            $menu['management'] = [
                'icon' => 'box',
                'title' => 'จัดการข้อมูล',
                'sub_menu' => [
                    'projects' => [
                        'icon' => '',
                        'route_name' => 'projects.index',
                        'params' => ['layout' => 'side-menu'],
                        'title' => 'จัดการโครงการ'
                    ],
                    'teams' => [
                        'icon' => '',
                        'route_name' => 'teams.index',
                        'params' => ['layout' => 'side-menu'],
                        'title' => 'จัดการทีมงาน'
                    ],
                    'assets' => [
                        'icon' => '',
                        'route_name' => 'assets.index',
                        'params' => ['layout' => 'side-menu'],
                        'title' => 'จัดการสินทรัพย์'
                    ]
                ]
            ];
        }


        // ========== เมนูสำหรับผู้ดูแลระบบสูงสุดเท่านั้น (Role 1) ==========
        if ($role_id == 1) {
            $menu['user_management'] = [
                'icon' => 'users',
                'title' => 'จัดการผู้ใช้งาน',
                'sub_menu' => [
                    'users' => [
                        'icon' => '',
                        'route_name' => 'users.index',
                        'params' => ['layout' => 'side-menu'],
                        'title' => 'ผู้ใช้งานทั้งหมด'
                    ],
                    'roles' => [
                        'icon' => '',
                        'route_name' => 'roles.index',
                        'params' => ['layout' => 'side-menu'],
                        'title' => 'ตำแหน่งและสิทธิ์'
                    ]
                ]
            ];
        }

        // ========== เมนูสำหรับ Staff (Role 4) ==========
        if ($role_id == 4) {
             $menu['my_work'] = [
                'icon' => 'clipboard',
                'title' => 'งานของฉัน',
                'sub_menu' => [
                    'my_projects' => [
                        'icon' => '',
                        'route_name' => 'my-projects.index',
                        'params' => ['layout' => 'side-menu'],
                        'title' => 'โครงการที่ได้รับมอบหมาย'
                    ],
                    'my_timesheets' => [
                        'icon' => '',
                        'route_name' => 'my-timesheets.index',
                        'params' => ['layout' => 'side-menu'],
                        'title' => 'บันทึกเวลาทำงาน'
                    ]
                ]
            ];
        }


        // ========== เมนูรายงาน (Role 1, 2, 3) ==========
        if (in_array($role_id, [1, 2, 3])) {
            $menu['devider-2'] = 'devider'; // <-- แก้ไขตรงนี้
            $menu['reports'] = [
                'icon' => 'printer',
                'title' => 'รายงาน',
                'sub_menu' => [
                    'timesheet_report' => [
                        'icon' => '',
                        'route_name' => 'reports.timesheet',
                        'params' => ['layout' => 'side-menu'],
                        'title' => 'รายงาน Timesheet'
                    ]
                ]
            ];
        }
        
        // ========== เมนูสำหรับลูกค้า (Role 5) ==========
        if ($role_id == 5) {
             $menu['client_report'] = [
                'icon' => 'file-text',
                'route_name' => 'reports.client',
                'params' => ['layout' => 'side-menu'],
                'title' => 'รายงานความคืบหน้า'
            ];
        }


        return $menu;
    }
}
