<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Find extends Model
{
    protected $guarded = [];

    protected $appends = ['contents'];

    public function getContentsAttribute()
    {
        return Storage::disk('local')->get($this->md_file);
    }

    public function find_type(): BelongsTo
    {
        return $this->belongsTo(FindType::class);
    }
}
