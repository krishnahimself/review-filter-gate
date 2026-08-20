<?php

use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Review')->name('home');

Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
