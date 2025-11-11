<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    /**
     * Danh sách tất cả courses
     */
    public function index(Request $request)
    {
        $query = Course::with(['subject', 'teacher.user', 'schedules'])
            ->withCount(['registrations as approved_students_count' => function($q) { $q->where('status', 'approved'); }]);

        // Filter by semester
        if ($request->has('semester')) {
            $query->where('semester', $request->semester);
        }

        // Filter by academic year
        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search by subject name or code
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('subject', function($q) use ($search) {
                $q->where('subject_name', 'like', "%{$search}%")
                  ->orWhere('subject_code', 'like', "%{$search}%");
            });
        }

        $courses = $query->paginate(20);

        return response()->json($courses);
    }

    /**
     * Chi tiết course
     */
    public function show($id)
    {
        $course = Course::with(['subject', 'teacher.user', 'schedules', 'registrations.student.user'])
            ->findOrFail($id);

        return response()->json($course);
    }

    /**
     * Danh sách courses có thể đăng ký (Sinh viên)
     */
    public function availableCourses(Request $request)
    {
        $query = Course::with(['subject', 'teacher.user', 'schedules'])
            ->withCount(['registrations as approved_students_count' => function($q) { $q->where('status', 'approved'); }])
            ->where('status', 'open')
            ->whereRaw('approved_students_count < max_students');

        // Filter by semester
        if ($request->has('semester')) {
            $query->where('semester', $request->semester);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('subject', function($q) use ($search) {
                $q->where('subject_name', 'like', "%{$search}%")
                  ->orWhere('subject_code', 'like', "%{$search}%");
            });
        }

        $courses = $query->paginate(20);

        return response()->json($courses);
    }
}