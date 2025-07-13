<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AwardsController;
use App\Http\Controllers\BiblioController;
use App\Http\Controllers\BioController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DesignCreditsController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\FreebiesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\HonorsController;
use App\Http\Controllers\LessonsBlessingsController;
use App\Http\Controllers\LifePoemsController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\OnlineResourcesController;
use App\Http\Controllers\PublicationsController;
use App\Http\Controllers\ReviewsQuotesController;
use App\Http\Controllers\SeeHearReadController;
use App\Http\Controllers\SiteData;
use App\Http\Controllers\SocialsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/login', function () {
    // this is just to catch Sanctum's autoredirect to [login]
    return response()->json('Invalid token', 401);
})->name('login');

Route::get('/site-data', [SiteData::class, 'pull'])
    ->name('site-data');

Route::controller(HomeController::class)
    ->group(function () {
        Route::get('/home', 'index')
            ->name('home-index');

        Route::get('/admin/covers', 'index')
            ->middleware('auth:admin')
            ->name('admin-covers-index');

        Route::put('/admin/covers/{id}', 'update')
            ->middleware('auth:admin')
            ->name('admin-cover-update');

        Route::post('/admin/covers/{id}', 'store')
            ->middleware('auth:admin')
            ->name('admin-cover-store');

        Route::delete('/admin/covers/{id}', 'destroy')
            ->middleware('auth:admin')
            ->name('admin-cover-update');

        Route::post('/admin/covers/upload/{id}', 'uploadImage')
            ->middleware('auth:admin')
            ->name('admin-cover-upload');
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

        Route::get('/admin/events', 'index')
            ->middleware('auth:admin')
            ->name('admin-events-index');

        Route::put('/admin/events/{id}', 'update')
            ->middleware('auth:admin')
            ->name('admin-events-update');

        Route::post('/admin/events/{id}', 'store')
            ->middleware('auth:admin')
            ->name('admin-events-store');


        Route::delete('/admin/events/{id}', 'destroy')
            ->middleware('auth:admin')
            ->name('admin-events-destroy');
    });

Route::controller(SeeHearReadController::class)
    ->group(function () {
        Route::get('/finds', 'index')
            ->name('finds-index');

        Route::get('/finds/{typeId}', 'getFindsByType')
            ->name('finds-by-type');
    });

Route::controller(BioController::class)
    ->group(function () {
        Route::get('/bio', 'index')
            ->name('bio-index');
    });

Route::controller(ContactController::class)
    ->group(function () {
        Route::post('/contact', 'store')
            ->name('contact-store');

        Route::get('/admin/contacts', 'index')
            ->middleware('auth:admin')
            ->name('admin-contacts-index');

        Route::put('/admin/contact/{id}', 'update')
            ->middleware('auth:admin')
            ->name('admin-contact-update');

        Route::delete('/admin/contact/{id}', 'destroy')
            ->middleware('auth:admin')
            ->name('admin-contact-delete');
    });

Route::controller(LessonsBlessingsController::class)
    ->group(function () {
        Route::get('/lessons-and-blessings', 'index')
            ->name('lessons-and-blessings-index');
    });

Route::controller(FreebiesController::class)
    ->group(function () {
        Route::get('/freebies', 'index')
            ->name('freebies-index');
    });

Route::controller(OnlineResourcesController::class)
    ->group(function () {
        Route::get('/online-resources', 'index')
            ->name('online-resources-index');

        Route::get('/online-resources/{typeId}', 'getResourceLinksByType')
            ->name('online-resources-by-type');
    });

Route::controller(ReviewsQuotesController::class)
    ->group(function () {
        Route::get('/reviews-quotes', 'index')
            ->name('reviews-quotes-index');
    });

Route::controller(BiblioController::class)
    ->group(function () {
        Route::get('/biblio', 'index')
            ->name('biblio-index');

        Route::get('/biblio/{typeId}', 'getBiblioByType')
            ->name('biblio-by-type');

        Route::get('/admin/biblio', 'index')
            ->middleware('auth:admin')
            ->name('admin-biblio-types-index');

        Route::get('/admin/biblio/{typeId}', 'getBiblioByType')
            ->middleware('auth:admin')
            ->name('admin-biblio-entries-index');

        Route::post('/admin/biblio', 'save')
            ->middleware('auth:admin')
            ->name('admin-biblio-save');

        Route::delete('/admin/biblio/{id}', 'destroy')
            ->middleware('auth:admin')
            ->name('admin-biblio-delete');
    });


Route::controller(AwardsController::class)
    ->group(function () {
        Route::get('/awards', 'index')
            ->name('awards-index');

        Route::get('/admin/awards', 'adminIndex')
            ->middleware('auth:admin')
            ->name('admin-awards-index');

        Route::put('/admin/awards/{id}', 'update')
            ->middleware('auth:admin')
            ->name('admin-awards-update');

        Route::post('/admin/awards', 'store')
            ->middleware('auth:admin')
            ->name('admin-awards-update');

        Route::delete('/admin/awards/{id}', 'destroy')
            ->middleware('auth:admin')
            ->name('admin-awards-update');
    });


Route::controller(HonorsController::class)
    ->group(function () {
        Route::get('/honors', 'index')
            ->name('honors-index');
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

Route::controller(DesignCreditsController::class)
    ->group(function () {
        Route::get('/design-credits', 'index')
            ->name('design-credits-index');
    });

Route::get('/download/press-kit', function () {
    $storagePath = 'press-kit.zip';
    $downloadName = "author-press-kit-" . now()->format('Y-m-d') . ".zip";

    return Storage::disk('local')
        ->download($storagePath, $downloadName);
});

Route::controller(AuthController::class)
    ->group(function () {
        Route::post('/admin/login', 'login')
            ->name('admin-login');

        Route::post('/admin/logout', 'logout')
            ->middleware('auth:admin')
            ->name('admin-logout');

        Route::post('/admin/validate-token', 'validate')
            ->middleware('auth:admin')
            ->name('admin-validate');
    });
