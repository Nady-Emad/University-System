<?php

namespace Tests\Feature;

use App\Models\OnlineExam;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DoctorOnlineExamDetailsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_doctor_online_exam_details_include_all_enrolled_students_with_or_without_attempts(): void
    {
        $doctorUser = User::query()->where('email', 'm.ali@university.com')->firstOrFail();

        $exam = OnlineExam::query()
            ->where('doctor_id', $doctorUser->doctor->id)
            ->whereHas('subject', fn ($query) => $query->where('code', 'CS101'))
            ->firstOrFail();

        $expectedEnrolledCount = $exam->subject
            ->students()
            ->wherePivot('enrollment_status', 'enrolled')
            ->count();

        $response = $this->actingAs($doctorUser)
            ->get(route('doctor.online-exams.show', $exam->id));

        $response->assertOk();

        $rows = $response->viewData('studentAttemptRows');
        $summary = $response->viewData('attemptSummary');

        $this->assertCount($expectedEnrolledCount, $rows);
        $this->assertSame($expectedEnrolledCount, $summary['submitted'] + $summary['in_progress'] + $summary['not_started']);
        $this->assertGreaterThan(0, $rows->where('status_key', 'not_started')->count());
    }
}
