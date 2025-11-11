<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('day_of_week')->comment('2=Thứ 2, 3=Thứ 3,..., 8=Chủ nhật');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_schedules');
    }
};