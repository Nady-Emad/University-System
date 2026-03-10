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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subject_name');
            $table->enum('exam_type', ['midterm', 'final']);
            $table->date('exam_date');
            $table->unsignedInteger('total_marks')->default(100);
            $table->unsignedTinyInteger('credit_hours')->default(3);
            $table->enum('semester', ['Fall', 'Spring', 'Summer'])->default('Fall');
            $table->string('academic_year', 20);
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
