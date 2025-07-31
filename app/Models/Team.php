<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'team_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'team_id',
        'team_name',
        'team_lead_id',
        'description',
    ];

    /**
     * Get the team lead that owns the team.
     */
    public function teamLead()
    {
        return $this->belongsTo(User::class, 'team_lead_id', 'user_id');
    }
}
