<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Cover extends Model
{
    protected $guarded = [];

    protected $appends = ['contents', 'image_url', 'raw'];

    public function getContentsAttribute()
    {
        $markdown = Storage::disk('local')->get($this->md_file);
        return Markdown::convert($markdown)->getContent();
    }

    public function getRawAttribute()
    {
        return Storage::disk('local')->get($this->md_file);
    }

    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image);
    }
}
