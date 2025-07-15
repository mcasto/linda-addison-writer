<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LessonsBlessing extends Model
{
    protected $guarded = [];

    protected $appends = ['header', 'poem'];

    protected static function booted()
    {
        static::deleting(function ($model) {
            if ($model->md_file && Storage::disk('local')->exists($model->header_file)) {
                Storage::disk('local')->delete($model->header_file);
            }

            if ($model->md_file && Storage::disk('local')->exists($model->poem_file)) {
                Storage::disk('local')->delete($model->poem_file);
            }
        });
    }

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
