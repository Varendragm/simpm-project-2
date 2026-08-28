<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ManajerController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\TeknisiController;
use Illuminate\Support\Facades\Route;

// ===== AUTH =====
Route::get('/', fn () => redirect()->route('login'));
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ===== SUPERVISOR =====
Route::middleware(['auth', 'role:supervisor'])->prefix('supervisor')->name('supervisor.')->group(function () {
    Route::get('/dashboard', [SupervisorController::class, 'dashboard'])->name('dashboard');
    Route::get('/monitoring', [SupervisorController::class, 'monitoring'])->name('monitoring');
    Route::get('/monitoring/{mesin}', [SupervisorController::class, 'detailMesin'])->name('detail-mesin');
    Route::get('/maintenance', [SupervisorController::class, 'maintenance'])->name('maintenance');
    Route::get('/maintenance/tambah', [SupervisorController::class, 'jadwalTambahForm'])->name('jadwal.tambah');
    Route::post('/maintenance', [SupervisorController::class, 'jadwalSimpan'])->name('jadwal.simpan');
    Route::get('/riwayat', [SupervisorController::class, 'riwayat'])->name('riwayat');
    Route::get('/laporan', [SupervisorController::class, 'laporan'])->name('laporan');
    Route::get('/profil', [SupervisorController::class, 'profil'])->name('profil');
    Route::put('/profil', [SupervisorController::class, 'profilUpdate'])->name('profil.update');
});

// ===== TEKNISI =====
Route::middleware(['auth', 'role:teknisi'])->prefix('teknisi')->name('teknisi.')->group(function () {
    Route::get('/dashboard', [TeknisiController::class, 'dashboard'])->name('dashboard');
    Route::get('/jadwal/{schedule}', [TeknisiController::class, 'detailJadwal'])->name('jadwal.detail');
    Route::post('/jadwal/{schedule}/selesai', [TeknisiController::class, 'tandaiSelesai'])->name('jadwal.selesai');
    Route::post('/checklist/{item}/toggle', [TeknisiController::class, 'toggleChecklist'])->name('checklist.toggle');
    Route::get('/riwayat', [TeknisiController::class, 'riwayat'])->name('riwayat');
    Route::get('/performa', [TeknisiController::class, 'performa'])->name('performa');
    Route::get('/profil', [TeknisiController::class, 'profil'])->name('profil');
    Route::put('/profil', [TeknisiController::class, 'profilUpdate'])->name('profil.update');
});

// ===== MANAJER (read-only) =====
Route::middleware(['auth', 'role:manajer'])->prefix('manajer')->name('manajer.')->group(function () {
    Route::get('/dashboard', [ManajerController::class, 'dashboard'])->name('dashboard');
    Route::get('/performa', [ManajerController::class, 'performa'])->name('performa');
    Route::get('/maintenance', [ManajerController::class, 'maintenance'])->name('maintenance');
    Route::get('/laporan', [ManajerController::class, 'laporan'])->name('laporan');
    Route::get('/profil', [ManajerController::class, 'profil'])->name('profil');
    Route::put('/profil', [ManajerController::class, 'profilUpdate'])->name('profil.update');
});
