<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AwardSeeder::class,
            BiblioTypeSeeder::class,
            BiblioSeeder::class,
            BiblioPubSeeder::class,
            ContactSeeder::class,
            CoverSeeder::class,
            EventSeeder::class,
            FindTypeSeeder::class,
            FindSeeder::class,
            FreebieSeeder::class,
            HonorSeeder::class,
            LatestNewsSeeder::class,
            LessonsBlessingSeeder::class,
            LifePoemSeeder::class,
            OnlineResourceSeeder::class,
            OnlineResourceLinkSeeder::class,
            PublicationTypeSeeder::class,
            PublicationSeeder::class,
            ReviewSeeder::class,
            SocialSeeder::class,
            TrackingLogSeeder::class,
            UserSeeder::class
        ]);
    }
}
