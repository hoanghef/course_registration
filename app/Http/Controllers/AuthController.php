<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Đăng ký tài khoản mới
     */
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'full_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'role' => 'required|in:sinh_vien,giang_vien,phong_dao_tao',
            
            // Sinh viên
            'student_code' => 'required_if:role,sinh_vien|unique:students',
            'major' => 'required_if:role,sinh_vien',
            'academic_year' => 'required_if:role,sinh_vien',
            'class_name' => 'nullable|string',
            
            // Giảng viên
            'teacher_code' => 'required_if:role,giang_vien|unique:teachers',
            'department' => 'required_if:role,giang_vien',
            'degree' => 'nullable|string',
            'specialization' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Tạo user
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'role' => $request->role,
                'is_active' => true,
            ]);

            // Tạo profile tương ứng với role
            if ($request->role === 'sinh_vien') {
                Student::create([
                    'user_id' => $user->id,
                    'student_code' => $request->student_code,
                    'major' => $request->major,
                    'academic_year' => $request->academic_year,
                    'class_name' => $request->class_name,
                ]);
            } elseif ($request->role === 'giang_vien') {
                Teacher::create([
                    'user_id' => $user->id,
                    'teacher_code' => $request->teacher_code,
                    'department' => $request->department,
                    'degree' => $request->degree,
                    'specialization' => $request->specialization,
                ]);
            }

            // Tạo token
            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return response()->json([
                'message' => 'Đăng ký thành công',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user->load(['student', 'teacher']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Có lỗi xảy ra khi đăng ký: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Đăng nhập
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'error' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'error' => 'Tài khoản của bạn đã bị khóa'
            ], 403);
        }

        // Xóa token cũ
        $user->tokens()->delete();

        // Tạo token mới
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Đăng nhập thành công',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load(['student', 'teacher']),
        ]);
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Đăng xuất thành công'
        ]);
    }

    /**
     * Lấy thông tin user hiện tại
     */
    public function me(Request $request)
    {
        $user = $request->user()->load(['student', 'teacher']);

        return response()->json($user);
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'error' => 'Mật khẩu hiện tại không đúng'
            ], 400);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        // Xóa tất cả token cũ
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Đổi mật khẩu thành công. Vui lòng đăng nhập lại'
        ]);
    }
}