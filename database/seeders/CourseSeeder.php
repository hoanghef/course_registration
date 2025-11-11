<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\CourseSchedule;
use App\Models\Subject;
use App\Models\Teacher;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $subjects = Subject::all();
        $teachers = Teacher::all();
        $semester = 'HK1';
        $academicYear = '2024-2025';

        $courseCount = 0;

        foreach ($subjects as $index => $subject) {
            // Tạo 2 lớp cho mỗi môn
            for ($classNum = 1; $classNum <= 2; $classNum++) {
                $course = Course::create([
                    'course_code' => $subject->subject_code . ".{$classNum}",
                    'subject_id' => $subject->id,
                    'teacher_id' => $teachers->random()->id,
                    'semester' => $semester,
                    'academic_year' => $academicYear,
                    'max_students' => rand(30, 60),
                    'current_students' => 0,
                    'room' => 'A' . rand(101, 505),
                    'status' => 'open',
                    'start_date' => now()->addDays(7),
                    'end_date' => now()->addMonths(4),
                ]);

                // Tạo lịch học (2 buổi/tuần)
                $days = ($classNum == 1) ? [2, 4] : [3, 5]; // Lớp 1: T2,T4 | Lớp 2: T3,T5
                $startTime = ($classNum == 1) ? '07:00:00' : '09:30:00';
                $endTime = ($classNum == 1) ? '09:30:00' : '12:00:00';

                foreach ($days as $day) {
                    CourseSchedule::create([
                        'course_id' => $course->id,
                        'day_of_week' => $day,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'room' => $course->room,
                    ]);
                }

                $courseCount++;
            }
        }

        echo "✅ Đã tạo {$courseCount} lớp môn học\n";
    }
}