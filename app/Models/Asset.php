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
        'asset_id',
        'asset_name',
        'asset_code',
        'description',
        'status',
        'project_id',
        'assigned_to_user_id',
    ];
}
