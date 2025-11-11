<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RegistrationController extends Controller
{
    /**
     * Sinh viên đăng ký môn học - TỰ ĐỘNG DUYỆT
     */
    public function register(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $user = Auth::user();
        
        if (!$user->isSinhVien()) {
            return response()->json(['error' => 'Chỉ sinh viên mới có thể đăng ký môn học'], 403);
        }

        $student = $user->student;
        $course = Course::findOrFail($request->course_id);

        // Kiểm tra trạng thái lớp học
        if ($course->status !== 'open') {
            return response()->json(['error' => 'Lớp học không mở đăng ký'], 400);
        }

        // Kiểm tra số lượng sinh viên
        if ($course->current_students >= $course->max_students) {
            return response()->json(['error' => 'Lớp học đã đầy'], 400);
        }

        // Kiểm tra đã đăng ký chưa (chỉ kiểm tra đăng ký còn hiệu lực, không tính cái bị hủy)
        $existingRegistration = Registration::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existingRegistration) {
            return response()->json(['error' => 'Bạn đã đăng ký môn học này'], 400);
        }

        DB::beginTransaction();
        try {
            // Kiểm tra xem có đăng ký bị hủy trước đó không
            $cancelledRegistration = Registration::where('student_id', $student->id)
                ->where('course_id', $course->id)
                ->where('status', 'cancelled')
                ->first();

            if ($cancelledRegistration) {
                // Nếu có, update status thành 'approved' thay vì insert mới
                $registration = $cancelledRegistration;
                $registration->update([
                    'status' => 'approved',
                    'approved_by' => 1,
                    'approved_at' => now(),
                ]);
            } else {
                // Nếu không có, tạo đăng ký mới
                $registration = Registration::create([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'status' => 'approved',
                    'approved_by' => 1,
                    'approved_at' => now(),
                ]);
            }

            // Tăng số lượng sinh viên hiện tại
            $course->increment('current_students');

            DB::commit();

            return response()->json([
                'message' => 'Đăng ký môn học thành công!',
                'registration' => $registration->load('course.subject', 'course.teacher.user'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Xem danh sách đăng ký của sinh viên
     */
    public function myRegistrations()
    {
        $user = Auth::user();
        $student = $user->student;

        $registrations = Registration::with(['course.subject', 'course.teacher.user', 'course.schedules'])
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($registrations);
    }

    /**
     * Hủy đăng ký (Sinh viên)
     */
    public function cancel($id)
    {
        $user = Auth::user();
        $student = $user->student;

        $registration = Registration::where('id', $id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        if ($registration->status === 'cancelled') {
            return response()->json(['error' => 'Đăng ký đã bị hủy trước đó'], 400);
        }

        DB::beginTransaction();
        try {
            // Chỉ update status thành cancelled, không decrement current_students
            // vì sinh viên có thể đăng ký lại sau (update record này)
            $registration->update(['status' => 'cancelled']);

            DB::commit();

            return response()->json(['message' => 'Đã hủy đăng ký thành công']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Có lỗi xảy ra: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Danh sách tất cả đăng ký (Phòng đào tạo - CHỈ XEM)
     */
    public function allRegistrations(Request $request)
    {
        $user = Auth::user();

        if (!$user->isPhongDaoTao() && !$user->isAdmin()) {
            return response()->json(['error' => 'Không có quyền truy cập'], 403);
        }

        $query = Registration::with(['student.user', 'course.subject', 'course.teacher.user'])
            ->where('status', '!=', 'cancelled'); // Mặc định loại bỏ đăng ký bị hủy

        // Lọc theo học kỳ
        if ($request->has('semester')) {
            $query->whereHas('course', function($q) use ($request) {
                $q->where('semester', $request->semester);
            });
        }

        // Lọc theo trạng thái (cho phép override filter mặc định)
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $registrations = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json($registrations);
    }
}