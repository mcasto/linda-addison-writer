<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FindType extends Model
{
    protected $guarded = [];

    public function finds(): HasMany
    {
        return $this->hasMany(Find::class)->orderBy('date', 'desc');
    }
}
