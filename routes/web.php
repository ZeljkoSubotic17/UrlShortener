<?php

use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UrlController::class, 'index']);

Route::post('/shorten', [UrlController::class, 'shorten']);
Route::get('/something/{hash}', [UrlController::class, 'redirect']);
