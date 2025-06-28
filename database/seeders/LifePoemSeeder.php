<?php

namespace Database\Seeders;

use App\Models\LifePoem;
use App\Providers\AppServiceProvider;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class LifePoemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recs = AppServiceProvider::getSeedData('lifePoems');

        $poems = collect($recs)->map(fn($rec) => $rec['poem'])->unique()->toArray();
        $poemDate = Carbon::yesterday();

        foreach ($poems as $poemText) {
            $poem = LifePoem::create([
                'date' => $poemDate,
                'md_file' => ''
            ]);

            $mdFile = 'life-poems/' . $poem->id . '.md';

            $poem->md_file = $mdFile;
            $poem->save();

            Storage::disk('local')->put($mdFile, $poemText);

            $poemDate->addDay();
        }
    }
}
