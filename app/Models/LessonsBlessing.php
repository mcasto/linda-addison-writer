<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LessonsBlessing extends Model
{
    protected $guarded = [];

    protected $appends = ['header', 'poem'];

    public function getHeaderAttribute()
    {
        return Storage::disk('local')->get($this->header_file);
    }

    public function getPoemAttribute()
    {
        $poem = Storage::disk('local')->get($this->poem_file);
        return Markdown::convert($poem)->getContent();
    }
}
