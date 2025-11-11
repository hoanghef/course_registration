<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Danh sách thanh toán của sinh viên
     */
    public function myPayments()
    {
        $user = Auth::user();
        $student = $user->student;

        $payments = Payment::with(['registration.course.subject'])
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($payments);
    }

    /**
     * Tạo thanh toán mới
     */
    public function makePayment(Request $request)
    {
        $user = Auth::user();
        $student = $user->student;

        $request->validate([
            'registration_id' => 'required|exists:registrations,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank_transfer,e_wallet,other',
            'notes' => 'nullable|string',
        ]);

        // Kiểm tra registration thuộc về sinh viên
        $registration = Registration::where('id', $request->registration_id)
            ->where('student_id', $student->id)
            ->where('status', 'approved')
            ->firstOrFail();

        // Kiểm tra đã thanh toán chưa
        if ($registration->payment()->exists()) {
            return response()->json([
                'error' => 'Đã thanh toán cho môn học này'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $payment = Payment::create([
                'student_id' => $student->id,
                'registration_id' => $request->registration_id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'payment_status' => 'completed',
                'transaction_code' => 'PAY' . time() . rand(1000, 9999),
                'payment_date' => now(),
                'notes' => $request->notes,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Thanh toán thành công',
                'payment' => $payment->load(['registration.course.subject']),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xem hóa đơn
     */
    public function invoice($id)
    {
        $user = Auth::user();
        $student = $user->student;

        $payment = Payment::with([
            'registration.course.subject',
            'registration.course.teacher.user',
            'student.user'
        ])
            ->where('id', $id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        return response()->json([
            'invoice' => [
                'transaction_code' => $payment->transaction_code,
                'payment_date' => $payment->payment_date,
                'student_name' => $payment->student->user->full_name,
                'student_code' => $payment->student->student_code,
                'subject_name' => $payment->registration->course->subject->subject_name,
                'subject_code' => $payment->registration->course->subject->subject_code,
                'credits' => $payment->registration->course->subject->credits,
                'course_code' => $payment->registration->course->course_code,
                'amount' => $payment->amount,
                'payment_method' => $payment->payment_method,
                'payment_status' => $payment->payment_status,
            ]
        ]);
    }
}