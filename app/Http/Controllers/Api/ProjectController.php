<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Exception;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            return ProjectResource::collection(Project::latest()->paginate(10));
        } catch (Exception $e) {
            Log::error('Error fetching projects: ' . $e->getMessage());
            return response()->json(['message' => 'เกิดข้อผิดพลาดในการดึงข้อมูลโครงการ'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ถ้าไม่มี project_code ส่งมา ให้สร้างอัตโนมัติ
        if (!$request->has('project_code') || empty($request->project_code)) {
            $request->merge(['project_code' => $this->auto_generateCode($request->project_type)]);
        }

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

        try {
            $validated['is_subscribed'] = $request->boolean('is_subscribed');
            $validated['team_members'] = $request->team_members ? array_filter($request->team_members) : [];
            $validated['customer_contacts'] = $request->customer_contacts ? array_filter($request->customer_contacts) : [];

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
            
            $project = Project::create($validated);

            return new ProjectResource($project);
        } catch (Exception $e) {
            Log::error('Error creating project: ' . $e->getMessage());
            return response()->json(['message' => 'เกิดข้อผิดพลาดในการสร้างโครงการ'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        try {
            return new ProjectResource($project);
        } catch (Exception $e) {
            Log::error('Error showing project ' . $project->project_id . ': ' . $e->getMessage());
            return response()->json(['message' => 'ไม่พบข้อมูลโครงการ'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update_projects(Request $request, Project $project)
    {

        $validated = $request->validate([
            'project_type' => 'sometimes|required|string|in:โครงการ,บ้าน,อื่นๆ',
            'project_type_other' => 'required_if:project_type,อื่นๆ|nullable|string|max:255',
            'project_code' => 'required_if:project_code,อื่นๆ|nullable|string|max:255',
            'reference_code' => 'nullable|string|max:255',
            'project_name' => 'sometimes|required|string|max:255',
            'po_number' => 'nullable|string|max:255',
            'location_address' => 'nullable|string',
            'location_map_link' => 'nullable|url',
            'is_subscribed' => 'sometimes|boolean',
            'team_members' => 'nullable|array',
            'team_members.*' => 'nullable|string|exists:users,user_id',
            'customer_contacts' => 'nullable|array',
            'customer_contacts.*' => 'nullable|string|exists:users,user_id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'progress' => 'sometimes|required|integer|min:0|max:100',
            'description' => 'nullable|string',
            'image_description' => 'nullable|string',
        ]);


        try {
            if($request->has('is_subscribed')) {
                $validated['is_subscribed'] = $request->boolean('is_subscribed');
            }
            if($request->has('team_members')) {
                $validated['team_members'] = $request->team_members ? array_filter($request->team_members) : [];
            }
            if($request->has('customer_contacts')) {
                $validated['customer_contacts'] = $request->customer_contacts ? array_filter($request->customer_contacts) : [];
            }

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

            dd($request->file(), $validated, $project->project_id);

            $project->update($validated);

            return new ProjectResource($project);
        } catch (Exception $e) {
            Log::error('Error updating project ' . $project->project_id . ': ' . $e->getMessage());
            return response()->json(['message' => 'เกิดข้อผิดพลาดในการอัปเดตโครงการ'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        try {
            $project->delete();
            return response()->noContent();
        } catch (Exception $e) {
            Log::error('Error deleting project ' . $project->project_id . ': ' . $e->getMessage());
            return response()->json(['message' => 'เกิดข้อผิดพลาดในการลบโครงการ'], 500);
        }
    }


    // --- Private Helper Methods for Code Generation ---
    // (โค้ดส่วนนี้เหมือนเดิม)
    private function auto_generateCode($projectType) {
        $prefixMap = [
            'โครงการ' => 'C',
            'บ้าน' => 'H',
            'อื่นๆ' => 'T',
        ];

        if (!isset($prefixMap[$projectType])) {
            return null; // Or throw an exception
        }

        $prefix = $prefixMap[$projectType];
        $allCodes = Project::where('project_code', 'LIKE', $prefix . '%')->pluck('project_code')->toArray();

        if (empty($allCodes)) {
            return $prefix . 'AA00';
        }

        usort($allCodes, [$this, 'customSort']);
        $lastCode = end($allCodes);
        
        $coreCode = substr($lastCode, 1);
        $nextCoreCode = '';

        if ($prefix === 'H' || $prefix === 'T') {
            $nextCoreCode = $this->incrementLLNN($coreCode);
        } elseif ($prefix === 'C') {
            if (preg_match('/^[A-Z]{2}\d{2}$/', $coreCode)) {
                $nextCoreCode = ($coreCode === 'ZZ99') ? '1A00' : $this->incrementLLNN($coreCode);
            } elseif (preg_match('/^[1-9][A-Z]\d{2}$/', $coreCode)) {
                $nextCoreCode = ($coreCode === '9Z99') ? '0000' : $this->incrementNLNN($coreCode);
            } elseif (preg_match('/^\d{4}$/', $coreCode)) {
                if ($coreCode === '9999') return null;
                $nextNum = intval($coreCode) + 1;
                $nextCoreCode = str_pad($nextNum, 4, '0', STR_PAD_LEFT);
            }
        }

        return $nextCoreCode ? $prefix . $nextCoreCode : null;
    }
    private function incrementLLNN($code) {
        $letters = substr($code, 0, 2);
        $numbers = intval(substr($code, 2, 2));

        $numbers++;
        if ($numbers > 99) {
            $numbers = 0;
            $l2 = $letters[1];
            $l1 = $letters[0];
            
            if ($l2 < 'Z') {
                $l2++;
            } else {
                $l2 = 'A';
                if ($l1 < 'Z') {
                    $l1++;
                } else {
                    return null; // Overflow ZZ99
                }
            }
            $letters = $l1 . $l2;
        }
        return $letters . str_pad($numbers, 2, '0', STR_PAD_LEFT);
    }
    private function incrementNLNN($code) {
        $num_char = $code[0];
        $letter_char = $code[1];
        $numbers = intval(substr($code, 2, 2));

        $numbers++;
        if ($numbers > 99) {
            $numbers = 0;
            if ($letter_char < 'Z') {
                $letter_char++;
            } else {
                $letter_char = 'A';
                if ($num_char < '9') {
                    $num_char++;
                } else {
                    return null; // Overflow 9Z99
                }
            }
        }
        return $num_char . $letter_char . str_pad($numbers, 2, '0', STR_PAD_LEFT);
    }
    private function customSort($a, $b) {
        $patternA = $this->getCodePatternOrder(substr($a, 1));
        $patternB = $this->getCodePatternOrder(substr($b, 1));

        if ($patternA !== $patternB) {
            return $patternA <=> $patternB;
        }
        return $a <=> $b;
    }
    private function getCodePatternOrder($coreCode) {
        if (preg_match('/^[A-Z]{2}\d{2}$/', $coreCode)) return 1;
        if (preg_match('/^[1-9][A-Z]\d{2}$/', $coreCode)) return 2;
        if (preg_match('/^\d{4}$/', $coreCode)) return 3;
        return 0;
    }
}
