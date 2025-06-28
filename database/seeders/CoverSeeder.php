<?php

namespace Database\Seeders;

use App\Models\Cover;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class CoverSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recs = AppServiceProvider::getSeedData('coverList');

        foreach ($recs as $rec) {
            $cover = Cover::create([
                'title' => $rec['title'],
                'purchase_url' => $rec['purchaseURL'],
                'image' => "covers/" . $rec['image'] . ".webp",
                'sort_order' => $rec['sortOrder'],
                'md_file' => ''
            ]);

            $mdFile = 'covers/' . $cover->id . '.md';

            $cover->md_file = $mdFile;
            $cover->save();

            $md = $rec['description'];


            Storage::disk('local')->put($mdFile, $md);
            Storage::disk('public')->put('covers/' . $cover->id . '.md', $md);
        }
    }
}
