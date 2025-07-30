<?php

namespace App\Main;
use Illuminate\Support\Facades\Auth;

class SimpleMenu
{
    /**
     * List of simple menu items.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function menu()
    {
        $menu = array();
        $user = Auth::user();
        $position = $user ? $user->position : null;

        if ($position == 1) {
            $menu = [
                'dashboard' => [
                    'icon' => 'home',
                    'route_name' => 'dashboard',
                    'params' => ['layout' => 'side-menu'],
                    'title' => 'หน้าหลัก'
                ],
                'task_manager_list' => [
                    'icon' => 'edit',
                    'title' => 'จัดการใบงาน',
                    'sub_menu' => [
                        'task_manager_list' => [
                            'icon' => '',
                            'route_name' => 'task_manager_list',
                            'params' => ['layout' => 'side-menu'],
                            'title' => 'ใบงานทั้งหมด'
                        ],
                        'task_manager_form' => [
                            'icon' => '',
                            'route_name' => 'task_manager_form',
                            'params' => ['layout' => 'side-menu'],
                            'title' => 'ใบงานใหม่'
                        ]
                    ]
                ],
                'calendar' => [
                    'icon' => 'calendar',
                    'route_name' => 'calendar',
                    'params' => ['layout' => 'side-menu'],
                    'title' => 'ปฏิทินนัดหมาย'
                ],
                'crud' => [
                    'icon' => 'calendar',
                    'route_name' => 'book_list',
                    'params' => ['layout' => 'side-menu'],
                    'title' => 'นัดหมายทั้งหมด'
                ],
            ];
        }

        if ($position == 2) {
            $menu['workplace_list'] = [
                'icon' => 'clipboard',
                'route_name' => 'workplace_list',
                'params' => ['layout' => 'side-menu'],
                'title' => 'ลิสท์งานช่าง'
            ];
            $menu['calendar'] = [
                'icon' => 'calendar',
                'route_name' => 'calendar',
                'params' => ['layout' => 'side-menu'],
                'title' => 'ปฏิทินนัดหมาย'
            ];
        }

        if ($position == 1) {
            $menu['users'] = [
                'icon' => 'codesandbox',
                'title' => 'ข้อมูลหลัก',
                'sub_menu' => [
                    'users-layout-3' => [
                        'icon' => '',
                        'route_name' => 'employee_list',
                        'params' => ['layout' => 'side-menu'],
                        'title' => 'จัดการพนักงาน'
                    ],
                    'product_list' => [
                        'icon' => '',
                        'route_name' => 'product_list',
                        'params' => ['layout' => 'side-menu'],
                        'title' => 'จัดการสินค้า/บริการ'
                    ]
                ]
            ];

            $menu['report'] = [
                'icon' => 'printer',
                'title' => 'รายงาน',
                'sub_menu' => [
                    'report_sale' => [
                        'icon' => '',
                        'route_name' => 'report_sale',
                        'params' => ['layout' => 'side-menu'],
                        'title' => 'รายงานการทำนัดขาย'
                    ],
                    'report_technician' => [
                        'icon' => '',
                        'route_name' => 'report_technician',
                        'params' => ['layout' => 'side-menu'],
                        'title' => 'รายงานการทำงานของช่าง'
                    ],
                    'report_product' => [
                        'icon' => '',
                        'route_name' => 'report_product',
                        'params' => ['layout' => 'side-menu'],
                        'title' => 'รายงานการขายสินค้า'
                    ]
                ]
            ];
        }

        return $menu;
    }
}
