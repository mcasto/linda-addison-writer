<?php

namespace Database\Seeders;

use App\Models\LatestNews;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class LatestNewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recs = AppServiceProvider::getSeedData('latestNews');

        foreach ($recs as $rec) {
            $news = LatestNews::create([
                'date' => $rec['date'],
                'md_file' => ''
            ]);

            $mdFile = 'latest-news/' . $news->id . '.md';

            $news->md_file = $mdFile;
            $news->save();

            Storage::disk('local')->put($mdFile, $rec['news']);
        }
    }
}
