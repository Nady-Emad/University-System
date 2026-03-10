<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->foreignId('source_online_exam_id')
                ->nullable()
                ->unique()
                ->after('doctor_id')
                ->constrained('exams_new')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['source_online_exam_id']);
            $table->dropUnique(['source_online_exam_id']);
            $table->dropColumn('source_online_exam_id');
        });
    }
};
