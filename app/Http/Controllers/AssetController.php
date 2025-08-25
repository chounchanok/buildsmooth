<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // ดึงข้อมูลสินทรัพย์ทั้งหมดพร้อมความสัมพันธ์
        $assets = Asset::with(['project', 'assignedUser'])->latest()->get();
        return view('assets.index', compact('assets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::all();
        $teamUsers = User::where('role_id', '>=', 3)->orderBy('first_name')->get();
        $customerUsers = User::where('role_id', '<=', 2)->orderBy('first_name')->get();
        return view('assets.form', compact('projects', 'teamUsers', 'customerUsers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_name' => 'required|string|max:255',
            'asset_code' => 'nullable|string|max:100|unique:assets,asset_code',
            'description' => 'nullable|string',
            'status' => 'required|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'project_id' => 'nullable|uuid|exists:projects,project_id',
            'assigned_user' => 'nullable|uuid|exists:users,user_id',
            'team_members' => 'nullable|uuid|exists:users,user_id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf|max:5120',
            'document_detail' => 'nullable|string',
        ]);

        // จัดการการอัปโหลดไฟล์
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $file) {
                $path = $file->store('asset_images', 'public');
                $imagePaths[] = $path;
            }
            $validated['image_paths'] = $imagePaths;
        }

        if ($request->hasFile('documents')) {
            $documentPaths = [];
            foreach ($request->file('documents') as $file) {
                $path = $file->store('asset_documents', 'public');
                $documentPaths[] = $path;
            }
            $validated['document_paths'] = $documentPaths;
        }
        
        // ใช้ข้อมูลที่ผ่าน validation แล้วในการสร้าง Asset
        Asset::create($validated);

        return redirect()->route('assets.index')->with('success', 'เพิ่มสินทรัพย์ใหม่สำเร็จ');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        $projects = Project::all();
        $teamUsers = User::where('role_id', '>=', 3)->orderBy('first_name')->get();
        $customerUsers = User::where('role_id', '<=', 2)->orderBy('first_name')->get();
        return view('assets.form', compact('asset', 'projects', 'teamUsers' , 'customerUsers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'asset_name' => 'sometimes|required|string|max:255',
            'asset_code' => ['nullable', 'string', 'max:100', Rule::unique('assets')->ignore($asset->asset_id, 'asset_id')],
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'project_id' => 'nullable|uuid|exists:projects,project_id',
            'assigned_user' => 'nullable|uuid|exists:users,user_id',
            'team_members' => 'nullable|uuid|exists:users,user_id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf|max:5120',
            'document_detail' => 'nullable|string', // แก้ไขจาก document_detail
            'status' => 'sometimes|string|max:50',
        ]);

        // จัดการการอัปโหลดไฟล์ (ถ้ามีการส่งมา)
        if ($request->hasFile('images')) {
            // (แนะนำ) ลบไฟล์เก่าก่อนอัปโหลดใหม่
            foreach ($asset->image_paths ?? [] as $oldPath) {
                Storage::disk('public')->delete($oldPath);
            }
            $imagePaths = [];
            foreach ($request->file('images') as $file) {
                $path = $file->store('asset_images', 'public');
                $imagePaths[] = $path;
            }
            $validated['image_paths'] = $imagePaths;
        }

        if ($request->hasFile('documents')) {
            // (แนะนำ) ลบไฟล์เก่าก่อนอัปโหลดใหม่
            foreach ($asset->document_paths ?? [] as $oldPath) {
                Storage::disk('public')->delete($oldPath);
            }
            $documentPaths = [];
            foreach ($request->file('documents') as $file) {
                $path = $file->store('asset_documents', 'public');
                $documentPaths[] = $path;
            }
            $validated['document_paths'] = $documentPaths;
        }
        
        // ใช้ข้อมูลที่ผ่าน validation แล้วในการอัปเดต
        $asset->update($validated);

        return redirect()->route('assets.index')->with('success', 'อัปเดตข้อมูลสินทรัพย์สำเร็จ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        // ลบไฟล์ที่เกี่ยวข้องก่อนลบข้อมูลออกจาก DB
        foreach ($asset->image_paths ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }
        foreach ($asset->document_paths ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }

        $asset->delete();
        return redirect()->route('assets.index')->with('success', 'ลบสินทรัพย์สำเร็จ');
    }
}
