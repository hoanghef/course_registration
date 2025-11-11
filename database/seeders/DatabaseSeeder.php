<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        echo "🚀 Bắt đầu seeding...\n\n";

        $this->call([
            UserSeeder::class,
            SubjectSeeder::class,
            CourseSeeder::class,
        ]);
        
        echo "\n✅ ✅ ✅ Seeding hoàn tất!\n\n";
        echo "📝 THÔNG TIN TÀI KHOẢN:\n";
        echo "─────────────────────────────────────────\n";
        echo "👤 Admin:\n";
        echo "   Email: admin@university.edu.vn\n";
        echo "   Pass:  admin123\n\n";
        
        echo "🏢 Phòng Đào Tạo:\n";
        echo "   Email: phongdaotao@university.edu.vn\n";
        echo "   Pass:  pdt123\n\n";
        
        echo "👨‍🏫 Giảng viên (1-5):\n";
        echo "   Email: teacher1@university.edu.vn\n";
        echo "   Pass:  teacher123\n\n";
        
        echo "👨‍🎓 Sinh viên (1-20):\n";
        echo "   Email: student1@university.edu.vn\n";
        echo "   Pass:  student123\n";
        echo "─────────────────────────────────────────\n";
    }
}