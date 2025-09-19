<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

// 認証不要ルート
Route::get('/', function () {
    return redirect('/login');
});

// 認証関連
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
});

// 認証必須ルート
Route::middleware(['auth', 'office.scope'])->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // ダッシュボード
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // 勤怠管理
    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::post('/clock-in', [AttendanceController::class, 'clockIn'])->name('clock-in');
        Route::post('/clock-out', [AttendanceController::class, 'clockOut'])->name('clock-out');
        Route::post('/break-start', [AttendanceController::class, 'breakStart'])->name('break-start');
        Route::post('/break-end', [AttendanceController::class, 'breakEnd'])->name('break-end');
        Route::get('/monthly', [AttendanceController::class, 'monthly'])->name('monthly');
    });

    // シフト管理
    Route::prefix('shifts')->name('shifts.')->group(function () {
        Route::get('/', [ShiftController::class, 'index'])->name('index');
        Route::get('/create', [ShiftController::class, 'create'])->name('create');
        Route::post('/', [ShiftController::class, 'store'])->name('store');
        Route::post('/auto-assign', [ShiftController::class, 'autoAssign'])->name('auto-assign');
    });

    // 日次スケジュール
    Route::prefix('schedule')->name('schedule.')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('index');
        Route::post('/update', [ScheduleController::class, 'update'])->name('update');
    });

    // メッセージ
    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::get('/create', [MessageController::class, 'create'])->name('create');
        Route::post('/', [MessageController::class, 'store'])->name('store');
        Route::get('/{message}', [MessageController::class, 'show'])->name('show');
        Route::post('/{message}/pin', [MessageController::class, 'pin'])->name('pin');
        Route::post('/{message}/read', [MessageController::class, 'markAsRead'])->name('read');
    });
});