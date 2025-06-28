<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BiblioPub extends Model
{
    protected $guarded = [];

    public function biblio(): BelongsTo
    {
        return $this->belongsTo(Biblio::class);
    }
}
