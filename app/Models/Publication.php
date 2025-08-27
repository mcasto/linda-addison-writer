<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Publication extends Model
{
    protected $guarded = [];

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

    public function publication_type(): BelongsTo
    {
        return $this->belongsTo(PublicationType::class);
    }

    public function brokenLink() // Singular method name
    {
        return $this->morphOne(BrokenLink::class, 'linkable', 'table_name', 'table_id');
    }
}
