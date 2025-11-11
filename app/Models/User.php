<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'full_name',
        'phone',
        'address',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // Role checking methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isPhongDaoTao()
    {
        return $this->role === 'phong_dao_tao';
    }

    public function isGiangVien()
    {
        return $this->role === 'giang_vien';
    }

    public function isSinhVien()
    {
        return $this->role === 'sinh_vien';
    }
}