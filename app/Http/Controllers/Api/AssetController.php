<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     * แสดงรายการสินทรัพย์ทั้งหมด
     */
    public function index()
    {
        $assets = Asset::latest()->paginate(20);
        return response()->json($assets);
    }

    /**
     * Store a newly created resource in storage.
     * บันทึกสินทรัพย์ใหม่
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_name' => 'required|string|max:255',
            'asset_code' => 'nullable|string|max:100|unique:assets,asset_code',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'project_id' => 'nullable|uuid|exists:projects,project_id',
            'team_members' => 'nullable|array',
            'team_members.*' => 'nullable|string|exists:users,user_id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf|max:5120',
            'document_detail' => 'nullable|string',
        ]);

        // จัดการข้อมูล Array
        $validated['team_members'] = $request->team_members ? array_filter($request->team_members) : [];

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
        
        $asset = Asset::create($validated);

        return response()->json($asset, 201);
    }

    /**
     * Display the specified resource.
     * แสดงข้อมูลสินทรัพย์ชิ้นเดียว
     */
    public function show(Asset $asset)
    {
        return response()->json($asset);
    }

    /**
     * Update the specified resource in storage.
     * อัปเดตข้อมูลสินทรัพย์
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
            'team_members' => 'nullable|array',
            'team_members.*' => 'nullable|string|exists:users,user_id',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf|max:5120',
            'document_detail' => 'nullable|string',
            'status' => 'sometimes|string|max:50',
        ]);
        
        // จัดการข้อมูล Array (ถ้ามีการส่งมา)
        if ($request->has('team_members')) {
            $validated['team_members'] = $request->team_members ? array_filter($request->team_members) : [];
        }

        // จัดการการอัปโหลดไฟล์ (ถ้ามีการส่งมา)
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
        
        $asset->update($validated);

        return response()->json($asset);
    }

    /**
     * Remove the specified resource from storage.
     * ลบสินทรัพย์
     */
    public function destroy(Asset $asset)
    {
        // ลบไฟล์ที่เกี่ยวข้อง
        foreach ($asset->image_paths ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }
        foreach ($asset->document_paths ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }

        $asset->delete();
        return response()->json(null, 204); // 204 No Content
    }
}

