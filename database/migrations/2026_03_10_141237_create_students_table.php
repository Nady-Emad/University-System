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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone', 30)->nullable();
            $table->year('entry_year');
            $table->string('student_code', 20)->unique();
            $table->enum('status', ['active', 'inactive', 'graduated', 'suspended'])->default('active');
            $table->decimal('current_gpa', 3, 2)->default(0);
            $table->decimal('current_cgpa', 3, 2)->default(0);
            $table->unsignedInteger('total_completed_credit_hours')->default(0);
            $table->decimal('total_quality_points', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
