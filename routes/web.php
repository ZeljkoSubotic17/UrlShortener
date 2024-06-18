<?php

use App\Http\Controllers\UrlController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::post('/shorten', [UrlController::class, 'shorten']);
Route::get('/something/{hash}', [UrlController::class, 'redirect']);
