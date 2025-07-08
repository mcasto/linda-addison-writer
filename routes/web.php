<?php

use Illuminate\Support\Facades\Route;

Route::get('/login', function () {
    return response()->json('Invalid token', 401);
})->name('login');
