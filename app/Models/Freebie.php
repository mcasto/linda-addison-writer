<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Freebie extends Model
{
    protected $guarded = [];

    protected $appends = ['contents'];

    public function getContentsAttribute()
    {
        return Storage::disk('local')->get($this->md_file);
    }

    public static function today()
    {
        $freebie =  self::whereDate('start_date', '<=', Carbon::now())
            ->whereDate('end_date', '>=', Carbon::now())
            ->orderBy('start_date')
            ->first();

        // if freebie not found, randomize their order again & reset their start/end dates
        if (!$freebie) {
            $startDate = Carbon::now();
            $endDate = Carbon::now()->addDays(6);

            $list = self::inRandomOrder()->get();
            foreach ($list as $rec) {
                $rec->start_date = $startDate;
                $rec->end_date = $endDate;
                $rec->save();

                if ($startDate == Carbon::now()) {
                    $freebie = $rec;
                }

                $startDate->addDays(7);
                $endDate->addDays(7);
            }
        }

        return $freebie;
    }
}
