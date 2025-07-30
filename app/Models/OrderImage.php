<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderImage extends Model
{
    use HasFactory;

    protected $table = 'order_image';
    protected $primaryKey = 'image_id';
    public $timestamps = false; // ปิด timestamps

    protected $fillable = [
        'image_name',
        'image_path',
        'image_order',
        'image_created',
    ];
}
