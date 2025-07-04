<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BiblioType extends Model
{
    protected $guarded = [];

    public function biblios(): HasMany
    {
        return $this->hasMany(Biblio::class)->orderBy('sort_date', 'desc');
    }
}
