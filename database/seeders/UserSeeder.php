<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 1. Tạo Admin
        User::create([
            'username' => 'admin',
            'email' => 'admin@university.edu.vn',
            'password' => Hash::make('admin123'),
            'full_name' => 'Quản trị viên',
            'phone' => '0123456789',
            'role' => 'admin',
            'is_active' => true,
        ]);

        echo "✅ Đã tạo Admin\n";

        // 2. Tạo Phòng Đào Tạo
        User::create([
            'username' => 'phongdaotao',
            'email' => 'phongdaotao@university.edu.vn',
            'password' => Hash::make('pdt123'),
            'full_name' => 'Phòng Đào Tạo',
            'phone' => '0123456790',
            'role' => 'phong_dao_tao',
            'is_active' => true,
        ]);

        echo "✅ Đã tạo Phòng Đào Tạo\n";

        // 3. Tạo 5 Giảng viên
        $departments = ['Khoa CNTT', 'Khoa Điện', 'Khoa Cơ khí', 'Khoa Kinh tế', 'Khoa Ngoại ngữ'];
        
        for ($i = 1; $i <= 5; $i++) {
            $teacher_user = User::create([
                'username' => 'teacher' . $i,
                'email' => "teacher{$i}@university.edu.vn",
                'password' => Hash::make('teacher123'),
                'full_name' => "Giảng viên {$i}",
                'phone' => "098765432{$i}",
                'role' => 'giang_vien',
                'is_active' => true,
            ]);

            Teacher::create([
                'user_id' => $teacher_user->id,
                'teacher_code' => "GV" . str_pad($i, 4, '0', STR_PAD_LEFT),
                'department' => $departments[($i-1) % 5],
                'degree' => ($i % 2 == 0) ? 'Tiến sĩ' : 'Thạc sĩ',
                'specialization' => 'Chuyên ngành ' . $i,
            ]);
        }

        echo "✅ Đã tạo 5 Giảng viên\n";

        // 4. Tạo 20 Sinh viên
        $majors = ['Công nghệ thông tin', 'Kỹ thuật điện', 'Cơ khí', 'Kinh tế', 'Tiếng Anh'];
        
        for ($i = 1; $i <= 20; $i++) {
            $student_user = User::create([
                'username' => 'student' . $i,
                'email' => "student{$i}@university.edu.vn",
                'password' => Hash::make('student123'),
                'full_name' => "Sinh viên {$i}",
                'phone' => "091234567" . str_pad($i, 2, '0', STR_PAD_LEFT),
                'role' => 'sinh_vien',
                'is_active' => true,
            ]);

            Student::create([
                'user_id' => $student_user->id,
                'student_code' => "SV" . date('Y') . str_pad($i, 4, '0', STR_PAD_LEFT),
                'major' => $majors[($i-1) % 5],
                'academic_year' => '2024-2028',
                'class_name' => 'K' . (2024 + (($i-1) % 4)),
                'gpa' => rand(250, 400) / 100,
            ]);
        }

        echo "✅ Đã tạo 20 Sinh viên\n";
        echo "📊 Tổng: " . User::count() . " users\n";
    }
}