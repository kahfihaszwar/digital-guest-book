<?php

use App\Http\Controllers\GuestbookController;
use Illuminate\Support\Facades\Route;

Route::get('/', [GuestbookController::class, 'index'])->name('guestbook.index');
Route::post('/entries', [GuestbookController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('guestbook.store');
