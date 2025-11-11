<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_code',
        'major',
        'academic_year',
        'class_name',
        'gpa',
    ];

    protected $casts = [
        'gpa' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}