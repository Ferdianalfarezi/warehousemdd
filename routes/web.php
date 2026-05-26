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
use App\Http\Controllers\InhouseRequestController;
use App\Http\Controllers\OuthouseRequestController;
use App\Http\Controllers\PddConfirmController;
use App\Http\Controllers\SubcontConfirmController;
use App\Http\Controllers\AndonInhouseController;
use App\Http\Controllers\AndonOuthouseController;
use App\Http\Controllers\AndonGeneralCheckupController;
use App\Http\Controllers\RequestPartController;
use App\Http\Controllers\HistoryRequestPartController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect()->route('login'));

// ==================== ANDON (Public / No Auth) ====================

Route::get('andon/inhouse', [AndonInhouseController::class, 'index'])->name('andon.inhouse.index');
Route::get('andon/inhouse/{id}', [AndonInhouseController::class, 'show'])->name('andon.inhouse.show');

Route::get('andon/outhouse', [AndonOuthouseController::class, 'index'])->name('andon.outhouse.index');
Route::get('andon/outhouse/{id}', [AndonOuthouseController::class, 'show'])->name('andon.outhouse.show');

Route::get('andon/general-checkup', [AndonGeneralCheckupController::class, 'index'])->name('andon.general-checkup.index');
Route::get('andon/general-checkup/{id}', [AndonGeneralCheckupController::class, 'show'])->name('andon.general-checkup.show');

// ==================== AUTHENTICATED ROUTES ====================

Route::middleware('auth')->group(function () {

    // ==================== DASHBOARD ====================

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/part-condition', [DashboardController::class, 'getPartConditionByMonth'])->name('dashboard.part-condition');
    Route::get('/dashboard/top-parts', [DashboardController::class, 'getTopRequestedPartsByMonth'])->name('dashboard.top-parts');

    // ==================== PROFILE ====================

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ==================== DATA MASTER ====================

    // Suppliers
    Route::post('/suppliers/import', [SupplierController::class, 'import'])->name('suppliers.import');
    Route::get('/suppliers/download/template', [SupplierController::class, 'downloadTemplate'])->name('suppliers.download.template');
    Route::resource('suppliers', SupplierController::class)->except(['create', 'edit']);

    // Parts
    Route::get('/parts/import', [PartController::class, 'importForm'])->name('parts.import.form');
    Route::post('/parts/import', [PartController::class, 'import'])->name('parts.import');
    Route::get('/parts/download-template', [PartController::class, 'downloadTemplate'])->name('parts.download.template');
    Route::post('/parts/bulk-request', [PartController::class, 'bulkRequestWarehouse'])->name('parts.bulkRequest');
    Route::get('/parts/data', [PartController::class, 'getData'])->name('parts.data');
    Route::resource('parts', PartController::class);

    // Barangs
    Route::post('/barangs/import-excel', [BarangController::class, 'importExcel'])->name('barangs.import-excel');
    Route::get('/barangs/data', [BarangController::class, 'getData'])->name('barangs.data');  // ⚠️ Sebelum resource
    Route::resource('barangs', BarangController::class);
    Route::get('/barangs/{barang}/details', [CheckIndicatorController::class, 'getBarangDetails']);

    // Schedules
    Route::get('/barangs-for-schedule', [ScheduleController::class, 'getBarangsForSchedule'])->name('barangs.for-schedule');
    Route::resource('schedules', ScheduleController::class);

    // Check Indicators
    Route::resource('check-indicators', CheckIndicatorController::class);

    // Users
    Route::resource('users', UserController::class)->middleware('can:manage-users');

    // ==================== REQUEST PARTS ====================

    Route::prefix('request-parts')->name('request-parts.')->group(function () {
        Route::get('/', [RequestPartController::class, 'index'])->name('index');
        Route::get('/check-updates', [RequestPartController::class, 'checkUpdates'])->name('check-updates');  // ⚠️ Harus sebelum {requestPart}
        Route::get('/{requestPart}', [RequestPartController::class, 'show'])->name('show');
        Route::post('/{requestPart}/verify', [RequestPartController::class, 'verify'])->name('verify');
        Route::post('/{requestPart}/sync-status', [RequestPartController::class, 'syncWarehouseStatus'])->name('sync-status');
        Route::post('/{requestPart}/submit-to-warehouse', [RequestPartController::class, 'submitToWarehouse'])->name('submit-to-warehouse');
    });

    // History Request Parts
    Route::prefix('history-request-parts')->name('history-request-parts.')->group(function () {
        Route::get('/', [HistoryRequestPartController::class, 'index'])->name('index');
        Route::get('/{historyRequestPart}', [HistoryRequestPartController::class, 'show'])->name('show');
    });

    // ==================== DATA TRANSAKSI ====================

    // General Checkups
    Route::post('general-checkups/auto-populate', [GeneralCheckupController::class, 'autoPopulate'])->name('general-checkups.auto-populate');
    Route::post('general-checkups/{id}/start-repair', [GeneralCheckupController::class, 'startRepair'])->name('general-checkups.start-repair');
    Route::get('general-checkups/{id}/process', [GeneralCheckupController::class, 'process'])->name('general-checkups.process');
    Route::post('general-checkups/{id}/save-checkup', [GeneralCheckupController::class, 'saveCheckup'])->name('general-checkups.save-checkup');
    Route::post('general-checkups/{id}/finish-checkup', [GeneralCheckupController::class, 'finishCheckup'])->name('general-checkups.finish-checkup');
    Route::resource('general-checkups', GeneralCheckupController::class);

    // Checkup Parts
    Route::post('checkup-parts/add', [CheckupPartController::class, 'store'])->name('checkup-parts.add');
    Route::get('checkup-parts/available', [CheckupPartController::class, 'getAvailableParts'])->name('checkup-parts.available');
    Route::post('checkup-parts/{id}/close', [CheckupPartController::class, 'close'])->name('checkup-parts.close');
    Route::post('/checkup-parts/close-all/{checkupDetailId}', [CheckupPartController::class, 'closeAll'])->name('checkup-parts.close-all');
    Route::delete('checkup-parts/{id}', [CheckupPartController::class, 'destroy'])->name('checkup-parts.destroy');

    // Inhouse & Outhouse Requests
    Route::post('inhouse-requests/store', [InhouseRequestController::class, 'store'])->name('inhouse-requests.store');
    Route::post('inhouse-requests/{id}/close', [InhouseRequestController::class, 'close'])->name('inhouse-requests.close');

    Route::post('outhouse-requests/store', [OuthouseRequestController::class, 'store'])->name('outhouse-requests.store');
    Route::post('outhouse-requests/{id}/close', [OuthouseRequestController::class, 'close'])->name('outhouse-requests.close');

    // ==================== APPROVAL ====================

    Route::prefix('pdd')->name('pdd.')->group(function () {
        Route::get('confirm', [PddConfirmController::class, 'index'])->name('confirm.index');
        Route::get('confirm/{id}', [PddConfirmController::class, 'show'])->name('confirm.show');
        Route::post('confirm/{id}/approve', [PddConfirmController::class, 'approve'])->name('confirm.approve');
        Route::post('confirm/{id}/complete', [PddConfirmController::class, 'complete'])->name('confirm.complete');
    });

    Route::prefix('subcont')->name('subcont.')->group(function () {
        Route::get('confirm', [SubcontConfirmController::class, 'index'])->name('confirm.index');
        Route::get('confirm/{id}', [SubcontConfirmController::class, 'show'])->name('confirm.show');
        Route::post('confirm/{id}/approve', [SubcontConfirmController::class, 'approve'])->name('confirm.approve');
        Route::post('confirm/{id}/complete', [SubcontConfirmController::class, 'complete'])->name('confirm.complete');
    });

    // ==================== HISTORY ====================

    Route::get('history-checkups', [HistoryCheckupController::class, 'index'])->name('history-checkups.index');
    Route::get('history-checkups/stats/get', [HistoryCheckupController::class, 'getStats'])->name('history-checkups.stats');  // ⚠️ Sebelum {id}
    Route::get('history-checkups/{id}', [HistoryCheckupController::class, 'show'])->name('history-checkups.show');
    Route::delete('history-checkups/{id}', [HistoryCheckupController::class, 'destroy'])->name('history-checkups.destroy');

    // ==================== API HELPERS ====================

    Route::get('/api/approval-counts', function () {
        return response()->json([
            'inhouse'  => \App\Models\InhouseRequest::where('status', 'pending')->count(),
            'outhouse' => \App\Models\OuthouseRequest::where('status', 'pending')->count(),
        ]);
    });

    Route::get('/api/transaction-counts', function () {
        try {
            $requestPartCount = \App\Models\RequestPart::whereIn('status', [
                'pending', 'approved_kadiv', 'approved_kagud', 'ready', 'completed',
            ])->count();

            $checkupCount = \App\Models\GeneralCheckup::whereIn('status', [
                'pending', 'on_process',
            ])->count();

            return response()->json([
                'request_parts'   => $requestPartCount,
                'general_checkups' => $checkupCount,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'request_parts'   => 0,
                'general_checkups' => 0,
            ]);
        }
    });
});

require __DIR__.'/auth.php';