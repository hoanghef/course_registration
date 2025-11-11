<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_code',
        'program_name',
        'major',
        'total_credits',
        'duration_years',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_credits' => 'integer',
        'duration_years' => 'float',
    ];

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'program_subjects')
            ->withPivot('semester_recommended', 'is_required')
            ->withTimestamps();
    }
}