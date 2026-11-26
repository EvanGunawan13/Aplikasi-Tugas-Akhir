<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RackController;
use App\Http\Controllers\ProductRackController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ItemSearchController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PartnerCompanyController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PartnerOutputController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes - Mandiri POS (CV Mandiri Diesel)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    // Dashboard umum untuk pemilik, admin, petugas gudang
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Endpoint JSON Pencarian Barang (bisa diakses semua role yang login)
    Route::get('/api/items/search', [ItemSearchController::class, 'searchJson'])->name('api.items.search');

    // Fitur Cari Barang & Lihat Katalog (Pemilik, Petugas Gudang, Kasir)
    Route::middleware(['role:pemilik,petugas_gudang,kasir'])->group(function () {
        Route::get('/items/search', [ItemSearchController::class, 'index'])->name('items.search');
    });

    // Profil Pengguna
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Hak Akses: Kasir & Admin (Penjualan POS)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:kasir,admin'])->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::post('/pos', [PosController::class, 'store'])->name('pos.store');
        Route::get('/pos/success/{sale}', [PosController::class, 'success'])->name('pos.success');
        Route::get('/pos/receipt/{sale}', [PosController::class, 'receipt'])->name('pos.receipt');
        Route::get('/pos/receipt/{sale}/pdf', [PosController::class, 'receiptPdf'])->name('pos.receipt.pdf');
    });

    /*
    |--------------------------------------------------------------------------
    | Hak Akses: Petugas Gudang (Rak, Barang, Cek Stok, Koreksi Stok)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:petugas_gudang'])->group(function () {
        // Kelola Data Rak
        Route::resource('racks', RackController::class);

        // Kelola Data Barang & Foto Katalog
        Route::resource('products', ProductController::class);
        Route::post('/products/{product}/photos/{photo}/primary', [ProductController::class, 'setPrimaryPhoto'])->name('products.photos.primary');
        Route::delete('/products/{product}/photos/{photo}', [ProductController::class, 'deletePhoto'])->name('products.photos.destroy');

        // Tempatkan Barang ke Rak
        Route::get('/product-racks', [ProductRackController::class, 'index'])->name('product-racks.index');
        Route::post('/product-racks', [ProductRackController::class, 'store'])->name('product-racks.store');
        Route::delete('/product-racks/{product}/{rack}', [ProductRackController::class, 'destroy'])->name('product-racks.destroy');

        // Cek Stok
        Route::get('/stock', [StockController::class, 'index'])->name('stock.index');

        // Koreksi Stok Fisik
        Route::get('/stock/adjustment', [StockController::class, 'adjustment'])->name('stock.adjustment');
        Route::post('/stock/adjustment', [StockController::class, 'storeAdjustment'])->name('stock.adjustment.store');
    });

    /*
    |--------------------------------------------------------------------------
    | Hak Akses: Admin (Pemasok, Rekanan, Pembelian, Barang Keluar B2B, Void)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->group(function () {
        // Pemasok & Perusahaan Rekanan
        Route::resource('suppliers', SupplierController::class)->except(['show']);
        Route::resource('partners', PartnerCompanyController::class)->except(['show']);

        // Pembelian dari Pemasok
        Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show']);

        // Barang Keluar Rekanan B2B
        Route::resource('partner-outputs', PartnerOutputController::class)->only(['index', 'create', 'store', 'show']);

        // Void Transaksi POS
        Route::post('/sales/{sale}/void', [PosController::class, 'void'])->name('sales.void');
    });

    /*
    |--------------------------------------------------------------------------
    | Hak Akses: Pemilik & Admin (Rekapitulasi Transaksi & Cetak PDF)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:pemilik,admin'])->group(function () {
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/pdf', [ReportController::class, 'downloadPdf'])->name('reports.pdf');
    });

    /*
    |--------------------------------------------------------------------------
    | Hak Akses: Pemilik (Kelola Akun Pengguna)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:pemilik'])->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });
});

require __DIR__.'/auth.php';
