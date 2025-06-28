<?php

namespace Database\Seeders;

use App\Models\PublicationType;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PublicationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pubs = AppServiceProvider::getSeedData('publications');
        $types = collect($pubs)->map(fn($pub) => $pub['type'])->unique()->toArray();

        foreach ($types as $type) {
            PublicationType::create([
                'name' => $type
            ]);
        }
    }
}
