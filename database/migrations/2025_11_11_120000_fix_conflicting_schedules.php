<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Replace schedules for specific courses to avoid conflicts with IT001.2 and IT002.1
     */
    public function up()
    {
        $map = [
            // course_code => [schedules]
            'IT003.1' => [
                ['day_of_week' => 6, 'start_time' => '13:00:00', 'end_time' => '15:30:00', 'room' => 'A121'], // Thu 6
                ['day_of_week' => 7, 'start_time' => '13:00:00', 'end_time' => '15:30:00', 'room' => 'A121'], // Thu 7
            ],
            'IT004.1' => [
                ['day_of_week' => 3, 'start_time' => '13:00:00', 'end_time' => '15:30:00', 'room' => 'A102'], // Thu 3
                ['day_of_week' => 5, 'start_time' => '13:00:00', 'end_time' => '15:30:00', 'room' => 'A102'], // Thu 5
            ],
            'IT005.1' => [
                ['day_of_week' => 6, 'start_time' => '07:00:00', 'end_time' => '09:30:00', 'room' => 'A328'], // Thu 6 morning
                ['day_of_week' => 8, 'start_time' => '09:30:00', 'end_time' => '12:00:00', 'room' => 'A328'], // CN
            ],
        ];

        foreach ($map as $code => $schedules) {
            $course = DB::table('courses')->where('course_code', $code)->first();
            if (!$course) continue;

            // remove existing schedules for this course
            DB::table('course_schedules')->where('course_id', $course->id)->delete();

            // insert new schedules
            foreach ($schedules as $s) {
                DB::table('course_schedules')->insert([
                    'course_id' => $course->id,
                    'day_of_week' => $s['day_of_week'],
                    'start_time' => $s['start_time'],
                    'end_time' => $s['end_time'],
                    'room' => $s['room'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     * We don't attempt to restore original schedules here.
     */
    public function down()
    {
        // no-op
    }
};
