<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * List users with optional filtering
     */
    public function index(Request $request)
    {
        $query = User::with(['student', 'teacher']);

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('q')) {
            $q = $request->q;
            $query->where(function ($qwhere) use ($q) {
                $qwhere->where('username', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('full_name', 'like', "%{$q}%");
            });
        }

        $perPage = (int) $request->get('per_page', 15);

        return response()->json($query->paginate($perPage));
    }

    /**
     * Show single user
     */
    public function show($id)
    {
        $user = User::with(['student', 'teacher'])->find($id);
        if (!$user) {
            return response()->json(['error' => 'Người dùng không tồn tại'], 404);
        }
        return response()->json($user);
    }

    /**
     * Create a new user (admin-only)
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:50|unique:users',
            'email' => 'required|email|max:100|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'full_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'role' => 'required|in:admin,sinh_vien,giang_vien,phong_dao_tao',

            // Sinh viên
            'student_code' => 'required_if:role,sinh_vien|unique:students',
            'major' => 'required_if:role,sinh_vien',
            'academic_year' => 'required_if:role,sinh_vien',

            // Giảng viên
            'teacher_code' => 'required_if:role,giang_vien|unique:teachers',
            'department' => 'required_if:role,giang_vien',
        ]);

        DB::beginTransaction();
        try {
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

            if ($request->role === 'sinh_vien') {
                Student::create([
                    'user_id' => $user->id,
                    'student_code' => $request->student_code,
                    'major' => $request->major,
                    'academic_year' => $request->academic_year,
                    'class_name' => $request->class_name ?? null,
                ]);
            } elseif ($request->role === 'giang_vien') {
                Teacher::create([
                    'user_id' => $user->id,
                    'teacher_code' => $request->teacher_code,
                    'department' => $request->department,
                    'degree' => $request->degree ?? null,
                    'specialization' => $request->specialization ?? null,
                ]);
            }

            DB::commit();

            return response()->json($user->load(['student', 'teacher']), 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Lỗi khi tạo người dùng: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update user and role/profile
     */
    public function update(Request $request, $id)
    {
        $user = User::with(['student', 'teacher'])->find($id);
        if (!$user) {
            return response()->json(['error' => 'Người dùng không tồn tại'], 404);
        }

        $rules = [
            'username' => 'sometimes|string|max:50|unique:users,username,' . $user->id,
            'email' => 'sometimes|email|max:100|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|confirmed',
            'full_name' => 'sometimes|string|max:100',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'role' => 'sometimes|in:admin,sinh_vien,giang_vien,phong_dao_tao',
        ];

        $request->validate($rules);

        DB::beginTransaction();
        try {
            $data = $request->only(['username', 'email', 'full_name', 'phone', 'address']);
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            // If role is changing, handle profile creation/deletion later
            $oldRole = $user->role;
            if ($request->has('role')) {
                $data['role'] = $request->role;
            }

            $user->update($data);

            // Handle role-specific profiles
            $newRole = $user->role;
            // If switched to student
            if ($newRole === 'sinh_vien') {
                $request->validate([
                    'student_code' => 'required|unique:students,student_code,' . ($user->student->id ?? 'NULL') . ',id,user_id,' . $user->id,
                    'major' => 'required',
                    'academic_year' => 'required',
                ]);

                if ($user->student) {
                    $user->student->update([
                        'student_code' => $request->student_code,
                        'major' => $request->major,
                        'academic_year' => $request->academic_year,
                        'class_name' => $request->class_name ?? $user->student->class_name,
                    ]);
                } else {
                    Student::create([
                        'user_id' => $user->id,
                        'student_code' => $request->student_code,
                        'major' => $request->major,
                        'academic_year' => $request->academic_year,
                        'class_name' => $request->class_name ?? null,
                    ]);
                }
                // remove teacher if existed
                if ($user->teacher) {
                    $user->teacher->delete();
                }
            } elseif ($newRole === 'giang_vien') {
                $request->validate([
                    'teacher_code' => 'required|unique:teachers,teacher_code,' . ($user->teacher->id ?? 'NULL') . ',id,user_id,' . $user->id,
                    'department' => 'required',
                ]);

                if ($user->teacher) {
                    $user->teacher->update([
                        'teacher_code' => $request->teacher_code,
                        'department' => $request->department,
                        'degree' => $request->degree ?? $user->teacher->degree,
                        'specialization' => $request->specialization ?? $user->teacher->specialization,
                    ]);
                } else {
                    Teacher::create([
                        'user_id' => $user->id,
                        'teacher_code' => $request->teacher_code,
                        'department' => $request->department,
                        'degree' => $request->degree ?? null,
                        'specialization' => $request->specialization ?? null,
                    ]);
                }
                // remove student if existed
                if ($user->student) {
                    $user->student->delete();
                }
            } else {
                // role is admin or phong_dao_tao - remove any student/teacher profiles
                if ($user->student) {
                    $user->student->delete();
                }
                if ($user->teacher) {
                    $user->teacher->delete();
                }
            }

            DB::commit();

            return response()->json($user->load(['student', 'teacher']));

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Lỗi khi cập nhật người dùng: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete user and related profiles
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'Người dùng không tồn tại'], 404);
        }

        DB::beginTransaction();
        try {
            // delete profiles
            if ($user->student) {
                $user->student->delete();
            }
            if ($user->teacher) {
                $user->teacher->delete();
            }

            // delete tokens
            $user->tokens()->delete();

            $user->delete();

            DB::commit();

            return response()->json(['message' => 'Người dùng đã được xóa']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Lỗi khi xóa người dùng: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Toggle active/inactive status
     */
    public function toggleActive($id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'Người dùng không tồn tại'], 404);
        }

        $user->is_active = !$user->is_active;
        $user->save();

        return response()->json(['message' => 'Trạng thái tài khoản đã được cập nhật', 'is_active' => $user->is_active]);
    }
}
