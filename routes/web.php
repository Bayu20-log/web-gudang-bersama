<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PemasokController;
use App\Http\Controllers\KondisiController;
use App\Http\Controllers\LokasiController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LaporanAsetController;
use App\Http\Controllers\OmzetController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\DashboardGudangController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StockNotificationController;

use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
})->name('welcome');
Route::get('/welcome', function () {
    return view('home');
});

//route di landing page
    Route::get('/profil', function () {
        return view('landingpage.profil');
    })->name('profil');

    Route::get('/fitur/gudang', function () {
        return view('landingpage.gudang');
    })->name('gudang');

    Route::get('/fitur/superadmin', function () {
        return view('landingpage.superadmin');
    })->name('superadmin');

    Route::get('/fitur/viewer', function () {
        return view('landingpage.viewer');
    })->name('viewer');

    Route::get('/faq', function () {
        return view('landingpage.faq');
    })->name('faq');

    Route::get('/galeri', function () {
        return view('landingpage.galeri');
    })->name('galeri');

    Route::get('/kontak', function () {
        return view('landingpage.kontak');
    })->name('kontak');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'auto.logout'])->group(function () {
    Route::get('/dashboard/superadmin', [App\Http\Controllers\UserController::class, 'dashboardSuperadmin'])->name('dashboard.superadmin');

    Route::get('/dashboard/gudang', [DashboardGudangController::class, 'index'])->name('dashboard.gudang');


    Route::get('/dashboard/viewer', function () {
        return view('dashboard.viewer');
    })->name('dashboard.viewer');

    Route::get('/stok-terpakai', [BarangKeluarController::class, 'cekStok'])->name('barang-keluar.cekStok');
    Route::get('/barang-keluar/detail-barang', [BarangKeluarController::class, 'getDetailBarang'])->name('barang-keluar.detail-barang');

   Route::middleware(['auth', 'auto.logout'])->group(function () {
        Route::get('/laporan', [App\Http\Controllers\LaporanController::class, 'index'])->name('laporan');
        //Route::get('/laporan/stok-barang', [LaporanController::class, 'stok'])->name('laporan.stok');
        Route::get('/laporan/arus-barang', [App\Http\Controllers\LaporanController::class, 'arus'])->name('laporan.arus');
        Route::get('/laporan/print/pdf', [ExportController::class, 'exportPdf'])->name('laporan.pdf');
        Route::get('/laporan/print/excel', [ExportController::class, 'exportExcel'])->name('laporan.excel');
        Route::get('/laporan/arus/pdf', [ExportController::class, 'exportArusPdf'])->name('laporan.arus.pdf');
        Route::get('/laporan/arus/excel', [ExportController::class, 'exportArusExcel'])->name('laporan.arus.excel');
        
    });
    Route::middleware(['auth', 'role:gudang'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-read', [NotificationController::class, 'markRead'])->name('notifications.markRead');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markSingleRead'])->name('notifications.markSingleRead');
    });

    Route::middleware(['auth', 'role:gudang'])->group(function () {
    Route::get('/stock-notifications', [StockNotificationController::class, 'index'])->name('stock-notifications.index');
    });


    
     // ========== Fitur Barang Keluar (Tambahan AJAX) ==========
    Route::get('/stok-terpakai', [BarangKeluarController::class, 'cekStok'])->name('barang-keluar.cekStok');
    Route::get('/barang-keluar/detail-barang', [BarangKeluarController::class, 'getDetailBarang'])->name('barang-keluar.detail-barang');
    Route::get('/barang-keluar/barang-bersisa', [BarangKeluarController::class, 'getBarangBersisa'])->name('barang-keluar.barang-bersisa');
    Route::get('/barang-keluar/pilihan-barang', [BarangKeluarController::class, 'getPilihanBarangUnik'])->name('barang-keluar.pilihan-barang');

    // ✅ Triple Dropdown AJAX support
    Route::get('/barang-keluar/lokasi-by-kode', [BarangKeluarController::class, 'getLokasiByKode'])->name('barang-keluar.lokasi-by-kode');
    Route::get('/barang-keluar/kondisi-by-kode-lokasi', [BarangKeluarController::class, 'getKondisiByKodeLokasi'])->name('barang-keluar.kondisi-by-kode-lokasi');

        // Laporan
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan');
        Route::get('/laporan/arus-barang', [LaporanController::class, 'arus'])->name('laporan.arus');
        Route::get('/laporan/print/pdf', [ExportController::class, 'exportPdf'])->name('laporan.pdf');
        Route::get('/laporan/print/excel', [ExportController::class, 'exportExcel'])->name('laporan.excel');
        Route::get('/laporan/arus/pdf', [ExportController::class, 'exportArusPdf'])->name('laporan.arus.pdf');
        Route::get('/laporan/arus/excel', [ExportController::class, 'exportArusExcel'])->name('laporan.arus.excel');
        Route::get('/export/omzet/pdf', [ExportController::class, 'exportOmzetPdf'])->name('export.omzet.pdf');
        Route::get('/export/omzet/excel', [ExportController::class, 'exportOmzetExcel'])->name('export.omzet.excel');
        Route::get('/laporan/aset', [LaporanAsetController::class, 'index'])->name('laporan.aset');
        Route::get('/export-aset-excel', [ExportController::class, 'exportAsetExcel'])->name('export.aset.excel');
        Route::get('/export-aset-pdf', [ExportController::class, 'exportAsetPdf'])->name('export.aset.pdf');
        


    // 👇 Hanya untuk role GUDANG
    Route::middleware(['role:gudang'])->group(function () {
        Route::resource('barang-masuk', BarangMasukController::class);
        Route::resource('barang-keluar', BarangKeluarController::class);
        Route::get('barang-masuk/{barangMasuk}', [BarangMasukController::class, 'show'])->name('barang-masuk.show');
        Route::resource('pemasok', PemasokController::class);
        Route::resource('kondisi', KondisiController::class);
        Route::resource('satuan', SatuanController::class);
        Route::resource('item', ItemController::class);
        Route::resource('lokasi', LokasiController::class);
        Route::resource('kategori', KategoriController::class);
        Route::resource('aset', LaporanAsetController::class);
        Route::resource('omzet', OmzetController::class);
    });
    //untuk view stok
    Route::middleware(['auth'])->group(function () {
        Route::get('/laporan-stok-viewer', [LaporanController::class, 'stokViewer'])
            ->name('laporan.stok.viewer');
        Route::get('/laporan-stok-admin', [LaporanController::class, 'stokAdmin'])
            ->name('laporan.stok.admin');
    });
    // Master data
        Route::resource('pemasok', PemasokController::class);
        Route::resource('kondisi', KondisiController::class);
        Route::resource('satuan', SatuanController::class);
        Route::resource('item', ItemController::class);
        Route::resource('lokasi', LokasiController::class);
        Route::resource('kategori', KategoriController::class);
    });

    //barang masuk
    Route::get('barang-masuk/{barangMasuk}', [BarangMasukController::class, 'show'])->name('barang-masuk.show');
        Route::get('/barang-masuk/{id}/qr-card', [BarangMasukController::class, 'qrCard'])->name('barang-masuk.qr-card');
        Route::get('/barang-masuk/{id}/print', [BarangMasukController::class, 'print'])->name('barang-masuk.print');
        Route::get('/barang-masuk/{id}/cetak-pdf', [BarangMasukController::class, 'cetakPDF'])->name('barang-masuk.cetak.pdf');
        Route::get('/barang-masuk/{id}/cetak-ba', [BarangMasukController::class, 'cetakBeritaAcara'])->name('barang-masuk.cetak-berita-acara');
        Route::get('/barang-masuk/{id}/cetak-qr-kecil', [BarangMasukController::class, 'cetakQRKecil'])->name('barang-masuk.cetak-qr-kecil');
     // ✅ Detail Barang Keluar + Cetak BA + cetak detail
    Route::get('/barang-keluar/{id}/detail', [BarangKeluarController::class, 'show'])->name('barang-keluar.detail');
    Route::get('/barang-keluar/{id}/cetak-ba', [BarangKeluarController::class, 'cetakBA'])->name('barang-keluar.cetak-ba');
    Route::get('/barang-keluar/{id}/cetak-detail', [BarangKeluarController::class, 'cetakDetail'])->name('barang-keluar.cetak-detail');

        // 👇 Hanya untuk role SUPERADMIN
    Route::middleware(['auth', 'role:superadmin'])->group(function () {
        Route::get('/kelola-user', [UserController::class, 'index'])->name('user.index');
        Route::get('/kelola-user/create', [UserController::class, 'create'])->name('user.create');
        Route::post('/kelola-user', [UserController::class, 'store'])->name('user.store');
        Route::get('/kelola-user/{user}/edit', [UserController::class, 'edit'])->name('user.edit');
        Route::put('/kelola-user/{user}', [UserController::class, 'update'])->name('user.update');
        Route::patch('/user/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('user.toggleStatus');

    
    });

    

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/{user}', [ProfileController::class, 'update'])->name('profile.update');
});




Route::get('/learn-more', function () {
    return view('learn-more');
})->name('learn.more');

require __DIR__.'/auth.php';
