<?php

namespace Database\Seeders;

use App\Models\BiblioPub;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BiblioPubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recs = AppServiceProvider::getSeedData('biblioPubs');

        foreach ($recs as $rec) {
            BiblioPub::create(
                [
                    'id' => $rec['id'],
                    'biblio_id' => $rec['biblioID'],
                    'pub_date' => $rec['pubDate'],
                    'display_date' => $rec['dispDate'],
                    'publication' => $rec['publication']
                ]
            );
        }
    }
}
