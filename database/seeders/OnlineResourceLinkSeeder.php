<?php

namespace Database\Seeders;

use App\Models\OnlineResourceLink;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OnlineResourceLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recs = AppServiceProvider::getSeedData('onlineResourceLinks');

        foreach ($recs as $rec) {
            OnlineResourceLink::create([
                'online_resource_id' => $rec['orID'],
                'name' => $rec['text'],
                'url' => $rec['url']
            ]);
        }
    }
}
