<?php

namespace Database\Seeders;

use App\Models\OnlineResource;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OnlineResourceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recs = AppServiceProvider::getSeedData('onlineResources');

        foreach ($recs as $key => $rec) {
            OnlineResource::create(
                [
                    'id' => $rec['id'],
                    'header' => $rec['header'],
                    'sort_order' => $key + 1
                ]
            );
        }
    }
}
