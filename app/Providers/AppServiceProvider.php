<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    public static function getSeedData($table)
    {
        $raw = file_get_contents(__DIR__ . '/seed-data.json');
        $decoded = json_decode($raw, true);
        return $decoded[$table];
    }
}
