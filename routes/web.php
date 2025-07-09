<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\ProdukController;

// Redirect ke login
Route::get('/', function () {
    // return view('welcome');
    return redirect()->route('backend.login');
});

// Login & Logout
Route::get('backend/login', [LoginController::class, 'loginBackend'])->name('backend.login');
Route::post('backend/login', [LoginController::class, 'authenticateBackend'])->name('backend.login');
Route::post('backend/logout', [LoginController::class, 'logoutBackend'])->name('backend.logout');

// Beranda
Route::get('backend/beranda', [BerandaController::class, 'berandaBackend'])->name('backend.beranda')->middleware('auth');
Route::get('/backend/beranda', [BerandaController::class, 'index'])->name('backend.beranda');
Route::get('/produk-per-kategori/{namaKategori}', [BerandaController::class, 'produkPerKategori'])->name('produk.perKategori');


// User
Route::resource('backend/user', UserController::class, ['as' => 'backend'])->middleware('auth');

// Laporan User
Route::get('backend/laporan/formuser', [UserController::class, 'formUser'])->name('backend.laporan.formuser')->middleware('auth');
Route::post('backend/laporan/cetakuser', [UserController::class, 'cetakUser'])->name('backend.laporan.cetakuser')->middleware('auth');

// Kategori
Route::resource('backend/kategori', KategoriController::class, ['as' => 'backend'])->middleware('auth');

// Produk
Route::resource('backend/produk', ProdukController::class, ['as' => 'backend'])->middleware('auth');

// Foto Produk
Route::post('foto-produk/store', [ProdukController::class, 'storeFoto'])->name('backend.foto_produk.store')->middleware('auth');
Route::delete('foto-produk/{id}', [ProdukController::class, 'destroyFoto'])->name('backend.foto_produk.destroy')->middleware('auth');

// Laporan Produk
Route::get('backend/laporan/formproduk', [ProdukController::class, 'formProduk'])->name('backend.laporan.formproduk')->middleware('auth');
Route::post('backend/laporan/cetakproduk', [ProdukController::class, 'cetakProduk'])->name('backend.laporan.cetakproduk')->middleware('auth');
//Route::get('/produk/laporan', [ProdukController::class, 'laporan'])->name('produk.laporan');
//Route::get('/produk/laporan/cetak', [ProdukController::class, 'cetakLaporan'])->name('produk.laporan.cetak');
