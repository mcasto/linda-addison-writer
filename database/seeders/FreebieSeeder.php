<?php

namespace Database\Seeders;

use App\Models\Freebie;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class FreebieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recs = AppServiceProvider::getSeedData('freebies');

        foreach ($recs as $rec) {
            $freebie =  Freebie::create([
                'title' => $rec['title'],
                'note' => $rec['note'],
                'sub' => $rec['sub'],
                'start_date' => $rec['startDate'],
                'end_date' => $rec['endDate'],
                'md_file' => ''
            ]);

            $mdFile = 'freebies/' . $freebie->id . '.md';

            $freebie->md_file = $mdFile;
            $freebie->save();

            Storage::disk('local')->put($mdFile, $rec['story']);
        }
    }
}
