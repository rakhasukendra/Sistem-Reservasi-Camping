<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\StaffController;
use App\Models\Article;
use Illuminate\Support\Facades\Route;

Route::get('/', [BookingController::class, 'index'])->name('home');
Route::get('/artikel/{article:slug}', fn (Article $article) => $article->is_published ? view('article', compact('article')) : abort(404))->name('articles.show');

Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.store');
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/keluar', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profil', [AuthController::class, 'profile'])->name('profile');
    Route::put('/profil', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::delete('/hapus-akun', [AuthController::class, 'deleteAccount'])->name('deleteAccount');

    Route::middleware('role:user')->group(function () {
        Route::get('/reservasi-saya', [BookingController::class, 'myBookings'])->name('bookings.index');
        Route::get('/keranjang', [BookingController::class, 'cart'])->name('cart');
        Route::post('/keranjang/tambah/{item}', [BookingController::class, 'addToCart'])->name('cart.add');
        Route::post('/keranjang/hapus/{item}', [BookingController::class, 'removeFromCart'])->name('cart.remove');
        Route::post('/keranjang/update/{item}', [BookingController::class, 'updateCart'])->name('cart.update');
        Route::post('/keranjang/kosongkan', [BookingController::class, 'clearCart'])->name('cart.clear');
        Route::get('/reservasi/buat', [BookingController::class, 'create'])->name('bookings.create');
        Route::post('/ketersediaan', [BookingController::class, 'checkAvailability'])->middleware('throttle:30,1')->name('bookings.availability');
        Route::post('/reservasi', [BookingController::class, 'store'])->name('bookings.store');
        Route::post('/reservasi/{booking}/batal', [BookingController::class, 'cancel'])->name('bookings.cancel');
    });
});

Route::prefix('staff')->name('staff.')->middleware(['auth', 'role:staff,admin'])->group(function () {
    Route::get('/', [StaffController::class, 'rentals'])->name('dashboard');
    Route::get('/stok', [StaffController::class, 'inventory'])->name('inventory');
    Route::get('/sewa', [StaffController::class, 'rentals'])->name('rentals');
    Route::get('/artikel', [StaffController::class, 'articles'])->name('articles');
    Route::get('/dashboard', [StaffController::class, 'analytics'])->name('analytics');
    Route::post('/reservasi/{booking}/keputusan', [StaffController::class, 'decide'])->name('bookings.decide');
    Route::post('/reservasi/{booking}/verifikasi-kontak', [StaffController::class, 'verifyContact'])->name('bookings.verify');
    Route::post('/reservasi/{booking}/pengambilan', [StaffController::class, 'pickup'])->name('bookings.pickup');
    Route::post('/reservasi/{booking}/kondisi', [StaffController::class, 'condition'])->name('bookings.condition');
    Route::post('/alat', [StaffController::class, 'saveItem'])->name('items.store');
    Route::get('/alat/{item}/edit', [StaffController::class, 'editItem'])->name('items.edit');
    Route::put('/alat/{item}', [StaffController::class, 'saveItem'])->name('items.update');
    Route::delete('/alat/{item}', [StaffController::class, 'deleteItem'])->name('items.destroy');
    Route::post('/artikel', [StaffController::class, 'saveArticle'])->name('articles.store');
    Route::put('/artikel/{article}', [StaffController::class, 'saveArticle'])->name('articles.update');
    Route::delete('/artikel/{article}', [StaffController::class, 'deleteArticle'])->name('articles.destroy');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::post('/akun', [StaffController::class, 'saveUser'])->name('users.store');
    Route::put('/akun/{user}', [StaffController::class, 'saveUser'])->name('users.update');
    Route::delete('/akun/{user}', [StaffController::class, 'deleteUser'])->name('users.delete');
    Route::get('/penilaian-staff', [StaffController::class, 'assessmentPage'])->name('assessments');
    Route::post('/staff/{staff}/nilai', [StaffController::class, 'assessStaff'])->name('staff.assess');
    Route::put('/penilaian/{log}', [StaffController::class, 'updateAssessment'])->name('assessment.update');
    Route::delete('/penilaian/{log}', [StaffController::class, 'deleteAssessment'])->name('assessment.delete');
});
