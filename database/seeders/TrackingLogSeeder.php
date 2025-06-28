<?php

namespace Database\Seeders;

use App\Models\TrackingLog;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TrackingLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recs = AppServiceProvider::getSeedData('trackingLog');

        foreach ($recs as $rec) {

            if (!is_null($rec['timestamp']) && !is_null($rec['originalURI'])) {
                TrackingLog::create([
                    'remote_addr' => $rec['remoteAddr'],
                    'original_uri' => $rec['originalURI'],
                    'created_at' => substr($rec['timestamp']['date'], 0, 19)
                ]);
            }
        }
    }
}
