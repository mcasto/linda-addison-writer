<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recs = AppServiceProvider::getSeedData('reviewsQuotes');

        foreach ($recs as $key => $rec) {
            $review = Review::create([
                'md_file' => '',
                'sort_order' => $key + 1
            ]);

            $mdFile = 'reviews/' . $review->id . '.md';

            $review->md_file = $mdFile;
            $review->save();

            $md = $rec['text'] . "\n\n&mdash; " . $rec['attribution'];

            Storage::disk('local')->put($mdFile, $md);
        }
    }
}
