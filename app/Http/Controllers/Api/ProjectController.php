<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    // ... index(), show(), update(), destroy() methods remain the same ...
    public function index()
    {
        return ProjectResource::collection(Project::latest()->paginate(10));
    }

    /**
     * Generate a new project code based on the project type.
     * สร้างรหัสโครงการใหม่อัตโนมัติตามประเภทโครงการ
     */
    public function generateCode(Request $request)
    {
        $request->validate(['project_type' => 'required|string|in:โครงการ,บ้าน,อื่นๆ']);
        $newCode = $this->auto_generateCode($request->project_type);

        if ($newCode === null) {
            return response()->json(['error' => 'ไม่สามารถสร้างรหัสโครงการได้, อาจถึงขีดจำกัดแล้ว'], 500);
        }

        return response()->json(['project_code' => $newCode]);
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
            'is_subscribed' => 'nullable|string',
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

        $validated['is_subscribed'] = $request->boolean('is_subscribed');
        $validated['team_members'] = $request->team_members ? array_filter($request->team_members) : [];
        $validated['customer_contacts'] = $request->customer_contacts ? array_filter($request->customer_contacts) : [];

        // ... (File upload logic remains the same) ...

        $project = Project::create($validated);

        return new ProjectResource($project);
    }

    public function show(Project $project)
    {
        return new ProjectResource($project);
    }

    public function update(Request $request, Project $project)
    {
         $validated = $request->validate([
            'project_type' => 'sometimes|required|string|in:โครงการ,บ้าน,อื่นๆ',
            'project_type_other' => 'required_if:project_type,อื่นๆ|nullable|string|max:255',
            'project_code' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('projects')->ignore($project->project_id, 'project_id')],
            'reference_code' => 'nullable|string|max:255',
            'project_name' => 'sometimes|required|string|max:255',
            'po_number' => 'nullable|string|max:255',
            'location_address' => 'nullable|string',
            'location_map_link' => 'nullable|url',
            'is_subscribed' => 'nullable|string',
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

        $project->update($validated);

        return new ProjectResource($project);
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return response()->noContent();
    }


    // --- Private Helper Methods for Code Generation ---

    private function auto_generateCode($projectType)
    {
        $prefixMap = [
            'โครงการ' => 'C',
            'บ้าน' => 'H',
            'อื่นๆ' => 'T',
        ];

        if (!isset($prefixMap[$projectType])) {
            return null; // Or throw an exception
        }

        $prefix = $prefixMap[$projectType];

        // ดึงรหัสล่าสุดของประเภทนั้นๆ มาทั้งหมดเพื่อหาตัวล่าสุดจริงๆ
        $allCodes = Project::where('project_code', 'LIKE', $prefix . '%')->pluck('project_code')->toArray();

        if (empty($allCodes)) {
            return $prefix . 'AA00';
        }

        // จัดเรียงรหัสตาม Logic ที่ซับซ้อน
        usort($allCodes, [$this, 'customSort']);
        $lastCode = end($allCodes);

        $coreCode = substr($lastCode, 1);
        $nextCoreCode = '';

        if ($prefix === 'H' || $prefix === 'T') {
            $nextCoreCode = $this->incrementLLNN($coreCode);
        } elseif ($prefix === 'C') {
            if (preg_match('/^[A-Z]{2}\d{2}$/', $coreCode)) { // Pattern: CAA00
                $nextCoreCode = ($coreCode === 'ZZ99') ? '1A00' : $this->incrementLLNN($coreCode);
            } elseif (preg_match('/^[1-9][A-Z]\d{2}$/', $coreCode)) { // Pattern: C1A00
                $nextCoreCode = ($coreCode === '9Z99') ? '0000' : $this->incrementNLNN($coreCode); // Jump to numeric
            } elseif (preg_match('/^\d{4}$/', $coreCode)) { // Pattern: C0000
                if ($coreCode === '9999') return null; // Limit reached
                $nextNum = intval($coreCode) + 1;
                $nextCoreCode = str_pad($nextNum, 4, '0', STR_PAD_LEFT);
            }
        }

        return $nextCoreCode ? $prefix . $nextCoreCode : null;
    }

    private function incrementLLNN($code)
    {
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

    private function incrementNLNN($code)
    {
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

    private function customSort($a, $b)
    {
        $patternA = $this->getCodePatternOrder(substr($a, 1));
        $patternB = $this->getCodePatternOrder(substr($b, 1));

        if ($patternA !== $patternB) {
            return $patternA <=> $patternB;
        }
        return $a <=> $b;
    }

    private function getCodePatternOrder($coreCode)
    {
        if (preg_match('/^[A-Z]{2}\d{2}$/', $coreCode)) return 1; // CAA00
        if (preg_match('/^[1-9][A-Z]\d{2}$/', $coreCode)) return 2; // C1A00
        if (preg_match('/^\d{4}$/', $coreCode)) return 3; // C0000
        return 0; // Default/Other
    }
}
