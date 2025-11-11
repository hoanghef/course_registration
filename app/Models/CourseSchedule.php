<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'day_of_week',
        'start_time',
        'end_time',
        'room',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}