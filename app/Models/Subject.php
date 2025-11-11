<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_code',
        'subject_name',
        'credits',
        'theory_hours',
        'practice_hours',
        'description',
        'prerequisite_subject_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'credits' => 'integer',
        'theory_hours' => 'integer',
        'practice_hours' => 'integer',
    ];

    public function prerequisite()
    {
        return $this->belongsTo(Subject::class, 'prerequisite_subject_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}