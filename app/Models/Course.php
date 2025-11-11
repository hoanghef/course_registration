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

    /**
     * Accessor to return current students count.
     * Prefer an eager-loaded approved_students_count (from withCount) if available
     * to avoid stale stored value or N+1 queries. Falls back to stored attribute.
     */
    public function getCurrentStudentsAttribute($value)
    {
        // If withCount('registrations as approved_students_count') was used
        if (array_key_exists('approved_students_count', $this->attributes)) {
            return (int) $this->attributes['approved_students_count'];
        }

        // If relation loaded with that alias
        if ($this->relationLoaded('registrations')) {
            return $this->registrations->where('status', 'approved')->count();
        }

        // Fallback to the raw value stored in DB
        return (int) $value;
    }
}