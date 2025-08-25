<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'asset_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'asset_name',
        'asset_code',
        'description',
        'status',
        'project_id',
        'assigned_to_user_id',
        'start_date',
        'end_date',
        'team_members',
        'image_paths',
        'document_paths',
        'document_detail',
    ];

    /**
     * Get the project that the asset belongs to.
     */

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'team_members' => 'array',
        'image_paths' => 'array',
        'document_paths' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    /**
     * Get the user that is assigned to the asset.
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id', 'user_id');
    }
}

