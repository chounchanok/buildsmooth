<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids; // 1. Import HasUuids trait
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory, HasUuids; // 2. เพิ่ม HasUuids เข้าไปใช้งาน

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'project_id'; // 3. บอก Laravel ว่า Primary Key ของเราคือ 'project_id'

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'project_type',
        'project_type_other',
        'project_code',
        'reference_code',
        'project_name', // ใช้ 'project_name' ให้ตรงกับ migration
        'po_number',
        'location_address',
        'location_map_link',
        'is_subscribed',
        'team_members',
        'customer_contacts',
        'start_date',
        'end_date',
        'progress',
        'description',
        'image_description',
        'image_paths',
        'document_paths',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'progress' => 'integer',
        'team_members' => 'array',
        'customer_contacts' => 'array',
        'image_paths' => 'array',
        'document_paths' => 'array',
    ];
}
