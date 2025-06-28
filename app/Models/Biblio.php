<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Biblio extends Model
{
    protected $guarded = [];

    public function biblio_pubs(): HasMany
    {
        return $this->hasMany(BiblioPub::class);
    }

    public function biblio_type(): BelongsTo
    {
        return $this->belongsTo(BiblioType::class);
    }
}
