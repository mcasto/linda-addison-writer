<?php

namespace Database\Seeders;

use App\Models\BiblioType;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BiblioTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recs = AppServiceProvider::getSeedData('biblioTypes');

        foreach ($recs as $rec) {
            BiblioType::create([
                'id' => $rec['id'],
                'type' => $rec['type'],
                'order' => $rec['order']
            ]);
        }
    }
}
