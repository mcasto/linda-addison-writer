<?php

namespace Database\Seeders;

use App\Models\Award;
use App\Providers\AppServiceProvider;
use Illuminate\Contracts\Cache\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class AwardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = AppServiceProvider::getSeedData('awards');

        foreach ($rows as $row) {
            $rec = Award::create([
                'id' => $row['id'],
                'year' => $row['year'],
                'md_file' => ''
            ]);

            $filename = "awards/" . $rec->id . ".md";
            Storage::disk('local')->put($filename, $row['text']);
            $rec->md_file = $filename;
            $rec->save();
        }
    }
}
