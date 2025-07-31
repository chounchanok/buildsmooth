<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        $users = User::all();
        return view('assets.form', compact('projects', 'users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'asset_name' => 'required|string|max:255',
            'asset_code' => 'nullable|string|max:100|unique:assets,asset_code',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'project_id' => 'nullable|uuid|exists:projects,project_id',
            'assigned_to_user_id' => 'nullable|uuid|exists:users,user_id',
        ]);

        Asset::create([
            'asset_id' => Str::uuid(),
        ] + $request->all());

        return redirect()->route('assets.index')->with('success', 'เพิ่มสินทรัพย์ใหม่สำเร็จ');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        $projects = Project::all();
        $users = User::all();
        return view('assets.form', compact('asset', 'projects', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'asset_name' => 'required|string|max:255',
            'asset_code' => 'nullable|string|max:100|unique:assets,asset_code,' . $asset->asset_id . ',asset_id',
            'description' => 'nullable|string',
            'status' => 'required|string',
            'project_id' => 'nullable|uuid|exists:projects,project_id',
            'assigned_to_user_id' => 'nullable|uuid|exists:users,user_id',
        ]);

        $asset->update($request->all());

        return redirect()->route('assets.index')->with('success', 'อัปเดตข้อมูลสินทรัพย์สำเร็จ');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->route('assets.index')->with('success', 'ลบสินทรัพย์สำเร็จ');
    }
}
