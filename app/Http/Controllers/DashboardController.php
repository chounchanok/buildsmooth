<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Project;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard with summary data.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // ดึงข้อมูลสรุปจากฐานข้อมูล
        $projectCount = Project::count();
        $assetCount = Asset::count();
        $userCount = User::count();

        // ดึงโครงการล่าสุด 5 โครงการเพื่อไปแสดงผล
        $recentProjects = Project::latest()->take(5)->get();

        // ส่งข้อมูลไปยัง View
        return view('dashboard.dashboard', [
            'projectCount' => $projectCount,
            'assetCount' => $assetCount,
            'userCount' => $userCount,
            'recentProjects' => $recentProjects,
        ]);
    }
}
