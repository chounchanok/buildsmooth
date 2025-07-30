<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Notification extends Model
{
    use HasFactory;

    // เนื่องจาก migration ใช้ $table->uuid()->primary();
    // Laravel จะตั้งชื่อคอลัมน์เป็น 'id' โดยอัตโนมัติ
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'is_read',
        'related_entity_type',
        'related_entity_id',
    ];

    /**
     * Boot the model.
     * ฟังก์ชันนี้จะทำงานอัตโนมัติเพื่อสร้าง UUID ให้กับ Primary Key
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = Str::uuid()->toString();
            }
        });
    }
}
