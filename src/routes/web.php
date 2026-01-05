<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceCorrectionController;
use App\Http\Controllers\Admin\AdminAttendanceCorrectionController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\AttendanceStaffController as AdminAttendanceStaffController;


// 一般ユーザー画面

Route::middleware('auth')->group(function () {

    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::post('/email/manual-verify', [VerificationController::class, 'manualVerify'])->name('manual.verify');

    Route::post('/email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
});

Route::middleware('auth')->get('/attendance', function () {
    return redirect()->route('attendance.index');
});

Route::middleware(['auth', 'verified.custom'])->group(function () {

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/start', [AttendanceController::class, 'start'])->name('attendance.start');
    Route::post('/attendance/end', [AttendanceController::class, 'end'])->name('attendance.end');
    Route::post('/attendance/break-in', [AttendanceController::class, 'breakIn'])->name('attendance.break_in');
    Route::post('/attendance/break-out', [AttendanceController::class, 'breakOut'])->name('attendance.break_out');

    Route::get('/attendance/list', [AttendanceController::class, 'list'])->name('attendance.list');
    Route::get('/attendance/detail/{id}', [AttendanceController::class, 'detail'])->name('attendance.detail')->where('id', '\d{4}-\d{2}-\d{2}');

    Route::get('/stamp_correction_request/list', [AttendanceCorrectionController::class, 'index'])->name('attendance_correction.list');

    Route::post('/stamp_correction_request', [AttendanceCorrectionController::class, 'store'])->name('attendance_correction.store');
});




// 管理者画面

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', function () {
        return view('admin.auth.login');
    })->middleware('guest:admin')->name('login');

    Route::post('/login', function () {
    $credentials = request()->validate([
        'email' => 'required|email',
        'password' => 'required',
    ], [
        'email.required' => 'メールアドレスを入力してください',
        'email.email' => '有効なメールアドレス形式で入力してください',
        'password.required' => 'パスワードを入力してください',
    ]);

    if (Auth::guard('admin')->attempt($credentials)) {
        return redirect()->intended(route('admin.attendance.list'));
    }

    return back()->withErrors([
        'email' => 'ログイン情報が登録されていません',
    ])->withInput(request()->only('email'));
})->name('login.submit');


    Route::middleware('auth:admin')->group(function () {
        Route::get('/attendance/list', [AdminAttendanceController::class, 'index'])->name('attendance.list');

        Route::get('/attendance/staff/{user}', [AdminAttendanceStaffController::class, 'show'])->name('attendance.staff');

        Route::get('/attendance/staff/{user}/export', [AdminAttendanceStaffController::class, 'exportCsv'])->name('attendance.staff.export');

        Route::get('/attendance/{user}/{date}', [AdminAttendanceController::class, 'detail'])->name('attendance.detail');

        Route::post('/attendance/update', [AdminAttendanceController::class, 'update'])->name('attendance.update');

        Route::get('/staff/list', [AdminAttendanceStaffController::class, 'index'])->name('staff.list');

        Route::get('/stamp_correction_request/list', [AdminAttendanceCorrectionController::class, 'index'])
            ->name('attendance_correction.list');

        Route::post('/stamp_correction_request/approve/{attendance_correct_request_id}', [AdminAttendanceCorrectionController::class, 'approve'])->name('attendance_correction.approve');
    });

    Route::post('/logout', function () {
        Auth::guard('admin')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('admin.login');
    })->name('logout');
});