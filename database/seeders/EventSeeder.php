<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Providers\AppServiceProvider;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $recs = AppServiceProvider::getSeedData('events');

        foreach ($recs as $rec) {
            $rec['startDate'] = $rec['startDate'] == '' ? null : $rec['startDate'];
            $rec['startTime'] = $rec['startTime'] == '' ? null : $rec['startTime'];
            $rec['endDate'] = $rec['endDate'] == '' ? $rec['startDate'] : $rec['endDate'];
            $rec['endTime'] = $rec['endTime'] == '' ? null : $rec['endTime'];

            $event = Event::create([
                'name' => $rec['eName'],
                'schedule' => $rec['schedule'],
                'start_date' => $rec['startDate'],
                'start_time' => $rec['startTime'],
                'end_date' => $rec['endDate'],
                'end_time' => $rec['endTime'],
                'url' => $rec['url'],
                'tz' => $rec['tz'],
                'md_file' => ''
            ]);

            $mdFile = 'events/' . $event->id . '.md';

            Storage::disk('local')->put($mdFile, $rec['details']);

            $event->md_file = $mdFile;
            $event->save();
        }
    }
}
