<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('subject_code', 20)->unique();
            $table->string('subject_name', 200);
            $table->integer('credits');
            $table->integer('theory_hours')->default(0);
            $table->integer('practice_hours')->default(0);
            $table->text('description')->nullable();
            $table->foreignId('prerequisite_subject_id')->nullable()->constrained('subjects')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};