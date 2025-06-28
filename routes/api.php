<?php

use App\Http\Controllers\SiteData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/site-data', [SiteData::class, 'pull']);
