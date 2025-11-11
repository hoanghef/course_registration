<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    use HasFactory;

    public $timestamps = false; // Chỉ có created_at

    protected $fillable = [
        'user_id',
        'action',
        'table_name',
        'record_id',
        'old_value',
        'new_value',
        'ip_address',
        'user_agent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}