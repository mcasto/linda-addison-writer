<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Honor extends Model
{
    protected $guarded = [];

    protected $appends = ['contents', 'raw'];

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
}
