<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerizinanController;
use App\Http\Controllers\DokumenController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PersyaratanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    // Home route
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/filter', [DashboardController::class, 'filter'])->name('dashboard.filter');
    Route::get('/dashboard/export-excel', [DashboardController::class, 'exportExcel'])->name('dashboard.export-excel');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Perizinan routes
    Route::resource('perizinan', PerizinanController::class);
    Route::get('perizinan/{perizinan}/download/{dokumen}', [PerizinanController::class, 'download'])->name('perizinan.download');
    Route::post('perizinan/{perizinan}/approve', [PerizinanController::class, 'approve'])->name('perizinan.approve');
    Route::post('perizinan/{perizinan}/reject', [PerizinanController::class, 'reject'])->name('perizinan.reject');

    // Dokumen routes
    Route::post('perizinan/{perizinan}/dokumen', [DokumenController::class, 'store'])->name('dokumen.store');
    Route::delete('dokumen/{dokumen}', [DokumenController::class, 'destroy'])->name('dokumen.destroy');
    Route::get('dokumen/{dokumen}/download', [DokumenController::class, 'download'])->name('dokumen.download');

    // Comment routes for all users
    Route::get('perizinan/{perizinan}/comments', [CommentController::class, 'getComments'])->name('comments.get');

    // Activity Log routes
    Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    Route::get('perizinan/{perizinan}/logs', [ActivityLogController::class, 'perizinanLogs'])->name('activity-logs.perizinan');

    // Report routes
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export', [ReportController::class, 'export'])->name('reports.export');
    Route::get('reports/daily', [ReportController::class, 'daily'])->name('reports.daily');
    Route::get('reports/monthly', [ReportController::class, 'monthly'])->name('reports.monthly');
    Route::get('reports/yearly', [ReportController::class, 'yearly'])->name('reports.yearly');

    // Laporan routes
    Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('laporan/perizinan', [LaporanController::class, 'perizinan'])->name('laporan.perizinan');
    Route::get('laporan/export', [LaporanController::class, 'export'])->name('laporan.export');

    // Admin only routes
    Route::middleware(['role:admin'])->group(function () {
        // Settings routes
        Route::resource('settings', SettingController::class);
        Route::get('settings/system', [SettingController::class, 'system'])->name('settings.system');
        Route::post('settings/system', [SettingController::class, 'updateSystem'])->name('settings.system.update');
        Route::get('settings/account', [SettingController::class, 'account'])->name('settings.account');
        Route::post('settings/account', [SettingController::class, 'updateAccount'])->name('settings.account.update');

        // Persyaratan routes
        Route::resource('persyaratan', PersyaratanController::class);
        Route::post('persyaratan/upload/{perizinan}/{persyaratan}', [PersyaratanController::class, 'uploadFile'])->name('persyaratan.upload');

        // Notification routes
        Route::resource('notifications', NotificationController::class);
        Route::post('notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
        Route::post('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
        Route::get('notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');

        // Comment routes for admin
        Route::post('perizinan/{perizinan}/comments', [CommentController::class, 'store'])->name('comments.store');
        Route::put('comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
        Route::delete('comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    });
});
