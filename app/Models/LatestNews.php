<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LatestNews extends Model
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
        try {
            $markdown = Storage::disk('local')->get($this->md_file);
            return Markdown::convert($markdown)->getContent();
        } catch (\Exception $e) {
            return '';
        }
    }

    public function getRawAttribute()
    {
        try {
            return Storage::disk('local')->get($this->md_file);
        } catch (\Exception $e) {
            return '';
        }
    }
}
