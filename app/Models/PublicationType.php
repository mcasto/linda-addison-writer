<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PublicationType extends Model
{
    protected $guarded = [];

    public function publications(): HasMany
    {
        return $this->hasMany(Publication::class);
    }
}
