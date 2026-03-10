<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Exam;
use App\Models\Result;
use App\Models\Student;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalStudents = Student::count();
        $totalDoctors = Doctor::count();
        $totalExams = Exam::count();
        $totalResults = Result::count();

        $topStudents = Student::query()
            ->select('id', 'full_name', 'student_code', 'current_gpa', 'current_cgpa')
            ->orderByDesc('current_cgpa')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalStudents',
            'totalDoctors',
            'totalExams',
            'totalResults',
            'topStudents'
        ));
    }
}
