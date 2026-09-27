<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrangTuaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\SantriController;
use App\Http\Controllers\SetoranController;
use App\Http\Controllers\UstadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect(auth()->user()->redirectPathByRole());
    }
    return view('landing');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', fn () => redirect(auth()->user()->redirectPathByRole()))->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');
    Route::resource('ustad', UstadController::class);
    Route::resource('orangtua', OrangTuaController::class);
    Route::resource('santri', SantriController::class);
    Route::resource('setoran', SetoranController::class);
    Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::get('/rekap/pdf', [RekapController::class, 'exportPdf'])->name('rekap.pdf');
    Route::get('/rekap/excel', [RekapController::class, 'exportExcel'])->name('rekap.excel');
});

Route::middleware(['auth', 'role:ustad'])->prefix('ustad')->name('ustad.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'ustad'])->name('dashboard');
    Route::resource('santri', SantriController::class)->only(['index', 'show', 'create', 'store']);
    Route::resource('setoran', SetoranController::class);
    Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::get('/rekap/pdf', [RekapController::class, 'exportPdf'])->name('rekap.pdf');
    Route::get('/rekap/excel', [RekapController::class, 'exportExcel'])->name('rekap.excel');
});

Route::middleware(['auth', 'role:orang_tua'])->prefix('orangtua')->name('orangtua.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'orangtua'])->name('dashboard');
    Route::resource('santri', SantriController::class)->only(['index', 'show']);
    Route::resource('setoran', SetoranController::class)->only(['index', 'show']);
    Route::get('/rekap', [RekapController::class, 'index'])->name('rekap.index');
    Route::get('/rekap/pdf', [RekapController::class, 'exportPdf'])->name('rekap.pdf');
});

require __DIR__.'/auth.php';
