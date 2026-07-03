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
use App\Http\Controllers\RequestRepairController;
use App\Http\Controllers\HistoryRepairController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect()->route('login'));

// ══════════════════════════════════════════════════════════════════════════
// ANDON  (Public — no auth required)
// ══════════════════════════════════════════════════════════════════════════

Route::prefix('andon')->name('andon.')->group(function () {

    Route::get('inhouse',       [AndonInhouseController::class,        'index'])->name('inhouse.index');
    Route::get('inhouse/{id}',  [AndonInhouseController::class,        'show'])->name('inhouse.show');

    Route::get('outhouse',      [AndonOuthouseController::class,       'index'])->name('outhouse.index');
    Route::get('outhouse/{id}', [AndonOuthouseController::class,       'show'])->name('outhouse.show');

    Route::get('general-checkup',       [AndonGeneralCheckupController::class, 'index'])->name('general-checkup.index');
    Route::get('general-checkup/{id}',  [AndonGeneralCheckupController::class, 'show'])->name('general-checkup.show');

});

// ══════════════════════════════════════════════════════════════════════════
// AUTHENTICATED ROUTES
// ══════════════════════════════════════════════════════════════════════════

Route::middleware('auth')->group(function () {

    // ── Dashboard ──────────────────────────────────────────────────────────
    Route::prefix('dashboard')->name('dashboard')->group(function () {
        Route::get('/',               [DashboardController::class, 'index'])->name('');
        Route::get('part-condition',  [DashboardController::class, 'getPartConditionByMonth'])->name('.part-condition');
        Route::get('top-parts',       [DashboardController::class, 'getTopRequestedPartsByMonth'])->name('.top-parts');
    });

    // ── Profile ────────────────────────────────────────────────────────────
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/',    [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/',  [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // ══════════════════════════════════════════════════════════════════════
    // DATA MASTER
    // ══════════════════════════════════════════════════════════════════════

    // ── Suppliers ──────────────────────────────────────────────────────────
    Route::post('/suppliers/import',            [SupplierController::class, 'import'])->name('suppliers.import');
    Route::get('/suppliers/download/template',  [SupplierController::class, 'downloadTemplate'])->name('suppliers.download.template');
    Route::resource('suppliers', SupplierController::class)->except(['create', 'edit']);

    // ── Parts ──────────────────────────────────────────────────────────────
    Route::prefix('parts')->name('parts.')->group(function () {
        Route::get('import',            [PartController::class, 'importForm'])->name('import.form');
        Route::post('import',           [PartController::class, 'import'])->name('import');
        Route::get('download-template', [PartController::class, 'downloadTemplate'])->name('download.template');
        Route::post('bulk-request',     [PartController::class, 'bulkRequestWarehouse'])->name('bulkRequest');
        Route::get('data',              [PartController::class, 'getData'])->name('data');  // ⚠️ sebelum resource
    });
    Route::resource('parts', PartController::class);

    // ── Barangs ────────────────────────────────────────────────────────────
    Route::prefix('barangs')->name('barangs.')->group(function () {
        Route::post('import-excel', [BarangController::class, 'importExcel'])->name('import-excel');
        Route::get('data',          [BarangController::class, 'getData'])->name('data');    // ⚠️ sebelum resource
    });
    Route::resource('barangs', BarangController::class);
    Route::get('/barangs/{barang}/details', [CheckIndicatorController::class, 'getBarangDetails']);

    // ── Schedules ──────────────────────────────────────────────────────────
    Route::get('/barangs-for-schedule', [ScheduleController::class, 'getBarangsForSchedule'])->name('barangs.for-schedule');
    Route::resource('schedules', ScheduleController::class);

    // ── Check Indicators ───────────────────────────────────────────────────
    Route::resource('check-indicators', CheckIndicatorController::class);

    // ── Users ──────────────────────────────────────────────────────────────
    Route::resource('users', UserController::class)->middleware('can:manage-users');

    // ══════════════════════════════════════════════════════════════════════
    // TRANSAKSI
    // ══════════════════════════════════════════════════════════════════════

    // ── General Checkups ───────────────────────────────────────────────────
    Route::prefix('general-checkups')->name('general-checkups.')->group(function () {
        Route::post('auto-populate',          [GeneralCheckupController::class, 'autoPopulate'])->name('auto-populate');
        Route::post('{id}/start-repair',      [GeneralCheckupController::class, 'startRepair'])->name('start-repair');
        Route::get('{id}/process',            [GeneralCheckupController::class, 'process'])->name('process');
        Route::post('{id}/save-checkup',      [GeneralCheckupController::class, 'saveCheckup'])->name('save-checkup');
        Route::post('{id}/finish-checkup',    [GeneralCheckupController::class, 'finishCheckup'])->name('finish-checkup');
    });
    Route::resource('general-checkups', GeneralCheckupController::class);

    // ── Checkup Parts ──────────────────────────────────────────────────────
    Route::prefix('checkup-parts')->name('checkup-parts.')->group(function () {
        Route::post('add',                          [CheckupPartController::class, 'store'])->name('add');
        Route::get('available',                     [CheckupPartController::class, 'getAvailableParts'])->name('available');
        Route::post('{id}/close',                   [CheckupPartController::class, 'close'])->name('close');
        Route::post('close-all/{checkupDetailId}',  [CheckupPartController::class, 'closeAll'])->name('close-all');
        Route::delete('{id}',                       [CheckupPartController::class, 'destroy'])->name('destroy');
    });

    // ── Inhouse Requests ───────────────────────────────────────────────────
    Route::prefix('inhouse-requests')->name('inhouse-requests.')->group(function () {
        Route::post('store',      [InhouseRequestController::class, 'store'])->name('store');
        Route::post('{id}/close', [InhouseRequestController::class, 'close'])->name('close');
    });

    // ── Outhouse Requests ──────────────────────────────────────────────────
    Route::prefix('outhouse-requests')->name('outhouse-requests.')->group(function () {
        Route::post('store',      [OuthouseRequestController::class, 'store'])->name('store');
        Route::post('{id}/close', [OuthouseRequestController::class, 'close'])->name('close');
    });

    // ── Request Parts ──────────────────────────────────────────────────────
    Route::prefix('request-parts')->name('request-parts.')->group(function () {
        Route::get('/',                         [RequestPartController::class, 'index'])->name('index');
        Route::get('check-updates',             [RequestPartController::class, 'checkUpdates'])->name('check-updates');  // ⚠️ sebelum {requestPart}
        Route::get('{requestPart}',             [RequestPartController::class, 'show'])->name('show');
        Route::post('{requestPart}/verify',     [RequestPartController::class, 'verify'])->name('verify');
        Route::post('{requestPart}/sync-status',[RequestPartController::class, 'syncWarehouseStatus'])->name('sync-status');
        Route::post('{requestPart}/submit-to-warehouse', [RequestPartController::class, 'submitToWarehouse'])->name('submit-to-warehouse');
    });

    // ── History Request Parts ──────────────────────────────────────────────
    Route::prefix('history-request-parts')->name('history-request-parts.')->group(function () {
        Route::get('/',             [HistoryRequestPartController::class, 'index'])->name('index');
        Route::get('{historyRequestPart}', [HistoryRequestPartController::class, 'show'])->name('show');
    });

    // ── Request Repair ─────────────────────────────────────────────────────
    Route::prefix('request-repairs')->name('request-repairs.')->group(function () {
        Route::get('/',                          [RequestRepairController::class, 'index'])->name('index');
        Route::get('data',                       [RequestRepairController::class, 'getData'])->name('data');           // ⚠️ sebelum {requestRepair}
        Route::get('search-barang',              [RequestRepairController::class, 'searchBarang'])->name('search-barang');
        Route::get('process-nos',                [RequestRepairController::class, 'getProcessNos'])->name('process-nos');
        Route::post('/',                         [RequestRepairController::class, 'store'])->name('store');
        Route::get('{requestRepair}/durasi',     [RequestRepairController::class, 'getDurasi'])->name('durasi');      // ⚠️ sebelum {requestRepair} show
        Route::get('{requestRepair}',            [RequestRepairController::class, 'show'])->name('show');
        Route::put('{requestRepair}',            [RequestRepairController::class, 'update'])->name('update');
        Route::patch('{requestRepair}/status',   [RequestRepairController::class, 'updateStatus'])->name('update-status');
        Route::delete('{requestRepair}',         [RequestRepairController::class, 'destroy'])->name('destroy');
    });

    // ── History Repair ─────────────────────────────────────────────────────
    Route::prefix('history-repairs')->name('history-repairs.')->group(function () {
        Route::get('/',               [HistoryRepairController::class, 'index'])->name('index');
        Route::get('data',            [HistoryRepairController::class, 'getData'])->name('data');
        Route::get('summary',         [HistoryRepairController::class, 'getSummary'])->name('summary'); 
        Route::get('by-part-no',      [HistoryRepairController::class, 'getByPartNo'])->name('by-part-no');
        Route::get('{historyRepair}', [HistoryRepairController::class, 'show'])->name('show');
        Route::get('{historyRepair}/print', [HistoryRepairController::class, 'print'])->name('print');
    });

    // ══════════════════════════════════════════════════════════════════════
    // APPROVAL
    // ══════════════════════════════════════════════════════════════════════

    // ── PDD ────────────────────────────────────────────────────────────────
    Route::prefix('pdd')->name('pdd.')->group(function () {
        Route::get('confirm',               [PddConfirmController::class, 'index'])->name('confirm.index');
        Route::get('confirm/{id}',          [PddConfirmController::class, 'show'])->name('confirm.show');
        Route::post('confirm/{id}/approve', [PddConfirmController::class, 'approve'])->name('confirm.approve');
        Route::post('confirm/{id}/complete',[PddConfirmController::class, 'complete'])->name('confirm.complete');
    });

    // ── Subcont ────────────────────────────────────────────────────────────
    Route::prefix('subcont')->name('subcont.')->group(function () {
        Route::get('confirm',               [SubcontConfirmController::class, 'index'])->name('confirm.index');
        Route::get('confirm/{id}',          [SubcontConfirmController::class, 'show'])->name('confirm.show');
        Route::post('confirm/{id}/approve', [SubcontConfirmController::class, 'approve'])->name('confirm.approve');
        Route::post('confirm/{id}/complete',[SubcontConfirmController::class, 'complete'])->name('confirm.complete');
    });

    // ══════════════════════════════════════════════════════════════════════
    // HISTORY
    // ══════════════════════════════════════════════════════════════════════

    Route::prefix('history-checkups')->name('history-checkups.')->group(function () {
        Route::get('/',         [HistoryCheckupController::class, 'index'])->name('index');
        Route::get('stats/get', [HistoryCheckupController::class, 'getStats'])->name('stats');   // ⚠️ sebelum {id}
        Route::get('{id}',      [HistoryCheckupController::class, 'show'])->name('show');
        Route::delete('{id}',   [HistoryCheckupController::class, 'destroy'])->name('destroy');
    });

    // ══════════════════════════════════════════════════════════════════════
    // API HELPERS
    // ══════════════════════════════════════════════════════════════════════

    Route::prefix('api')->group(function () {

        Route::get('approval-counts', function () {
            return response()->json([
                'inhouse'  => \App\Models\InhouseRequest::where('status', 'pending')->count(),
                'outhouse' => \App\Models\OuthouseRequest::where('status', 'pending')->count(),
            ]);
        });

        Route::get('transaction-counts', function () {
            try {
                return response()->json([
                    'request_parts'    => \App\Models\RequestPart::whereIn('status', [
                        'pending', 'approved_kadiv', 'approved_kagud', 'ready', 'completed',
                    ])->count(),
                    'general_checkups' => \App\Models\GeneralCheckup::whereIn('status', [
                        'pending', 'on_process',
                    ])->count(),
                ]);
            } catch (\Exception $e) {
                return response()->json(['request_parts' => 0, 'general_checkups' => 0]);
            }
        });

    });

});

require __DIR__.'/auth.php';