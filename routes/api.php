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
            ->name('admin-cover-destroy');

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

        Route::get('/admin/publications', 'adminIndex')
            ->middleware('auth:admin')
            ->name('admin-publications-index');

        Route::post('/admin/publications', 'store')
            ->middleware('auth:admin')
            ->name('admin-publications-store');

        Route::put('/admin/publications/{id}', 'update')
            ->middleware('auth:admin')
            ->name('admin-publications-update');

        Route::delete('/admin/publications/{id}', 'destroy')
            ->middleware('auth:admin')
            ->name('admin-publications-destroy');

        Route::post('/admin/publications/create-type', 'adminCreateType')
            ->middleware('auth:admin')
            ->name('admin-publications-create-type');

        Route::put('/admin/publications/update-type/{id}', 'adminUpdateType')
            ->middleware('auth:admin')
            ->name('admin-publications-update-type');

        Route::delete('/admin/publications/destroy-type/{id}', 'adminDestroyType')
            ->middleware('auth:admin')
            ->name('admin-publications-destroy-type');
    });

Route::controller(NewsController::class)
    ->group(function () {
        Route::get('/news', 'index')
            ->name('news-index');

        Route::get('/admin/news', 'index')
            ->middleware('auth:admin')
            ->name('admin-news-index');

        Route::put('/admin/news/{id}', 'update')
            ->middleware('auth:admin')
            ->name('admin-news-update');

        Route::post('/admin/news/{id}', 'store')
            ->middleware('auth:admin')
            ->name('admin-news-store');

        Route::delete('/admin/news/{id}', 'destroy')
            ->middleware('auth:admin')
            ->name('admin-news-destroy');
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

        Route::get('/admin/finds', 'adminIndex')
            ->middleware('auth:admin')
            ->name('admin-finds-index');

        Route::put('/admin/finds/{id}', 'update')
            ->middleware('auth:admin')
            ->name('admin-finds-update');

        Route::post('/admin/finds/{id}', 'store')
            ->middleware('auth:admin')
            ->name('admin-finds-store');


        Route::delete('/admin/finds/{id}', 'destroy')
            ->middleware('auth:admin')
            ->name('admin-finds-destroy');
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
            ->name('admin-contact-destroy');
    });

Route::controller(LessonsBlessingsController::class)
    ->group(function () {
        Route::get('/lessons-and-blessings', 'index')
            ->name('lessons-and-blessings-index');
    });

Route::controller(FreebiesController::class)
    ->group(function () {
        Route::get('/freebies', 'show')
            ->name('freebies-show');

        Route::get('/admin/freebies', 'index')
            ->middleware('auth:admin')
            ->name('admin-freebies-index');

        Route::post('/admin/freebies/{id}', 'store')
            ->middleware('auth:admin')
            ->name('admin-freebies-store');

        Route::put('/admin/freebies/{id}', 'update')
            ->middleware('auth:admin')
            ->name('admin-freebies-update');

        Route::delete('/admin/freebies/{id}', 'destroy')
            ->middleware('auth:admin')
            ->name('admin-freebies-destroy');
    });

Route::controller(OnlineResourcesController::class)
    ->group(function () {
        Route::get('/online-resources', 'index')
            ->name('online-resources-index');

        Route::get('/online-resources/{typeId}', 'getResourceLinksByType')
            ->name('online-resources-by-type');

        Route::get('/admin/online-resources', 'adminIndex')
            ->middleware('auth:admin')
            ->name('admin-online-resources-index');

        Route::post('/admin/online-resources', 'store')
            ->middleware('auth:admin')
            ->name('admin-online-resources-store');

        Route::put('/admin/online-resources', 'update')
            ->middleware('auth:admin')
            ->name('admin-online-resources-update');

        Route::delete('/admin/online-resources', 'destroy')
            ->middleware('auth:admin')
            ->name('admin-online-resources-destroy');
    });

Route::controller(ReviewsQuotesController::class)
    ->group(function () {
        Route::get('/reviews-quotes', 'index')
            ->name('reviews-quotes-index');

        Route::put('/admin/reviews-quotes', 'update')
            ->middleware('auth:admin')
            ->name('reviews-quotes-admin-update');

        Route::post('/admin/reviews-quotes', 'store')
            ->middleware('auth:admin')
            ->name('review-quotes-admin-store');
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
            ->name('admin-biblio-destroy');
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
            ->name('admin-awards-store');

        Route::delete('/admin/awards/{id}', 'destroy')
            ->middleware('auth:admin')
            ->name('admin-awards-destroy');
    });


Route::controller(HonorsController::class)
    ->group(function () {
        Route::get('/honors', 'index')
            ->name('honors-index');

        Route::get('/admin/honors', 'index')
            ->middleware('auth:admin')
            ->name('admin-honors-index');

        Route::put('/admin/honors/{id}', 'update')
            ->middleware('auth:admin')
            ->name('admin-honors-update');

        Route::post('/admin/honors/{id}', 'store')
            ->middleware('auth:admin')
            ->name('admin-honors-store');

        Route::delete('/admin/honors/{id}', 'destroy')
            ->middleware('auth:admin')
            ->name('admin-honors-destroy');
    });


Route::controller(LifePoemsController::class)
    ->group(function () {
        Route::get('/life-poem', 'show')
            ->name('life-poem-show');

        Route::get('/admin/life-poems', 'index')
            ->middleware('auth:admin')
            ->name('admin-life-poems-index');

        Route::put('/admin/life-poems/{id}', 'update')
            ->middleware('auth:admin')
            ->name('admin-life-poems-update');

        Route::post('/admin/life-poems/{id}', 'store')
            ->middleware('auth:admin')
            ->name('admin-life-poems-store');

        Route::delete('/admin/life-poems/{id}', 'destroy')
            ->middleware('auth:admin')
            ->name('admin-life-poems-destroy');
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

    $filePath = Storage::disk('local')->path($storagePath);
    return response()->download($filePath, $downloadName);
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
