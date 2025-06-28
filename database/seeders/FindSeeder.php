<?php

namespace Database\Seeders;

use App\Models\Find;
use App\Models\FindType;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class FindSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recs = AppServiceProvider::getSeedData('seeHearRead');

        foreach ($recs as $row) {
            $type = FindType::where('name', $row['type'])->first();
            $rec = Find::create([
                'title' => $row['text'],
                'url' => $row['url'],
                'date' => $row['date'],
                'find_type_id' => $type->id,
                'md_file' => ''
            ]);

            $mdFile = 'finds/' . $rec->id . '.md';

            Storage::disk('local')->put($mdFile, $row['note']);

            $rec->md_file = $mdFile;
            $rec->save();
        }
    }
}
