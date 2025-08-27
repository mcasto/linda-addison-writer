<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Social extends Model
{
    protected $guarded = [];

    public function brokenLink() // Singular method name
    {
        return $this->morphOne(BrokenLink::class, 'linkable', 'table_name', 'table_id');
    }
}
