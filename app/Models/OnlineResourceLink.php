<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OnlineResourceLink extends Model
{
    protected $guarded = [];

    public function online_resource(): BelongsTo
    {
        return $this->belongsTo(OnlineResource::class);
    }

    public function brokenLink() // Singular method name
    {
        return $this->morphOne(BrokenLink::class, 'linkable', 'table_name', 'table_id');
    }
}
