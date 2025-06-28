<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Publication extends Model
{
    protected $guarded = [];

    public function publication_type(): BelongsTo
    {
        return $this->belongsTo(PublicationType::class);
    }
}
