<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
    protected $guarded = [];

    protected $appends = ['contents', 'raw'];

    // mc-note: this would cast these fields in UTC, but it would require more formatting on the frontend to deal with what it receives

    // protected $casts = [
    //     'start_date' => 'datetime:Y-m-d\TH:i:s.v\Z', // UTC format
    //     'end_date' => 'datetime:Y-m-d\TH:i:s.v\Z',   // UTC format
    //     'start_time' => 'datetime:H:i:s\Z',          // UTC time format
    //     'end_time' => 'datetime:H:i:s\Z',            // UTC time format
    // ];

    protected static function booted()
    {
        static::deleting(function ($model) {
            if ($model->md_file && Storage::disk('local')->exists($model->md_file)) {
                Storage::disk('local')->delete($model->md_file);
            }
        });
    }

    public function getContentsAttribute()
    {
        $markdown = Storage::disk('local')->get($this->md_file);
        return Markdown::convert($markdown)->getContent();
    }

    public function getRawAttribute()
    {
        return Storage::disk('local')->get($this->md_file);
    }

    public function brokenLink() // Singular method name
    {
        return $this->morphOne(BrokenLink::class, 'linkable', 'table_name', 'table_id');
    }
}
