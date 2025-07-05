<?php

use App\Http\Controllers\AwardsController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LifePoemsController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PublicationsController;
use App\Http\Controllers\SiteData;
use App\Http\Controllers\SocialsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})
    ->middleware('auth:sanctum')
    ->name('user');

Route::get('/site-data', [SiteData::class, 'pull'])
    ->name('site-data');

Route::controller(HomeController::class)
    ->group(function () {
        Route::get('/home', 'index')
            ->name('home-index');
    });

Route::controller(AwardsController::class)
    ->group(function () {
        Route::get('/awards', 'index')
            ->name('awards-index');
    });


Route::controller(PublicationsController::class)
    ->group(function () {
        Route::get('/publications', 'index')
            ->name('publications-index');

        Route::get('/publications/{typeId}', 'getPublicationsByType')
            ->name('publications-by-type');
    });

Route::controller(NewsController::class)
    ->group(function () {
        Route::get('/news', 'index')
            ->name('news-index');
    });

Route::controller(EventsController::class)
    ->group(function () {
        Route::get('/events/past', 'getPast')
            ->name('events-past');

        Route::get('/events/future', 'getFuture')
            ->name('events-future');

        Route::get('/events/current', 'getCurrent')
            ->name('events-current');
    });

Route::controller(LifePoemsController::class)
    ->group(function () {
        Route::get('/life-poem', 'index')
            ->name('life-poem-index');
    });

Route::controller(SocialsController::class)
    ->group(function () {
        Route::get('/socials', 'index')
            ->name('socials-index');
    });
