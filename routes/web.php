<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BerandaController;
use App\Http\Controllers\InformasiController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\PengurusAnggotaController;
use App\Http\Controllers\PortalController;
use App\Http\Controllers\RapatController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BerandaController::class, 'index'])->name('home');
Route::get('/profil', [InformasiController::class, 'profil'])->name('profil.index');
Route::get('/galeri', [InformasiController::class, 'galeri'])->name('galeri.index');
Route::get('/kontak', [InformasiController::class, 'kontak'])->name('kontak.index');

Route::get('/kegiatan', [KegiatanController::class, 'index'])->name('kegiatan.index');
Route::middleware(['himatif.auth', 'himatif.role:pengurus'])->group(function (): void {
    Route::get('/kegiatan/create', [KegiatanController::class, 'create'])->name('kegiatan.create');
});
Route::get('/kegiatan/{id}', [KegiatanController::class, 'show'])->name('kegiatan.show');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:himatif-login')->name('login.store');
    Route::get('/registrasi', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/registrasi', [AuthController::class, 'register'])->middleware('throttle:himatif-register')->name('register.store');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('himatif.auth')->name('logout');

Route::middleware('himatif.auth')->prefix('portal')->name('portal.')->group(function (): void {
    Route::get('/', [PortalController::class, 'dashboard'])->name('dashboard');
    Route::get('/rapat', [RapatController::class, 'index'])->name('rapat.index');
    Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
    Route::get('/absensi/create', [AbsensiController::class, 'create'])->name('absensi.create');
    Route::post('/absensi', [AbsensiController::class, 'store'])->name('absensi.store');
    Route::get('/absensi/{id}', [AbsensiController::class, 'show'])->name('absensi.show');
});

Route::middleware(['himatif.auth', 'himatif.role:pengurus'])->prefix('portal/pengurus')->name('pengurus.')->group(function (): void {
    Route::get('/anggota', [PengurusAnggotaController::class, 'index'])->name('anggota.index');
    Route::patch('/anggota/{id}/verifikasi', [PengurusAnggotaController::class, 'approve'])->name('anggota.approve');
    Route::get('/rekap-absensi', [AbsensiController::class, 'rekap'])->name('absensi.index');
    Route::get('/rapat/create', [RapatController::class, 'create'])->name('rapat.create');
    Route::post('/rapat', [RapatController::class, 'store'])->name('rapat.store');
    Route::get('/rapat/{id}/edit', [RapatController::class, 'edit'])->name('rapat.edit');
    Route::put('/rapat/{id}', [RapatController::class, 'update'])->name('rapat.update');
    Route::patch('/rapat/{id}/status', [RapatController::class, 'ubahStatus'])->name('rapat.status');
    Route::delete('/rapat/{id}', [RapatController::class, 'destroy'])->name('rapat.destroy');
});

Route::middleware(['himatif.auth', 'himatif.role:pengurus'])->group(function (): void {
    Route::post('/kegiatan', [KegiatanController::class, 'store'])->name('kegiatan.store');
    Route::get('/kegiatan/{id}/edit', [KegiatanController::class, 'edit'])->name('kegiatan.edit');
    Route::put('/kegiatan/{id}', [KegiatanController::class, 'update'])->name('kegiatan.update');
    Route::delete('/kegiatan/{id}', [KegiatanController::class, 'destroy'])->name('kegiatan.destroy');
});
