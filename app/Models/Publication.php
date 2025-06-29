<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Publication extends Model
{
    protected $guarded = [];

    protected $appends = ['contents'];

    public function getContentsAttribute()
    {
        $markdown = Storage::disk('local')->get($this->md_file);
        return Markdown::convert($markdown)->getContent();
    }

    public function publication_type(): BelongsTo
    {
        return $this->belongsTo(PublicationType::class);
    }
}
