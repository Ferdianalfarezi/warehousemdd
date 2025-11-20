<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PartController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\CheckIndicatorController;
use App\Http\Controllers\GeneralCheckupController;
use App\Http\Controllers\CheckupPartController;
use App\Http\Controllers\HistoryCheckupController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Suppliers
    Route::resource('suppliers', SupplierController::class)->except(['create', 'edit']);

    // Parts
    Route::resource('parts', PartController::class);

    // Barangs
    Route::resource('barangs', BarangController::class);

    // Schedules
    Route::resource('schedules', ScheduleController::class);
    Route::get('/barangs-for-schedule', [ScheduleController::class, 'getBarangsForSchedule'])->name('barangs.for-schedule');

    // Users (only for admin and superadmin)
    Route::resource('users', UserController::class)->middleware('can:manage-users');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('check-indicators', CheckIndicatorController::class);
    Route::get('barangs/{barang}/details', [CheckIndicatorController::class, 'getBarangDetails']);

    // General Checkups Routes
    Route::resource('general-checkups', GeneralCheckupController::class);
    Route::post('general-checkups/auto-populate', [GeneralCheckupController::class, 'autoPopulate'])->name('general-checkups.auto-populate');
    Route::post('general-checkups/{id}/start-repair', [GeneralCheckupController::class, 'startRepair'])->name('general-checkups.start-repair');
    Route::get('general-checkups/{id}/process', [GeneralCheckupController::class, 'process'])->name('general-checkups.process');
    Route::post('general-checkups/{id}/save-checkup', [GeneralCheckupController::class, 'saveCheckup'])->name('general-checkups.save-checkup');
    Route::post('general-checkups/{id}/finish-checkup', [GeneralCheckupController::class, 'finishCheckup'])->name('general-checkups.finish-checkup');

    // Checkup Part Replacement Routes
    Route::post('checkup-parts/add', [CheckupPartController::class, 'store'])->name('checkup-parts.add');
    Route::delete('checkup-parts/{id}', [CheckupPartController::class, 'destroy'])->name('checkup-parts.destroy');
    Route::get('checkup-parts/available', [CheckupPartController::class, 'getAvailableParts'])->name('checkup-parts.available');

    // History Checkups Routes
    Route::get('history-checkups', [HistoryCheckupController::class, 'index'])->name('history-checkups.index');
    Route::get('history-checkups/{id}', [HistoryCheckupController::class, 'show'])->name('history-checkups.show');
    Route::delete('history-checkups/{id}', [HistoryCheckupController::class, 'destroy'])->name('history-checkups.destroy');
    Route::get('history-checkups/stats/get', [HistoryCheckupController::class, 'getStats'])->name('history-checkups.stats');
});

require __DIR__.'/auth.php';