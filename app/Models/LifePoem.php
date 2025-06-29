<?php

namespace App\Models;

use Carbon\Carbon;
use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LifePoem extends Model
{
    protected $guarded = [];

    protected $appends = ['contents'];

    public function getContentsAttribute()
    {
        $markdown = Storage::disk('local')->get($this->md_file);
        return Markdown::convert($markdown)->getContent();
    }

    public static function today()
    {
        $date = Carbon::now()->toString();

        $poem = self::whereDate('date', Carbon::now())->first();

        if (!$poem) {
            $list = self::inRandomOrder()->get();
            $date = Carbon::now();

            foreach ($list as $rec) {
                $rec->date = $date;
                $rec->save();

                if ($rec->date == Carbon::now()) {
                    logger()->debug('here');
                    $poem = $rec;
                }

                $date->addDay();
            }
        }

        $poem = self::whereDate('date', Carbon::now())->first();

        return $poem;
    }
}
