<?php

namespace Database\Seeders;

use App\Models\Honor;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class HonorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recs = AppServiceProvider::getSeedData('honors');

        foreach ($recs as $rec) {
            $honor = Honor::create([
                'year' => $rec['year'],
                'num' => $rec['num'],
                'md_file' => ''
            ]);

            $mdFile = 'honors/' . $honor->id . '.md';

            $honor->md_file = $mdFile;
            $honor->save();

            if (!Storage::disk('local')->exists($mdFile)) {
                Storage::disk('local')->put($mdFile, $rec['text']);
            }
        }
    }
}
