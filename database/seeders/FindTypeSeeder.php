<?php

namespace Database\Seeders;

use App\Models\FindType;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FindTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recs = AppServiceProvider::getSeedData('seeHearRead');

        $types = ['see', 'hear', 'read'];

        foreach ($types as $key => $type) {
            FindType::create([
                'name' => $type,
                'sort_order' => $key + 1
            ]);
        }
    }
}
