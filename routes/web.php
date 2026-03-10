<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\DoctorPortalController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ExamQuestionController;
use App\Http\Controllers\ExamTakingController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\SubjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }

    return match (auth()->user()->role) {
        'admin' => redirect()->route('dashboard'),
        'student' => redirect()->route('student.dashboard'),
        'doctor' => redirect()->route('doctor.dashboard'),
        default => redirect()->route('login'),
    };
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('students', StudentController::class);
    Route::resource('doctors', DoctorController::class);
    Route::resource('subjects', SubjectController::class);
    Route::resource('exams', ExamController::class);
    Route::resource('results', ResultController::class);
});

Route::prefix('student')->name('student.')->middleware(['auth', 'role:student'])->group(function () {
    Route::get('/dashboard', [StudentPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [StudentPortalController::class, 'profile'])->name('profile');
    Route::get('/exams', [StudentPortalController::class, 'exams'])->name('exams');
    Route::get('/results', [StudentPortalController::class, 'results'])->name('results');
    Route::get('/gpa-simulator', [StudentPortalController::class, 'simulateGpa'])->name('gpa-simulator');

    Route::get('/online-exams', [ExamTakingController::class, 'index'])->name('online-exams.index');
    Route::get('/online-exams/{examId}', [ExamTakingController::class, 'show'])->name('online-exams.show');
    Route::post('/online-exams/{examId}/submit', [ExamTakingController::class, 'submit'])->name('online-exams.submit');
});

Route::prefix('doctor')->name('doctor.')->middleware(['auth', 'role:doctor'])->group(function () {
    Route::get('/dashboard', [DoctorPortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/exams', [DoctorPortalController::class, 'exams'])->name('exams');
    Route::post('/exams', [DoctorPortalController::class, 'storeExam'])->name('exams.store');
    Route::get('/exams/{id}', [DoctorPortalController::class, 'showExam'])->name('exams.show');
    Route::put('/exams/{id}', [DoctorPortalController::class, 'updateExam'])->name('exams.update');
    Route::delete('/exams/{id}', [DoctorPortalController::class, 'destroyExam'])->name('exams.destroy');

    Route::get('/results', [DoctorPortalController::class, 'results'])->name('results');
    Route::get('/results/create/{examId}', [DoctorPortalController::class, 'createResult'])->name('results.create');
    Route::post('/results', [DoctorPortalController::class, 'storeResult'])->name('results.store');
    Route::get('/results/{id}/edit', [DoctorPortalController::class, 'editResult'])->name('results.edit');
    Route::put('/results/{id}', [DoctorPortalController::class, 'updateResult'])->name('results.update');

    Route::get('/online-exams', [DoctorPortalController::class, 'onlineExams'])->name('online-exams.index');
    Route::post('/online-exams', [DoctorPortalController::class, 'storeOnlineExam'])->name('online-exams.store');
    Route::get('/online-exams/{id}', [DoctorPortalController::class, 'showOnlineExam'])->name('online-exams.show');
    Route::put('/online-exams/{id}', [DoctorPortalController::class, 'updateOnlineExam'])->name('online-exams.update');
    Route::delete('/online-exams/{id}', [DoctorPortalController::class, 'destroyOnlineExam'])->name('online-exams.destroy');

    Route::get('/online-exams/{examId}/questions/create', [ExamQuestionController::class, 'create'])->name('questions.create');
    Route::post('/online-exams/{examId}/questions', [ExamQuestionController::class, 'store'])->name('questions.store');
    Route::get('/online-exam-questions/{id}/edit', [ExamQuestionController::class, 'edit'])->name('questions.edit');
    Route::put('/online-exam-questions/{id}', [ExamQuestionController::class, 'update'])->name('questions.update');
    Route::delete('/online-exam-questions/{id}', [ExamQuestionController::class, 'destroy'])->name('questions.destroy');
});
