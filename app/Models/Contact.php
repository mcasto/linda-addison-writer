<?php

namespace App\Models;

use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $guarded = [];
    protected $appends = ['mdMessage'];

    public function getMdMessageAttribute()
    {
        $markdown = $this->message;
        return Markdown::convert($markdown)->getContent();
    }
}
