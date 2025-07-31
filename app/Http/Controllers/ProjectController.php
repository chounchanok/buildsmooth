<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::latest()->paginate(10);
        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     * แสดงฟอร์มสำหรับสร้างโครงการใหม่
     */
    public function create()
    {
        return view('projects.form');
    }

    /**
     * Store a newly created resource in storage.
     * บันทึกข้อมูลโครงการใหม่ลงฐานข้อมูล
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string',
        ]);

        Project::create([
            'project_id' => Str::uuid(),
            'created_by_user_id' => Auth::id(),
        ] + $request->all());

        return redirect()->route('projects.index')->with('success', 'บันทึกโครงการใหม่สำเร็จ');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        // โดยทั่วไปจะ redirect ไปที่หน้า edit
        return redirect()->route('projects.edit', $project->project_id);
    }

    /**
     * Show the form for editing the specified resource.
     * แสดงฟอร์มพร้อมข้อมูลเดิมเพื่อแก้ไข
     */
    public function edit(Project $project)
    {
        return view('projects.form', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     * อัปเดตข้อมูลในฐานข้อมูล
     */
    public function update(Request $request, Project $project)
    {
        $request->validate([
            'project_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|string',
        ]);

        $project->update($request->all());

        return redirect()->route('projects.index')->with('success', 'อัปเดตข้อมูลโครงการสำเร็จ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'ลบโครงการสำเร็จ');
    }
}
