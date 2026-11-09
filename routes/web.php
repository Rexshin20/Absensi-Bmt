<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ErrorPageController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\OfficeLocationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\WorkSettingController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/pages/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/pages/login', [AuthController::class, 'login'])->name('login.store');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [PageController::class, 'dashboard'])->name('dashboard');
    Route::get('/account-settings', [PageController::class, 'accountSettings'])->name('account-settings');
    Route::get('/pages/error', [PageController::class, 'errorPage'])->name('pages.error');
    Route::get('/401', [ErrorPageController::class, 'unauthorized'])->name('errors.401');
    Route::get('/404', [ErrorPageController::class, 'notFound'])->name('errors.404');
    Route::get('/500', [ErrorPageController::class, 'serverError'])->name('errors.500');

    Route::get('/master/karyawan', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/master/karyawan', [EmployeeController::class, 'store'])->name('employees.store');
    Route::put('/master/karyawan/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/master/karyawan/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    Route::get('/master/akun', [AccountController::class, 'index'])->name('accounts.index');
    Route::post('/master/akun', [AccountController::class, 'store'])->name('accounts.store');
    Route::put('/master/akun/{account}', [AccountController::class, 'update'])->name('accounts.update');
    Route::delete('/master/akun/{account}', [AccountController::class, 'destroy'])->name('accounts.destroy');

    Route::get('/master/divisi', [DivisionController::class, 'index'])->name('divisions.index');
    Route::post('/master/divisi', [DivisionController::class, 'store'])->name('divisions.store');
    Route::put('/master/divisi/{division}', [DivisionController::class, 'update'])->name('divisions.update');
    Route::delete('/master/divisi/{division}', [DivisionController::class, 'destroy'])->name('divisions.destroy');

    Route::get('/master/jabatan', [PositionController::class, 'index'])->name('positions.index');
    Route::post('/master/jabatan', [PositionController::class, 'store'])->name('positions.store');
    Route::put('/master/jabatan/{position}', [PositionController::class, 'update'])->name('positions.update');
    Route::delete('/master/jabatan/{position}', [PositionController::class, 'destroy'])->name('positions.destroy');

    Route::get('/master/lokasi-kantor', [OfficeLocationController::class, 'index'])->name('office-locations.index');
    Route::post('/master/lokasi-kantor', [OfficeLocationController::class, 'store'])->name('office-locations.store');
    Route::put('/master/lokasi-kantor/{officeLocation}', [OfficeLocationController::class, 'update'])->name('office-locations.update');
    Route::delete('/master/lokasi-kantor/{officeLocation}', [OfficeLocationController::class, 'destroy'])->name('office-locations.destroy');

    Route::get('/master/hari-libur', [HolidayController::class, 'index'])->name('holidays.index');
    Route::post('/master/hari-libur', [HolidayController::class, 'store'])->name('holidays.store');
    Route::put('/master/hari-libur/{holiday}', [HolidayController::class, 'update'])->name('holidays.update');
    Route::delete('/master/hari-libur/{holiday}', [HolidayController::class, 'destroy'])->name('holidays.destroy');

    Route::get('/master/jenis-izin', [LeaveTypeController::class, 'index'])->name('leave-types.index');
    Route::post('/master/jenis-izin', [LeaveTypeController::class, 'store'])->name('leave-types.store');
    Route::put('/master/jenis-izin/{leaveType}', [LeaveTypeController::class, 'update'])->name('leave-types.update');
    Route::delete('/master/jenis-izin/{leaveType}', [LeaveTypeController::class, 'destroy'])->name('leave-types.destroy');

    Route::get('/absensi', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('/absensi/absen', [AttendanceController::class, 'checkIn'])->name('attendances.check-in');

    Route::get('/absensi/izin', [LeaveRequestController::class, 'index'])->name('leave-requests.index');
    Route::get('/absensi/izin/pending-count', [LeaveRequestController::class, 'pendingCount'])->name('leave-requests.pending-count');
    Route::post('/absensi/izin', [LeaveRequestController::class, 'store'])->name('leave-requests.store');
    Route::get('/absensi/izin/{leaveRequest}', [LeaveRequestController::class, 'show'])->name('leave-requests.show');
    Route::delete('/absensi/izin/{leaveRequest}', [LeaveRequestController::class, 'destroy'])->name('leave-requests.destroy');
    Route::post('/absensi/izin/{leaveRequest}/setujui', [LeaveRequestController::class, 'approve'])->name('leave-requests.approve');
    Route::post('/absensi/izin/{leaveRequest}/tolak', [LeaveRequestController::class, 'reject'])->name('leave-requests.reject');

    Route::get('/pengaturan/hari-kerja', [WorkSettingController::class, 'edit'])->name('work-settings.edit');
    Route::put('/pengaturan/hari-kerja', [WorkSettingController::class, 'update'])->name('work-settings.update');

});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/login', function () {
    return redirect()->route('login');
})->name('login.alias');
