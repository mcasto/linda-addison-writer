<?php

use App\Http\Controllers\SiteData;
use App\Models\Freebie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/site-data', [SiteData::class, 'pull']);


Route::get('/test-freebie', function (): JsonResponse {
    $freebie = Freebie::find(1);
    return response()->json($freebie);
});
