<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes - Hệ thống Đăng ký Học
|--------------------------------------------------------------------------
*/

// ===== PUBLIC ROUTES =====
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// ===== PROTECTED ROUTES =====
Route::middleware('auth:sanctum')->group(function () {
    
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/change-password', [AuthController::class, 'changePassword']);

    // ===== SINH VIÊN ROUTES =====
    Route::middleware('role:sinh_vien')->prefix('sinh-vien')->group(function () {
        // Xem môn có thể đăng ký
        Route::get('courses/available', [CourseController::class, 'availableCourses']);
        
        // Đăng ký môn học
        Route::post('registrations', [RegistrationController::class, 'register']);
        Route::get('registrations', [RegistrationController::class, 'myRegistrations']);
        Route::delete('registrations/{id}', [RegistrationController::class, 'cancel']);
        
        // Profile và lịch học
        Route::get('profile', [StudentController::class, 'profile']);
        Route::get('schedule', [StudentController::class, 'mySchedule']);
        
        // Thanh toán
        Route::get('payments', [PaymentController::class, 'myPayments']);
        Route::post('payments', [PaymentController::class, 'makePayment']);
        Route::get('payments/{id}/invoice', [PaymentController::class, 'invoice']);
    });

    // ===== PHÒNG ĐÀO TẠO ROUTES =====
    Route::middleware('role:phong_dao_tao')->prefix('dao-tao')->group(function () {
        // Duyệt đăng ký
        Route::get('registrations/pending', [RegistrationController::class, 'pendingList']);
        Route::put('registrations/{id}/approve', [RegistrationController::class, 'approve']);
        
        // Xem tất cả đăng ký
        Route::get('registrations', [RegistrationController::class, 'pendingList']);
    });

    // ===== GIẢNG VIÊN ROUTES =====
    Route::middleware('role:giang_vien')->prefix('giang-vien')->group(function () {
        // Xem lớp đang dạy
        Route::get('courses', [TeacherController::class, 'myCourses']);
        Route::get('courses/{id}', [TeacherController::class, 'courseDetail']);
        
        // Xem sinh viên trong lớp
        Route::get('courses/{id}/students', [TeacherController::class, 'courseStudents']);
        
        // Xem lịch giảng dạy
        Route::get('schedule', [TeacherController::class, 'mySchedule']);
        
        // Duyệt đăng ký (nếu được phân quyền)
        Route::put('registrations/{id}/approve', [RegistrationController::class, 'approve']);
    });

    // ===== SHARED ROUTES (Tất cả roles) =====
    Route::get('courses', [CourseController::class, 'index']);
    Route::get('courses/{id}', [CourseController::class, 'show']);
});

    // ===== ADMIN ROUTES =====
    // Bảo vệ bằng auth:sanctum trước, sau đó kiểm tra role
    Route::middleware(['auth:sanctum','role:admin'])->prefix('admin')->group(function () {
        // Quản lý tài khoản và phân quyền
        Route::get('users', [AdminController::class, 'index']);
        Route::post('users', [AdminController::class, 'store']);
        Route::get('users/{id}', [AdminController::class, 'show']);
        Route::put('users/{id}', [AdminController::class, 'update']);
        Route::delete('users/{id}', [AdminController::class, 'destroy']);
        Route::put('users/{id}/toggle-active', [AdminController::class, 'toggleActive']);
    });

// PHONG DAO TAO ROUTES (bảo vệ bằng auth:sanctum + role)
Route::middleware(['auth:sanctum','role:phong_dao_tao'])->prefix('dao-tao')->group(function () {
    // Quản lý môn học
    Route::get('subjects', [SubjectController::class, 'index']);
    Route::post('subjects', [SubjectController::class, 'store']);
    Route::get('subjects/{id}', [SubjectController::class, 'show']);
    Route::put('subjects/{id}', [SubjectController::class, 'update']);
    Route::delete('subjects/{id}', [SubjectController::class, 'destroy']);
    Route::post('subjects/{id}/add-courses', [SubjectController::class, 'addCourses']);
    
    // Xem danh sách đăng ký (chỉ xem, không duyệt)
    Route::get('registrations', [RegistrationController::class, 'allRegistrations']);
});