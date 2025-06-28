<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OnlineResource extends Model
{
    protected $guarded = [];

    public function online_resource_links(): HasMany
    {
        return $this->hasMany(OnlineResourceLink::class);
    }
}
