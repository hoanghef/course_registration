<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    public function run()
    {
        $subjects = [
            // Môn cơ bản CNTT
            [
                'subject_code' => 'IT001',
                'subject_name' => 'Nhập môn lập trình',
                'credits' => 4,
                'theory_hours' => 45,
                'practice_hours' => 30,
                'description' => 'Môn học cơ bản về lập trình',
                'prerequisite_subject_id' => null,
            ],
            [
                'subject_code' => 'IT002',
                'subject_name' => 'Cấu trúc dữ liệu và giải thuật',
                'credits' => 4,
                'theory_hours' => 45,
                'practice_hours' => 30,
                'description' => 'Các cấu trúc dữ liệu cơ bản',
                'prerequisite_subject_id' => 1, // Cần học IT001 trước
            ],
            [
                'subject_code' => 'IT003',
                'subject_name' => 'Lập trình hướng đối tượng',
                'credits' => 4,
                'theory_hours' => 45,
                'practice_hours' => 30,
                'description' => 'Lập trình OOP với Java/C++',
                'prerequisite_subject_id' => 1,
            ],
            [
                'subject_code' => 'IT004',
                'subject_name' => 'Cơ sở dữ liệu',
                'credits' => 4,
                'theory_hours' => 45,
                'practice_hours' => 30,
                'description' => 'Thiết kế và quản trị CSDL',
                'prerequisite_subject_id' => null,
            ],
            [
                'subject_code' => 'IT005',
                'subject_name' => 'Mạng máy tính',
                'credits' => 3,
                'theory_hours' => 45,
                'practice_hours' => 0,
                'description' => 'Các khái niệm cơ bản về mạng',
                'prerequisite_subject_id' => null,
            ],
            [
                'subject_code' => 'IT006',
                'subject_name' => 'Phát triển ứng dụng Web',
                'credits' => 4,
                'theory_hours' => 30,
                'practice_hours' => 45,
                'description' => 'Xây dựng web với HTML, CSS, JS, PHP',
                'prerequisite_subject_id' => 4,
            ],
            [
                'subject_code' => 'IT007',
                'subject_name' => 'Phát triển ứng dụng Mobile',
                'credits' => 4,
                'theory_hours' => 30,
                'practice_hours' => 45,
                'description' => 'Phát triển app Android/iOS',
                'prerequisite_subject_id' => 3,
            ],
            [
                'subject_code' => 'IT008',
                'subject_name' => 'Trí tuệ nhân tạo',
                'credits' => 3,
                'theory_hours' => 45,
                'practice_hours' => 0,
                'description' => 'Các kỹ thuật AI cơ bản',
                'prerequisite_subject_id' => 2,
            ],
            
            // Môn Toán
            [
                'subject_code' => 'MA001',
                'subject_name' => 'Toán cao cấp 1',
                'credits' => 4,
                'theory_hours' => 60,
                'practice_hours' => 0,
                'description' => 'Giải tích 1 biến',
                'prerequisite_subject_id' => null,
            ],
            [
                'subject_code' => 'MA002',
                'subject_name' => 'Toán cao cấp 2',
                'credits' => 4,
                'theory_hours' => 60,
                'practice_hours' => 0,
                'description' => 'Giải tích nhiều biến',
                'prerequisite_subject_id' => 9,
            ],
            
            // Môn Tiếng Anh
            [
                'subject_code' => 'EN001',
                'subject_name' => 'Tiếng Anh 1',
                'credits' => 3,
                'theory_hours' => 45,
                'practice_hours' => 0,
                'description' => 'Tiếng Anh cơ bản',
                'prerequisite_subject_id' => null,
            ],
            [
                'subject_code' => 'EN002',
                'subject_name' => 'Tiếng Anh 2',
                'credits' => 3,
                'theory_hours' => 45,
                'practice_hours' => 0,
                'description' => 'Tiếng Anh nâng cao',
                'prerequisite_subject_id' => 11,
            ],
        ];

        foreach ($subjects as $subject) {
            Subject::create($subject);
        }

        echo "✅ Đã tạo " . Subject::count() . " môn học\n";
    }
}