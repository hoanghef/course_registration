<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_code',
        'subject_id',
        'teacher_id',
        'semester',
        'academic_year',
        'max_students',
        'current_students',
        'schedule',
        'room',
        'status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'max_students' => 'integer',
        'current_students' => 'integer',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function schedules()
    {
        return $this->hasMany(CourseSchedule::class);
    }
}