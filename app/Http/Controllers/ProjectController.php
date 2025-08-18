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
        $validated = $request->validate([
            'project_type' => 'required|string|in:โครงการ,บ้าน,อื่นๆ',
            'project_type_other' => 'required_if:project_type,อื่นๆ|nullable|string|max:255',
            'project_code' => 'required|string|max:255|unique:projects,project_code',
            'reference_code' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'po_number' => 'nullable|string|max:255',
            'location_address' => 'nullable|string',
            'location_map_link' => 'nullable|url',
            'is_subscribed' => 'nullable|boolean',
            'team_members' => 'nullable|array',
            'team_members.*' => 'nullable|string|max:255',
            'customer_contacts' => 'nullable|array',
            'customer_contacts.*' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'progress' => 'required|integer|min:0|max:100',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048', // 2MB per image
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf|max:5120', // 5MB per document
            'image_description' => 'nullable|string',
        ]);

        // แปลงค่า checkbox
        $validated['is_subscribed'] = $request->has('is_subscribed');
        
        // จัดการข้อมูลทีมงานและลูกค้า (กรองค่าว่างออก)
        $validated['team_members'] = $request->team_members ? array_filter($request->team_members) : [];
        $validated['customer_contacts'] = $request->customer_contacts ? array_filter($request->customer_contacts) : [];

        // จัดการการอัปโหลดไฟล์
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
            'project_type' => 'required|string|in:custom,stock,other',
            'project_code' => 'required|string|max:255|unique:projects,project_code',
            'project_ref' => 'required|string|max:255|unique:projects,project_ref',
            'project_po' => 'required|string|max:255|unique:projects,project_po',
            'project_name' => 'required|string|max:255',
            'customer_name' => 'nullable|string|max:255',
            'location_house_no' => 'nullable|string|max:255',
            'location_subdistrict' => 'nullable|string|max:255',
            'location_district' => 'nullable|string|max:255',
            'location_province' => 'nullable|string|max:255',
            'location_postcode' => 'nullable|string|digits:5',

            'shipping_address_same_as_location' => 'nullable|boolean',
            'shipping_recipient_name' => 'required_if:shipping_address_same_as_location,false|nullable|string|max:255',
            'shipping_address' => 'required_if:shipping_address_same_as_location,false|nullable|string',
            'shipping_subdistrict' => 'required_if:shipping_address_same_as_location,false|nullable|string|max:255',
            'shipping_district' => 'required_if:shipping_address_same_as_location,false|nullable|string|max:255',
            'shipping_province' => 'required_if:shipping_address_same_as_location,false|nullable|string|max:255',
            'shipping_postcode' => 'required_if:shipping_address_same_as_location,false|nullable|string|digits:5',

            'main_customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:255',
            'contact_person_name' => 'nullable|string|max:255',

            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'progress' => 'required|integer|min:0|max:100',
            'description' => 'nullable|string',

            'electrical_plan_status' => 'nullable|string|in:new,existing',
            'plumbing_plan_status' => 'nullable|string|in:new,existing',
            'priority' => 'nullable|string|max:255',
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
