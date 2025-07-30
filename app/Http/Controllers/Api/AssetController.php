<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Asset; // ต้องสร้าง Model Asset ก่อน
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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
        $validator = Validator::make($request->all(), [
            'asset_name' => 'required|string|max:255',
            'asset_code' => 'nullable|string|max:100|unique:assets,asset_code',
            'description' => 'nullable|string',
            'project_id' => 'nullable|uuid|exists:projects,project_id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $asset = Asset::create([
            'asset_id' => Str::uuid(),
            'asset_name' => $request->asset_name,
            'asset_code' => $request->asset_code,
            'description' => $request->description,
            'project_id' => $request->project_id,
            'assigned_to_user_id' => auth()->id(), // กำหนดให้ผู้สร้างเป็นผู้ดูแลเบื้องต้น
        ]);

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
        $validator = Validator::make($request->all(), [
            'asset_name' => 'required|string|max:255',
            'asset_code' => 'nullable|string|max:100|unique:assets,asset_code,' . $asset->asset_id . ',asset_id',
            'description' => 'nullable|string',
            'status' => 'sometimes|string|max:50',
            'project_id' => 'nullable|uuid|exists:projects,project_id',
            'assigned_to_user_id' => 'nullable|uuid|exists:users,user_id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $asset->update($request->all());

        return response()->json($asset);
    }

    /**
     * Remove the specified resource from storage.
     * ลบสินทรัพย์
     */
    public function destroy(Asset $asset)
    {
        $asset->delete();
        return response()->json(null, 204); // 204 No Content
    }
}
