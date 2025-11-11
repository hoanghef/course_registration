<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('semester_recommended')->nullable()->comment('Học kỳ khuyến nghị');
            $table->boolean('is_required')->default(true)->comment('Môn bắt buộc hay tự chọn');
            $table->timestamps();
            
            $table->unique(['program_id', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('program_subjects');
    }
};