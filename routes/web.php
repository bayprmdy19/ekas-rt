<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IuranController;
use App\Http\Controllers\KasKeluarController;
use App\Http\Controllers\KasMasukController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicDashboardController;
use App\Http\Controllers\SuratController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Tempat pendaftaran seluruh route aplikasi E-KAS RT.
|
*/

// Route Public Dashboard (Tanpa Login)
Route::get('/', [PublicDashboardController::class, 'index'])->name('public.dashboard');

// Route Form Layanan Warga (Pengaduan & Administrasi Surat)
Route::post('/layanan-warga', [PublicDashboardController::class, 'storeLayanan'])->name('layanan.warga');

// Route Dashboard Multi-Role (Pengurus RT & Warga)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::post('/admin/kas', [DashboardController::class, 'storeKas'])->name('admin.kas.store');
    Route::post('/admin/warga', [DashboardController::class, 'storeWarga'])->name('admin.warga.store');
    Route::post('/admin/iuran', [DashboardController::class, 'storeIuran'])->name('admin.iuran.store');

    // Route Manajemen Iuran & Tagihan Admin
    Route::get('/admin/iuran', [IuranController::class, 'index'])->name('admin.iuran.index');
    Route::post('/admin/master-iuran', [IuranController::class, 'storeMaster'])->name('admin.master-iuran.store');
    Route::put('/admin/master-iuran/{id}', [IuranController::class, 'updateMaster'])->name('admin.master-iuran.update');
    Route::delete('/admin/master-iuran/{id}', [IuranController::class, 'destroyMaster'])->name('admin.master-iuran.destroy');
    Route::post('/admin/tagihan/generate', [IuranController::class, 'generateTagihan'])->name('admin.tagihan.generate');
    Route::patch('/admin/tagihan/{id}/toggle', [IuranController::class, 'toggleStatusTagihan'])->name('admin.tagihan.toggle');

    // Route Manajemen Pembayaran Admin
    Route::get('/admin/pembayaran', [PembayaranController::class, 'index'])->name('admin.pembayaran.index');
    Route::post('/admin/pembayaran', [PembayaranController::class, 'store'])->name('admin.pembayaran.store');
    Route::post('/admin/rekening', [PembayaranController::class, 'storeRekening'])->name('admin.rekening.store');
    Route::delete('/admin/rekening/{id}', [PembayaranController::class, 'destroyRekening'])->name('admin.rekening.destroy');

    // Route Manajemen Kas Masuk Admin
    Route::get('/admin/kas-masuk', [KasMasukController::class, 'index'])->name('admin.kas-masuk.index');
    Route::post('/admin/kas-masuk', [KasMasukController::class, 'store'])->name('admin.kas-masuk.store');
    Route::put('/admin/kas-masuk/{id}', [KasMasukController::class, 'update'])->name('admin.kas-masuk.update');
    Route::delete('/admin/kas-masuk/{id}', [KasMasukController::class, 'destroy'])->name('admin.kas-masuk.destroy');

    // Route Manajemen Kas Keluar Admin
    Route::get('/admin/kas-keluar', [KasKeluarController::class, 'index'])->name('admin.kas-keluar.index');
    Route::post('/admin/kas-keluar', [KasKeluarController::class, 'store'])->name('admin.kas-keluar.store');
    Route::put('/admin/kas-keluar/{id}', [KasKeluarController::class, 'update'])->name('admin.kas-keluar.update');
    Route::delete('/admin/kas-keluar/{id}', [KasKeluarController::class, 'destroy'])->name('admin.kas-keluar.destroy');

    // Route Manajemen Pengaduan Admin
    Route::get('/admin/pengaduan', [PengaduanController::class, 'index'])->name('admin.pengaduan.index');
    Route::post('/admin/pengaduan', [PengaduanController::class, 'store'])->name('admin.pengaduan.store');
    Route::patch('/admin/pengaduan/{id}/status', [PengaduanController::class, 'updateStatus'])->name('admin.pengaduan.update-status');
    Route::patch('/admin/pengaduan/{id}/rating', [PengaduanController::class, 'updateRating'])->name('admin.pengaduan.update-rating');
    Route::delete('/admin/pengaduan/{id}', [PengaduanController::class, 'destroy'])->name('admin.pengaduan.destroy');

    // Route Manajemen Surat Admin
    Route::get('/admin/surat', [SuratController::class, 'index'])->name('admin.surat.index');
    Route::post('/admin/surat', [SuratController::class, 'store'])->name('admin.surat.store');
    Route::put('/admin/surat/{id}', [SuratController::class, 'update'])->name('admin.surat.update');
    Route::patch('/admin/surat/{id}/status', [SuratController::class, 'updateStatus'])->name('admin.surat.update-status');
    Route::delete('/admin/surat/{id}', [SuratController::class, 'destroy'])->name('admin.surat.destroy');
    Route::get('/admin/surat/{id}/cetak', [SuratController::class, 'cetak'])->name('admin.surat.cetak');

    Route::get('/warga/dashboard', [DashboardController::class, 'warga'])->name('warga.dashboard');
    Route::patch('/warga/pengaduan/{id}/rating', [PengaduanController::class, 'updateRating'])->name('warga.pengaduan.rating');
    Route::get('/warga/surat/{id}/download', [DashboardController::class, 'downloadSurat'])->name('warga.surat.download');
});

// Route Manajemen Profil Admin (Bawaan Laravel Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';