<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Convert all course codes from format SUBJ.N01 to SUBJ.1, SUBJ.N02 to SUBJ.2, etc.
        DB::statement("
            UPDATE courses
            SET course_code = CONCAT(
                SUBSTRING_INDEX(course_code, '.N', 1),
                '.',
                CAST(SUBSTRING(course_code, POSITION('.N' IN course_code) + 2) AS UNSIGNED)
            )
            WHERE course_code LIKE '%.N%'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: convert SUBJ.1 back to SUBJ.N01, SUBJ.2 to SUBJ.N02, etc.
        // This is just for reference - not reversible without knowing original format
    }
};
