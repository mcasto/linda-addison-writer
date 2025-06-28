<?php

namespace Database\Seeders;

use App\Models\LessonsBlessing;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class LessonsBlessingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recs = AppServiceProvider::getSeedData('lessonsBlessings');

        foreach ($recs as $rec) {
            $lb = LessonsBlessing::create([
                'header_file' => '',
                'poem_file' => ''
            ]);

            $headerFile = 'lessons-blessings/' . $lb->id . '-header.md';
            $poemFile = 'lessons-blessings/' . $lb->id . '-poem.md';

            $lb->header_file = $headerFile;
            $lb->poem_file = $poemFile;
            $lb->save();

            Storage::disk('local')->put($headerFile, $rec['header']);
            Storage::disk('local')->put($poemFile, $rec['poem']);
        }
    }
}
