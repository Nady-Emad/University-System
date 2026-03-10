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
        Schema::create('exams_new', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('exam_type', ['midterm', 'final', 'quiz', 'practical']);
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->cascadeOnDelete();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->unsignedInteger('duration_minutes');
            $table->decimal('total_marks', 8, 2)->default(0);
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $table->boolean('allow_retake')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams_new');
    }
};
