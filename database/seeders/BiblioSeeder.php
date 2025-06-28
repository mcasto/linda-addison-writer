<?php

namespace Database\Seeders;

use App\Models\Biblio;
use App\Models\BiblioType;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BiblioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recs = AppServiceProvider::getSeedData('biblio');

        foreach ($recs as $rec) {
            $type = BiblioType::where('type', $rec['type'])
                ->first();

            Biblio::create([
                'id' => $rec['id'],
                'biblio_type_id' => $type->id,
                'title' => $rec['title'],
                'sort_date' => $rec['sortDate']
            ]);
        }
    }
}
