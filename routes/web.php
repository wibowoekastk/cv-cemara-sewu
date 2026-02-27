<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TargetController; 
use App\Http\Controllers\ObatController;
use App\Http\Controllers\KandangController; 
use App\Http\Controllers\PakanController; 
use App\Http\Controllers\DashboardMandorController;
use App\Http\Controllers\AnalyticController;
use App\Http\Controllers\SiklusController;
use App\Http\Controllers\BatchController; 

use App\Models\DailyRecord;
use App\Models\Kandang;
use App\Models\Unit;
use App\Models\Pakan;
use Carbon\Carbon;

Route::get('/', function () {
    return view('welcome');
});

// --- AUTHENTICATION ---
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// --- GROUP AUTH (Harus Login) ---
Route::middleware(['auth'])->group(function () {
    
    // Fitur User Umum
    Route::post('/user/profile/update', [UserController::class, 'updateProfile'])->name('user.profile.update');
    Route::post('/user/password/update', [UserController::class, 'updatePassword'])->name('user.password.update');

    // ====================================================
    // ROLE: OWNER
    // ====================================================
    Route::prefix('owner')->group(function () {
        // DASHBOARD UTAMA OWNER (GRAFIK PRODUKSI)
        Route::get('/dashboard', function () { 
            $today = \Carbon\Carbon::today();
            $produksiHariIni = \App\Models\DailyRecord::whereDate('tanggal', $today)->sum('telur_kg');
            $totalPopulasi = \App\Models\Kandang::sum('stok_saat_ini');
            $avgFcr = \App\Models\DailyRecord::whereDate('tanggal', $today)->where('fcr', '>', 0)->avg('fcr') ?? 0;
            $totalStokPakan = \App\Models\UnitPakanStock::sum('jumlah_stok');
            $lokasis = \App\Models\Unit::select('lokasi')->distinct()->pluck('lokasi');
            $units = \App\Models\Unit::all();
            $firstKandang = \App\Models\Kandang::first();

            return view('owner.dashboard', compact('produksiHariIni', 'totalPopulasi', 'avgFcr', 'totalStokPakan', 'lokasis', 'units', 'firstKandang')); 
        })->name('owner.dashboard');
        
        // --- MANAJEMEN USER (OWNER) ---
        Route::get('/user/input', function () { return view('owner.user.input'); })->name('owner.user.input');
        Route::post('/user/store', [UserController::class, 'store'])->name('owner.user.store');
        
        // Halaman Data User (Tabel List)
        Route::get('/user/data', [UserController::class, 'data'])->name('owner.user.data');
        
        // [FIXED] Halaman Dashboard User (Statistik Admin/Mandor)
        Route::get('/user/dashboarduser', [UserController::class, 'dashboardUser'])->name('owner.user.dashboarduser'); 

        Route::put('/user/update/{id}', [UserController::class, 'update'])->name('owner.user.update');
        Route::delete('/user/delete/{id}', [UserController::class, 'destroy'])->name('owner.user.delete');

        // Menu Analytic & Laporan
        Route::get('/analytic/data', [AnalyticController::class, 'ownerData'])->name('owner.analytic.data');
        Route::get('/analytic/laporan-pdf', [AnalyticController::class, 'ownerLaporan'])->name('owner.analytic.laporan-pdf');
        
        // Menu Kandang
        Route::get('/kandang/data', [KandangController::class, 'ownerData'])->name('owner.kandang.data');
        Route::get('/kandang/laporan-pdf', [KandangController::class, 'ownerLaporan'])->name('owner.kandang.laporan-pdf');

        // Menu Pakan
        Route::get('/pakan/data-input', [PakanController::class, 'ownerData'])->name('owner.pakan.data_input');
        Route::get('/pakan/laporan', [PakanController::class, 'ownerLaporan'])->name('owner.pakan.laporan');

        // Menu Obat
        Route::get('/obat/data-input', [ObatController::class, 'ownerData'])->name('owner.obat.data_input');
        Route::get('/obat/laporan', [ObatController::class, 'ownerLaporan'])->name('owner.obat.laporan');

        Route::get('/owner/settings', function () { return view('owner.settingsowner'); })->name('owner.settingsowner');
    });

    // ====================================================
    // ROLE: ADMIN
    // ====================================================
    Route::prefix('admin')->group(function () {
        
        Route::get('/dashboard', function () { 
            $today = Carbon::today();
            $produksiHariIni = DailyRecord::whereDate('tanggal', $today)->sum('telur_kg');
            $totalPopulasi = Kandang::sum('stok_saat_ini');
            $unitAktif = Unit::whereHas('kandangs', function($q) { $q->where('status', 'aktif'); })->count();
            $totalUnit = Unit::count();
            $matiHariIni = DailyRecord::whereDate('tanggal', $today)->sum('mati');
            $afkirHariIni = DailyRecord::whereDate('tanggal', $today)->sum('afkir');
            $totalDeplesi = $matiHariIni + $afkirHariIni;
            $persenDeplesi = $totalPopulasi > 0 ? ($totalDeplesi / ($totalPopulasi + $totalDeplesi)) * 100 : 0;
            $pakans = Pakan::orderBy('stok_pusat', 'asc')->take(3)->get();
            $lokasis = Unit::select('lokasi')->distinct()->pluck('lokasi');
            $units = Unit::all();

            return view('admin.dashboard', compact('produksiHariIni', 'totalPopulasi', 'unitAktif', 'totalUnit', 'totalDeplesi', 'persenDeplesi', 'pakans', 'lokasis', 'units')); 
        })->name('admin.dashboard');

        // Manajemen Batch
        Route::get('/batch', [BatchController::class, 'index'])->name('admin.batch.index');
        Route::post('/batch/store', [BatchController::class, 'store'])->name('admin.batch.store');
        Route::delete('/batch/{id}', [BatchController::class, 'destroy'])->name('admin.batch.delete');

        // Manajemen Siklus (Chick-In & Afkir)
        Route::post('/kandang/{id}/chick-in', [SiklusController::class, 'store'])->name('siklus.store');
        Route::post('/kandang/{id}/afkir', [SiklusController::class, 'afkir'])->name('siklus.afkir');

        // Manajemen Target
        Route::get('/target/input', [TargetController::class, 'create'])->name('admin.target.input'); 
        Route::get('/target/riwayat', [TargetController::class, 'riwayat'])->name('admin.targetmandor.riwayattarget');
        Route::post('/target/store', [TargetController::class, 'store'])->name('admin.target.store');
        Route::put('/target/update/{id}', [TargetController::class, 'update'])->name('admin.target.update');
        Route::delete('/target/delete/{id}', [TargetController::class, 'destroy'])->name('admin.target.delete');
        Route::get('/targetmandor/input', [TargetController::class, 'create'])->name('admin.targetmandor.inputtarget');

        // Manajemen User Admin
        Route::get('/user/dashboard', function () { return view('admin.user.dashboard'); })->name('admin.user.dashboard');
        Route::get('/user/input', function () { return view('admin.user.input'); })->name('admin.user.input');
        Route::post('/user/store', [UserController::class, 'store'])->name('admin.user.store');
        Route::get('/user/data', [UserController::class, 'data'])->name('admin.user.data');
        Route::get('/user/settings', function () { return view('admin.user.settings'); })->name('admin.user.settings');
        Route::get('/admin/settings', function () { return view('admin.settings'); })->name('admin.settings');

        // Manajemen Obat
        Route::prefix('obat')->name('admin.obat.')->group(function () {
            Route::get('/dashboard', [ObatController::class, 'dashboard'])->name('dashboard');
            Route::get('/input', [ObatController::class, 'input'])->name('input');
            Route::post('/store', [ObatController::class, 'store'])->name('store');
            Route::post('/store-stok', [ObatController::class, 'storeStok'])->name('store_stok');
            Route::get('/data', [ObatController::class, 'data'])->name('data_input');
            Route::get('/riwayat', [ObatController::class, 'riwayat'])->name('riwayat');
            Route::post('/store-usage', [ObatController::class, 'storeUsage'])->name('store_usage');
            Route::get('/laporan', [ObatController::class, 'laporan'])->name('laporan_pdf');
            Route::put('/update/{id}', [ObatController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [ObatController::class, 'destroy'])->name('delete');
        });

        // Manajemen Analytic
        Route::prefix('analytic')->name('admin.analytic.')->group(function () {
            Route::get('/dashboard', [AnalyticController::class, 'dashboard'])->name('dashboard');
            Route::get('/input', [AnalyticController::class, 'input'])->name('input');
            Route::post('/store', [AnalyticController::class, 'store'])->name('store'); 
            Route::get('/data', [AnalyticController::class, 'data'])->name('data');
            Route::get('/riwayat', [AnalyticController::class, 'riwayat'])->name('riwayat');
            Route::put('/update/{id}', [AnalyticController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [AnalyticController::class, 'destroy'])->name('delete');
            Route::get('/laporan', [AnalyticController::class, 'laporan'])->name('laporan_pdf');
            Route::get('/chart-data', [AnalyticController::class, 'getChartData'])->name('chart_data');
            Route::get('/kandang-stats/{id}', [AnalyticController::class, 'getKandangStats'])->name('kandang.stats');
        });
    
        // Manajemen Kandang
        Route::prefix('kandang')->name('admin.kandang.')->group(function () {
            Route::get('/dashboard', [KandangController::class, 'dashboard'])->name('dashboard');
            Route::get('/input', [KandangController::class, 'input'])->name('input');
            Route::post('/store-unit', [KandangController::class, 'storeUnit'])->name('store_unit');
            Route::post('/store-kandang', [KandangController::class, 'storeKandang'])->name('store_kandang');
            Route::get('/data-input', [KandangController::class, 'data'])->name('data_input');
            Route::put('/unit/update/{id}', [KandangController::class, 'updateUnit'])->name('update_unit');
            Route::put('/update/{id}', [KandangController::class, 'update'])->name('update'); 
            Route::delete('/unit/delete/{id}', [KandangController::class, 'destroyUnit'])->name('delete_unit');
            Route::delete('/delete/{id}', [KandangController::class, 'destroyKandang'])->name('delete_kandang');
            Route::get('/input-timbang', [KandangController::class, 'inputTimbang'])->name('input_timbang');
            Route::post('/store-timbang', [KandangController::class, 'storeTimbang'])->name('store_timbang');
            Route::get('/data-timbang', [KandangController::class, 'dataTimbang'])->name('data_timbang');
            Route::get('/riwayat/unit', [KandangController::class, 'riwayatUnit'])->name('riwayat.unit');
            Route::get('/riwayat/kandang', [KandangController::class, 'riwayatKandang'])->name('riwayat.kandang');
            Route::get('/riwayat/timbang', [KandangController::class, 'riwayatTimbang'])->name('riwayat.timbang');
            Route::get('/laporan', [KandangController::class, 'laporan'])->name('laporan');
            Route::get('/input_sampletelur', function () { return view('admin.kandang.input_sampletelur');})->name('input_sampletelur');
        });

        // Manajemen Pakan
        Route::prefix('pakan')->name('admin.pakan.')->group(function () {
            Route::get('/dashboard', [PakanController::class, 'dashboard'])->name('dashboard');
            Route::get('/input', [PakanController::class, 'input'])->name('input');
            Route::post('/store', [PakanController::class, 'store'])->name('store');
            Route::put('/update-stok/{id}', [PakanController::class, 'updateStok'])->name('update_stok');
            Route::post('/store-produksi', [PakanController::class, 'storeProduksi'])->name('store_produksi');
            
            // [BARU] Route Opname untuk Admin Pusat
            Route::post('/opname-pusat', [PakanController::class, 'opnamePusat'])->name('opname_pusat');
            Route::get('/data', [PakanController::class, 'data'])->name('data_input');
            Route::put('/update/{id}', [PakanController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [PakanController::class, 'destroy'])->name('delete');
            Route::get('/riwayat', [PakanController::class, 'riwayat'])->name('riwayat');
            Route::post('/store-distribution', [PakanController::class, 'storeDistribution'])->name('store_distribution');
            Route::post('/store-usage', [PakanController::class, 'storeUsage'])->name('store_usage');
            Route::delete('/riwayat/usage/{id}', [PakanController::class, 'destroyUsage'])->name('delete_usage');
            Route::delete('/riwayat/restock/{id}', [PakanController::class, 'destroyRestock'])->name('delete_restock');
            Route::get('/monitoring', [PakanController::class, 'monitoring'])->name('monitoring');
            Route::get('/laporan', [PakanController::class, 'laporan'])->name('laporan');
            Route::get('/surat-jalan/{id}', [PakanController::class, 'cetakSuratJalan'])->name('surat_jalan');
        });
    });

    // ====================================================
    // ROLE: MANDOR
    // ====================================================
    Route::prefix('mandor')->name('mandor.')->group(function () {
        Route::get('/dashboard', [DashboardMandorController::class, 'index'])->name('dashboard');
         Route::get('/produksi/input', [AnalyticController::class, 'mandorInput'])->name('produksi.input');
        Route::post('/produksi/store', [AnalyticController::class, 'store'])->name('produksi.store');
        Route::get('/produksi/data', [AnalyticController::class, 'mandorData'])->name('produksi.data');
        Route::get('/settingsmandor', function () { return view('mandor.settingsmandor'); })->name('settingsmandor');

        Route::prefix('pakan')->name('pakan.')->group(function () {
            Route::get('/input', [PakanController::class, 'inputMandor'])->name('input');
            Route::put('/terima', [PakanController::class, 'terimaStokMandor'])->name('update_stok');
            Route::post('/pakai', [PakanController::class, 'pakaiStokMandor'])->name('store_usage');
            
            // Route ini sudah ada & siap digunakan oleh form modal opname mandor
            Route::post('/opname', [PakanController::class, 'stockOpnameMandor'])->name('stock_opname');
            
            Route::put('/terima/{id}', [PakanController::class, 'terimaStokMandorById'])->name('terima_stok_id'); 
        });
    });

});