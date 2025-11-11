<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Course;
use App\Models\CourseSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    /**
     * Danh sách môn học
     */
    public function index()
    {
        $subjects = Subject::with(['courses', 'prerequisite'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($subjects);
    }

    /**
     * Chi tiết môn học
     */
    public function show($id)
    {
        $subject = Subject::with(['courses.teacher.user', 'prerequisite'])
            ->findOrFail($id);

        return response()->json($subject);
    }

    /**
     * Tạo môn học mới và tự động sinh 5 lớp
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject_code' => 'required|string|max:20|unique:subjects',
            'subject_name' => 'required|string|max:200',
            'credits' => 'required|integer|min:1|max:10',
            'theory_hours' => 'required|integer|min:0',
            'practice_hours' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'prerequisite_subject_id' => 'nullable|exists:subjects,id',
            
            // Thông tin cho các lớp
            'semester' => 'required|string',
            'academic_year' => 'required|string',
            'max_students' => 'required|integer|min:10|max:100',
            'number_of_classes' => 'required|integer|min:1|max:10',
            
            // Lịch học (mảng)
            'schedules' => 'required|array|min:1',
            'schedules.*.day_of_week' => 'required|integer|between:2,8',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
            
            // Danh sách phòng (tùy chọn)
            'rooms' => 'nullable|array',
            
            // Danh sách giảng viên (tùy chọn)
            'teachers' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            // 1. Tạo môn học
            $subject = Subject::create([
                'subject_code' => $request->subject_code,
                'subject_name' => $request->subject_name,
                'credits' => $request->credits,
                'theory_hours' => $request->theory_hours,
                'practice_hours' => $request->practice_hours,
                'description' => $request->description,
                'prerequisite_subject_id' => $request->prerequisite_subject_id,
                'is_active' => true,
            ]);

            // 2. Tự động tạo N lớp
            $numberOfClasses = $request->number_of_classes ?? 5;
            $rooms = $request->rooms ?? [];
            $teachers = $request->teachers ?? [];
            
            $courses = [];

            for ($i = 1; $i <= $numberOfClasses; $i++) {
                $classNumber = str_pad($i, 2, '0', STR_PAD_LEFT);
                
                // Lấy phòng và giảng viên (nếu có)
                $room = $rooms[$i - 1] ?? "A" . (100 + $i);
                $teacherId = $teachers[$i - 1] ?? null;

                // Tạo lớp
                $course = Course::create([
                    'course_code' => $request->subject_code . ".N" . $classNumber,
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacherId,
                    'semester' => $request->semester,
                    'academic_year' => $request->academic_year,
                    'max_students' => $request->max_students,
                    'current_students' => 0,
                    'room' => $room,
                    'status' => 'open',
                    'start_date' => now()->addDays(7),
                    'end_date' => now()->addMonths(4),
                ]);

                // 3. Tạo lịch học cho từng lớp
                foreach ($request->schedules as $schedule) {
                    CourseSchedule::create([
                        'course_id' => $course->id,
                        'day_of_week' => $schedule['day_of_week'],
                        'start_time' => $schedule['start_time'],
                        'end_time' => $schedule['end_time'],
                        'room' => $room,
                    ]);
                }

                $courses[] = $course;
            }

            DB::commit();

            return response()->json([
                'message' => "Đã tạo môn học '{$subject->subject_name}' với {$numberOfClasses} lớp thành công!",
                'subject' => $subject,
                'courses' => $courses,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật môn học
     */
    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $request->validate([
            'subject_code' => 'sometimes|string|max:20|unique:subjects,subject_code,' . $id,
            'subject_name' => 'sometimes|string|max:200',
            'credits' => 'sometimes|integer|min:1|max:10',
            'theory_hours' => 'sometimes|integer|min:0',
            'practice_hours' => 'sometimes|integer|min:0',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $subject->update($request->all());

        return response()->json([
            'message' => 'Cập nhật môn học thành công',
            'subject' => $subject,
        ]);
    }

    /**
     * Xóa môn học
     */
    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);

        // Kiểm tra có lớp nào đang mở không
        $activeCourses = $subject->courses()
            ->whereIn('status', ['open', 'closed'])
            ->count();

        if ($activeCourses > 0) {
            return response()->json([
                'error' => 'Không thể xóa môn học đang có lớp hoạt động'
            ], 400);
        }

        $subject->delete();

        return response()->json([
            'message' => 'Đã xóa môn học thành công'
        ]);
    }

    /**
     * Thêm lớp mới cho môn học đã tồn tại
     */
    public function addCourses(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);

        $request->validate([
            'semester' => 'required|string',
            'academic_year' => 'required|string',
            'number_of_classes' => 'required|integer|min:1|max:10',
            'max_students' => 'required|integer|min:10|max:100',
            'schedules' => 'required|array',
            'rooms' => 'nullable|array',
            'teachers' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            // Đếm số lớp hiện tại để đánh số tiếp
            $existingCount = Course::where('subject_id', $subject->id)
                ->where('semester', $request->semester)
                ->where('academic_year', $request->academic_year)
                ->count();

            $courses = [];
            $numberOfClasses = $request->number_of_classes;
            $rooms = $request->rooms ?? [];
            $teachers = $request->teachers ?? [];

            for ($i = 1; $i <= $numberOfClasses; $i++) {
                $classNumber = str_pad($existingCount + $i, 2, '0', STR_PAD_LEFT);
                $room = $rooms[$i - 1] ?? "A" . (100 + $existingCount + $i);
                $teacherId = $teachers[$i - 1] ?? null;

                $course = Course::create([
                    'course_code' => $subject->subject_code . ".N" . $classNumber,
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacherId,
                    'semester' => $request->semester,
                    'academic_year' => $request->academic_year,
                    'max_students' => $request->max_students,
                    'current_students' => 0,
                    'room' => $room,
                    'status' => 'open',
                    'start_date' => now()->addDays(7),
                    'end_date' => now()->addMonths(4),
                ]);

                foreach ($request->schedules as $schedule) {
                    CourseSchedule::create([
                        'course_id' => $course->id,
                        'day_of_week' => $schedule['day_of_week'],
                        'start_time' => $schedule['start_time'],
                        'end_time' => $schedule['end_time'],
                        'room' => $room,
                    ]);
                }

                $courses[] = $course;
            }

            DB::commit();

            return response()->json([
                'message' => "Đã thêm {$numberOfClasses} lớp mới cho môn '{$subject->subject_name}'",
                'courses' => $courses,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}