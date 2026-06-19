<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';

Route::get('/chat', [App\Http\Controllers\ChatController::class, 'index'])->name('chat.index');
Route::post('/chat', [App\Http\Controllers\ChatController::class, 'chat'])->name('chat.store');
