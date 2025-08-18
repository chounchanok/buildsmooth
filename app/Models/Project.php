<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

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
        'name',
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
        'is_subscribed' => 'boolean',
        'progress' => 'integer',
        'team_members' => 'array',
        'customer_contacts' => 'array',
        'image_paths' => 'array',
        'document_paths' => 'array',
    ];
}
