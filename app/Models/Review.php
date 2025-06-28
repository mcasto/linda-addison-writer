<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Review extends Model
{
    protected $guarded = [];

    protected $appends = ['contents'];

    public function getContentsAttribute()
    {
        return Storage::disk('local')->get($this->md_file);
    }
}
