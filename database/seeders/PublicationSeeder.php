<?php

namespace Database\Seeders;

use App\Models\Publication;
use App\Models\PublicationType;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PublicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recs = AppServiceProvider::getSeedData('publications');

        foreach ($recs as $rec) {
            $type = PublicationType::where('name', $rec['type'])->first();

            $pub = Publication::create([
                'publication_type_id' => $type->id,
                'year' => $rec['year'],
                'title' => $rec['title'],
                'url' => $rec['url'],
                'md_file' => ''
            ]);

            $mdFile = 'publications/' . $pub->id . '.md';

            $pub->md_file = $mdFile;
            $pub->save();

            Storage::disk('local')->put($mdFile, $rec['text']);
        }
    }
}
