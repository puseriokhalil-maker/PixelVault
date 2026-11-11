<?php

use App\Http\Controllers\AdminArtworkController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ArtworkController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ArtworkController::class, 'index'])->name('home');
Route::get('/artworks/{artwork:slug}', [ArtworkController::class, 'show'])->name('artworks.show');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/{artwork:slug}', [CartController::class, 'store'])->name('cart.items.store');
Route::delete('/cart/{artwork:slug}', [CartController::class, 'destroy'])->name('cart.items.destroy');

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('login.store');

Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('register.store');

Route::get('/dashboard', DashboardController::class)
    ->middleware('auth')
    ->name('dashboard');

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', LogoutController::class)->name('logout');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
});

Route::middleware(['auth', 'can:view-admin-dashboard'])->group(function (): void {
    Route::get('/admin', AdminDashboardController::class)->name('admin.dashboard');
    Route::get('/admin/artworks/create', [AdminArtworkController::class, 'create'])->name('admin.artworks.create');
    Route::post('/admin/artworks', [AdminArtworkController::class, 'store'])->name('admin.artworks.store');
});
