<?php

namespace Database\Seeders;

use App\Models\Social;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SocialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recs = AppServiceProvider::getSeedData('social');

        foreach ($recs as $rec) {
            Social::create([
                'icon' => $rec['icon'],
                'url' => $rec['url'],
                'sort_order' => $rec['sortOrder']
            ]);
        }
    }
}
