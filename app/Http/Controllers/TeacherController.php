<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherController extends Controller
{
    /**
     * Danh sách lớp đang dạy
     */
    public function myCourses(Request $request)
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        $query = Course::with(['subject', 'schedules'])
            ->withCount(['registrations as approved_students_count' => function($q) { $q->where('status', 'approved'); }])
            ->where('teacher_id', $teacher->id);

        // Filter by semester
        if ($request->has('semester')) {
            $query->where('semester', $request->semester);
        }

    $courses = $query->get();

        return response()->json($courses);
    }

    /**
     * Chi tiết lớp học
     */
    public function courseDetail($id)
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        $course = Course::with(['subject', 'schedules', 'registrations'])
            ->where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        return response()->json($course);
    }

    /**
     * Danh sách sinh viên trong lớp
     */
    public function courseStudents($id)
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        $course = Course::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $students = $course->registrations()
            ->with(['student.user'])
            ->where('status', 'approved')
            ->get()
            ->map(function($registration) {
                return [
                    'registration_id' => $registration->id,
                    'student_id' => $registration->student->id,
                    'student_code' => $registration->student->student_code,
                    'full_name' => $registration->student->user->full_name,
                    'email' => $registration->student->user->email,
                    'phone' => $registration->student->user->phone,
                    'class_name' => $registration->student->class_name,
                    'registration_date' => $registration->registration_date,
                ];
            });

        return response()->json([
            'course' => $course->load('subject'),
            'students' => $students,
        ]);
    }

    /**
     * Lịch giảng dạy
     */
    public function mySchedule()
    {
        $user = Auth::user();
        $teacher = $user->teacher;

        $courses = Course::with(['subject', 'schedules'])
            ->withCount(['registrations as approved_students_count' => function($q) { $q->where('status', 'approved'); }])
            ->where('teacher_id', $teacher->id)
            ->whereIn('status', ['open', 'closed'])
            ->get();

        $schedule = [];
        foreach ($courses as $course) {
            foreach ($course->schedules as $courseSchedule) {
                $schedule[] = [
                    'course_id' => $course->id,
                    'course_code' => $course->course_code,
                    'subject_name' => $course->subject->subject_name,
                    'day_of_week' => $courseSchedule->day_of_week,
                    'day_name' => $this->getDayName($courseSchedule->day_of_week),
                    'start_time' => $courseSchedule->start_time,
                    'end_time' => $courseSchedule->end_time,
                    'room' => $courseSchedule->room,
                    'current_students' => $course->current_students,
                    'max_students' => $course->max_students,
                ];
            }
        }

        usort($schedule, function($a, $b) {
            if ($a['day_of_week'] != $b['day_of_week']) {
                return $a['day_of_week'] - $b['day_of_week'];
            }
            return strcmp($a['start_time'], $b['start_time']);
        });

        return response()->json($schedule);
    }

    private function getDayName($dayNumber)
    {
        $days = [
            2 => 'Thứ 2',
            3 => 'Thứ 3',
            4 => 'Thứ 4',
            5 => 'Thứ 5',
            6 => 'Thứ 6',
            7 => 'Thứ 7',
            8 => 'Chủ nhật',
        ];
        return $days[$dayNumber] ?? '';
    }
}