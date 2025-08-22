<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // ดึงข้อมูลทีมทั้งหมด (ไม่ใช่แค่ทีละหน้า) เพื่อส่งให้ DataTable
        $teams = Team::with('teamLead')->latest()->get();
        return view('teams.index', compact('teams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // ดึงรายชื่อผู้ใช้ที่มี role Project Manager หรือ Team Lead มาเป็นตัวเลือกหัวหน้าทีม
        $teamLeads = User::whereIn('role_id', [3, 4])->get();
        return view('teams.form', compact('teamLeads'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'team_name' => 'required|string|max:100',
            'team_lead_id' => 'nullable|uuid|exists:users,user_id',
            'description' => 'nullable|string',
        ]);

        Team::create([
            'team_id' => Str::uuid(),
        ] + $request->all());

        return redirect()->route('teams.index')->with('success', 'สร้างทีมใหม่สำเร็จ');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        $teamLeads = User::whereIn('role_id', [3, 4])->get();
        return view('teams.form', compact('team', 'teamLeads'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        $request->validate([
            'team_name' => 'required|string|max:100',
            'team_lead_id' => 'nullable|uuid|exists:users,user_id',
            'description' => 'nullable|string',
        ]);

        $team->update($request->all());

        return redirect()->route('teams.index')->with('success', 'อัปเดตข้อมูลทีมสำเร็จ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        $team->delete();
        return redirect()->route('teams.index')->with('success', 'ลบทีมสำเร็จ');
    }
}
