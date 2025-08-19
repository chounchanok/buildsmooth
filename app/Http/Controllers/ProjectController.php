<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User; // 1. เพิ่มการ import Model User
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
        // 2. ดึงรายชื่อผู้ใช้ตาม Role แล้วส่งไปที่ View
        $teamUsers = User::where('role_id', '>=', 3)->orderBy('first_name')->get();
        $customerUsers = User::where('role_id', '<=', 2)->orderBy('first_name')->get();
        return view('projects.form', compact('teamUsers', 'customerUsers'));
    }

    /**
     * Store a newly created resource in storage.
     * บันทึกข้อมูลโครงการใหม่ลงฐานข้อมูล
     */
    public function store(Request $request)
    {
        // 3. อัปเดต Validation Rules
        $validated = $request->validate([
            'project_type' => 'required|string|in:โครงการ,บ้าน,อื่นๆ',
            'project_type_other' => 'required_if:project_type,อื่นๆ|nullable|string|max:255',
            'project_code' => 'required|string|max:255|unique:projects,project_code',
            'reference_code' => 'nullable|string|max:255',
            'project_name' => 'required|string|max:255',
            'po_number' => 'nullable|string|max:255',
            'location_address' => 'nullable|string',
            'location_map_link' => 'nullable|url',
            'is_subscribed' => 'nullable|boolean',
            'team_members' => 'nullable|array',
            'team_members.*' => 'nullable|string|exists:users,user_id', // ตรวจสอบว่าเป็น user_id ที่มีอยู่จริง
            'customer_contacts' => 'nullable|array',
            'customer_contacts.*' => 'nullable|string|exists:users,user_id', // ตรวจสอบว่าเป็น user_id ที่มีอยู่จริง
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'progress' => 'required|integer|min:0|max:100',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf|max:5120',
            'image_description' => 'nullable|string',
        ]);

        $validated['is_subscribed'] = $request->has('is_subscribed');
        $validated['team_members'] = $request->team_members ? json_encode($request->team_members) : [];
        $validated['customer_contacts'] = $request->customer_contacts ? json_encode($request->customer_contacts) : [];

        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $file) {
                $path = $file->store('project_images', 'public');
                $imagePaths[] = $path;
            }
            $validated['image_paths'] = $imagePaths;
        }

        if ($request->hasFile('documents')) {
            $documentPaths = [];
            foreach ($request->file('documents') as $file) {
                $path = $file->store('project_documents', 'public');
                $documentPaths[] = $path;
            }
            $validated['document_paths'] = $documentPaths;
        }

        Project::create($validated);

        return redirect()->route('projects.index')->with('success', 'สร้างโครงการสำเร็จแล้ว');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return redirect()->route('projects.edit', $project->project_id);
    }

    /**
     * Show the form for editing the specified resource.
     * แสดงฟอร์มพร้อมข้อมูลเดิมเพื่อแก้ไข
     */
    public function edit(Project $project)
    {
        // 4. ดึงรายชื่อผู้ใช้สำหรับหน้า Edit ด้วย
        $teamUsers = User::where('role_id', '>=', 3)->orderBy('first_name')->get();
        $customerUsers = User::where('role_id', '<=', 2)->orderBy('first_name')->get();
        return view('projects.form', compact('project', 'teamUsers', 'customerUsers'));
    }

    /**
     * Update the specified resource in storage.
     * อัปเดตข้อมูลในฐานข้อมูล
     */
    public function update(Request $request, Project $project)
    {
        // 5. อัปเดต Validation Rules สำหรับหน้า Update
        $validated = $request->validate([
            'project_type' => 'required|string|in:โครงการ,บ้าน,อื่นๆ',
            'project_type_other' => 'required_if:project_type,อื่นๆ|nullable|string|max:255',
            'project_code' => ['required', 'string', 'max:255', Rule::unique('projects')->ignore($project->project_id, 'project_id')],
            'reference_code' => 'nullable|string|max:255',
            'project_name' => 'required|string|max:255',
            'po_number' => 'nullable|string|max:255',
            'location_address' => 'nullable|string',
            'location_map_link' => 'nullable|url',
            'is_subscribed' => 'nullable|boolean',
            'team_members' => 'nullable|array',
            'team_members.*' => 'nullable|string|exists:users,user_id',
            'customer_contacts' => 'nullable|array',
            'customer_contacts.*' => 'nullable|string|exists:users,user_id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'progress' => 'required|integer|min:0|max:100',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf|max:5120',
            'image_description' => 'nullable|string',
        ]);

        $validated['is_subscribed'] = $request->has('is_subscribed');
        $validated['team_members'] = $request->team_members ? json_encode($request->team_members) : [];
        $validated['customer_contacts'] = $request->customer_contacts ? json_encode($request->customer_contacts) : [];

        if ($request->hasFile('images')) {
            // Optional: Delete old images if you want to replace them
            // foreach ($project->image_paths ?? [] as $oldPath) {
            //     Storage::disk('public')->delete($oldPath);
            // }
            $imagePaths = [];
            foreach ($request->file('images') as $file) {
                $path = $file->store('project_images', 'public');
                $imagePaths[] = $path;
            }
            $validated['image_paths'] = $imagePaths;
        }

        if ($request->hasFile('documents')) {
            // Optional: Delete old documents
            // foreach ($project->document_paths ?? [] as $oldPath) {
            //     Storage::disk('public')->delete($oldPath);
            // }
            $documentPaths = [];
            foreach ($request->file('documents') as $file) {
                $path = $file->store('project_documents', 'public');
                $documentPaths[] = $path;
            }
            $validated['document_paths'] = $documentPaths;
        }

        $project->update($validated);

        return redirect()->route('projects.index')->with('success', 'อัปเดตข้อมูลโครงการสำเร็จ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        // Optional: Delete associated files from storage
        // foreach ($project->image_paths ?? [] as $path) {
        //     Storage::disk('public')->delete($path);
        // }
        // foreach ($project->document_paths ?? [] as $path) {
        //     Storage::disk('public')->delete($path);
        // }

        $project->delete();
        return redirect()->route('projects.index')->with('success', 'ลบโครงการสำเร็จ');
    }
}
