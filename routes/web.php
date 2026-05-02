<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SmtpSettingController;
use App\Http\Controllers\SprintController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,5');
    Route::get('/captcha/refresh', [LoginController::class, 'refreshCaptcha'])->name('captcha.refresh');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Notifications API
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::post('/notifications/mark-all-unread', [NotificationController::class, 'markAllUnread'])->name('notifications.markAllUnread');

    Route::resource('projects', ProjectController::class);
    Route::get('/projects/{project}/gantt', [ProjectController::class, 'gantt'])->name('projects.gantt');
    Route::get('/projects/{project}/scurve', [ProjectController::class, 'scurve'])->name('projects.scurve');
    Route::get('/projects/{project}/kanban', [ProjectController::class, 'kanban'])->name('projects.kanban');

    Route::post('/projects/{project}/sprints', [SprintController::class, 'store'])->name('projects.sprints.store');
    Route::put('/projects/{project}/sprints/{sprint}', [SprintController::class, 'update'])->name('projects.sprints.update');
    Route::delete('/projects/{project}/sprints/{sprint}', [SprintController::class, 'destroy'])->name('projects.sprints.destroy');

    Route::get('/projects/{project}/tasks/create', [TaskController::class, 'create'])->name('projects.tasks.create');
    Route::post('/projects/{project}/tasks', [TaskController::class, 'store'])->name('projects.tasks.store');
    Route::get('/projects/{project}/tasks/{task}', [TaskController::class, 'show'])->name('projects.tasks.show');
    Route::put('/projects/{project}/tasks/{task}', [TaskController::class, 'update'])->name('projects.tasks.update');
    Route::delete('/projects/{project}/tasks/{task}', [TaskController::class, 'destroy'])->name('projects.tasks.destroy');
    Route::patch('/projects/{project}/tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('projects.tasks.status');
    Route::post('/projects/{project}/tasks/{task}/comments', [TaskController::class, 'storeComment'])->name('projects.tasks.comments.store');
    Route::get('/projects/{project}/backlog', [TaskController::class, 'backlog'])->name('projects.backlog');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/projects/{project}/pdf', [ReportController::class, 'projectPdf'])->name('reports.project.pdf');
    Route::get('/reports/projects/{project}/excel', [ReportController::class, 'projectExcel'])->name('reports.project.excel');
    Route::get('/reports/projects/{project}/tasks/pdf', [ReportController::class, 'tasksPdf'])->name('reports.tasks.pdf');
    Route::get('/reports/projects/{project}/tasks/excel', [ReportController::class, 'tasksExcel'])->name('reports.tasks.excel');

    Route::middleware('can:manage settings')->group(function () {
        Route::resource('users', UserController::class)->except('show');
        Route::get('/settings/smtp', [SmtpSettingController::class, 'edit'])->name('settings.smtp');
        Route::put('/settings/smtp', [SmtpSettingController::class, 'update'])->name('settings.smtp.update');
        Route::post('/settings/smtp/test', [SmtpSettingController::class, 'test'])->name('settings.smtp.test');
    });
});
