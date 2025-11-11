<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    /**
     * Xem profile sinh viên
     */
    public function profile()
    {
        $user = Auth::user();
        $student = $user->student()->with(['registrations.course.subject'])->first();

        return response()->json($student);
    }

    /**
     * Xem lịch học của sinh viên
     */
    public function mySchedule()
    {
        $user = Auth::user();
        $student = $user->student;

        $registrations = Registration::with(['course.subject', 'course.teacher.user', 'course.schedules'])
            ->where('student_id', $student->id)
            ->where('status', 'approved')
            ->get();

        // Group by day of week
        $schedule = [];
        foreach ($registrations as $registration) {
            foreach ($registration->course->schedules as $courseSchedule) {
                $schedule[] = [
                    'day_of_week' => $courseSchedule->day_of_week,
                    'day_name' => $this->getDayName($courseSchedule->day_of_week),
                    'start_time' => $courseSchedule->start_time,
                    'end_time' => $courseSchedule->end_time,
                    'room' => $courseSchedule->room,
                    'subject_name' => $registration->course->subject->subject_name,
                    'subject_code' => $registration->course->subject->subject_code,
                    'teacher_name' => $registration->course->teacher->user->full_name ?? 'Chưa phân công',
                ];
            }
        }

        // Sort by day and time
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