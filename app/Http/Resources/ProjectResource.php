<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    // ฟังก์ชัน Helper เพื่อลดการเขียนโค้ดซ้ำซ้อน

    public function toArray($request)
    {
        $getUserNames = function ($userIds) {
            // ตรวจสอบก่อนว่ามีข้อมูลหรือไม่ ถ้าเป็น null ให้ return array ว่าง
            if (empty($userIds)) {
                return [];
            }

            // ค้นหา User จาก ID ทั้งหมดที่มีใน array
            // สมมติว่าคอลัมน์ที่เก็บ UUID ในตาราง users คือ 'id'
            if(is_array($userIds) === false){
                $userIds = json_decode($userIds, true);
            }
            
            return User::whereIn('user_id', $userIds)
                ->get()
                ->map(function ($user) {
                    // ต่อชื่อและนามสกุลเข้าด้วยกัน
                    return trim($user->first_name . ' ' . $user->last_name);
                })
                ->all(); // แปลง Collection กลับเป็น PHP array
        };
        
        return [
            'project_id' => $this->project_id,
            'project_type' => $this->project_type,
            'project_type_other' => $this->project_type_other,
            'project_code' => $this->project_code,
            'reference_code' => $this->reference_code,
            'project_name' => $this->project_name,
            'po_number' => $this->po_number,
            'location_address' => $this->location_address,
            'location_map_link' => $this->location_map_link,
            'is_subscribed' => $this->is_subscribed,
            'team_members' => $getUserNames($this->team_members),
            'customer_contacts' => $getUserNames($this->customer_contacts),
            'start_date' => optional($this->start_date)->format('Y-m-d'),
            'end_date' => optional($this->end_date)->format('Y-m-d'),
            'progress' => $this->progress,
            'description' => $this->description,
            'image_description' => $this->image_description,
            'image_urls' => collect($this->image_paths)->map(fn ($path) => Storage::url($path))->toArray(),
            'document_urls' => collect($this->document_paths)->map(fn ($path) => Storage::url($path))->toArray(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
