<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('course_code', 20)->unique();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->string('semester', 20);
            $table->string('academic_year', 20);
            $table->integer('max_students')->default(50);
            $table->integer('current_students')->default(0);
            $table->text('schedule')->nullable();
            $table->string('room', 50)->nullable();
            $table->enum('status', ['pending', 'open', 'closed', 'completed'])->default('pending');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
            
            $table->index(['semester', 'academic_year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};