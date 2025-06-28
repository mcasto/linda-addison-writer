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

        $types = collect($recs)->map(fn($rec) => $rec['type'])->unique()->toArray();

        foreach ($types as $type) {
            FindType::create([
                'name' => $type
            ]);
        }
    }
}
