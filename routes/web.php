<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\ChromebookController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\DashboardController;
use App\Models\Chromebook;
use Illuminate\Support\Facades\Auth;


// Halaman utama: landing page
Route::get('/', function () {
    return view('index');
})->name('landing');

// Route untuk peminjaman Chromebook
Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
Route::get('/scan', [PeminjamanController::class, 'index'])->name('peminjaman'); // Scan QR tanpa login

// Login & Logout
Route::get('/login', function () {
    // Jika sudah login, redirect ke dashboard
    if (auth::check()) {
        return redirect()->route('dashboard');
    }
    return app(App\Http\Controllers\AuthController::class)->loginForm();
})->name('login');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Route setelah QR discan: menampilkan form peminjaman atau pengembalian
Route::get('/peminjaman/result', [PeminjamanController::class, 'result'])->name('peminjaman.result');
Route::get('/peminjaman/form/{kode_chromebook}', [PeminjamanController::class, 'showForm'])->name('peminjaman.form');
Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');

// Route untuk pengembalian chromebook
Route::get('/peminjaman/return/{kode_chromebook}', [PeminjamanController::class, 'showReturnForm'])->name('peminjaman.return');
Route::post('/peminjaman/return', [PeminjamanController::class, 'returnChromebook'])->name('peminjaman.return.store');

// API: ambil data siswa/guru via ID (untuk autofill di form)
Route::get('/get-siswa/{id}', function ($id) {
    $siswa = App\Models\Siswa::find($id);
    return response()->json($siswa);
});
Route::get('/get-guru/{id}', function ($id) {
    $guru = App\Models\Guru::find($id);
    return response()->json($guru);
});

// Semua route ini hanya bisa diakses jika sudah login (admin)
Route::middleware('auth')->group(function () {
    Route::get('/siswa/data', [SiswaController::class, 'getData'])->name('siswa.data');
    Route::resource('siswa', SiswaController::class);
    Route::post('/siswa/import', [SiswaController::class, 'import'])->name('siswa.import'); // Route untuk import data siswa
    Route::get('/guru/data', [GuruController::class, 'getData'])->name('guru.data');
    Route::post('/guru/import', [GuruController::class, 'import'])->name('guru.import');
    Route::get('/chromebook/data', [ChromebookController::class, 'getData'])->name('chromebook.data');
    Route::post('/chromebook/import', [ChromebookController::class, 'import'])->name('chromebook.import');
    Route::get('/peminjaman/data', [PeminjamanController::class, 'getData'])->name('peminjaman.data');
    Route::resource('guru', GuruController::class);
    Route::resource('chromebook', ChromebookController::class);

    // Dashboard untuk admin, menampilkan data peminjaman
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
