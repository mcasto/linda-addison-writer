<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrokenLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_name',
        'table_id',
        'confirmed_broken',
        'confirmed_working',
        'confirmed_date'
    ];

    protected $casts = [
        'confirmed_broken' => 'boolean',
        'confirmed_working' => 'boolean',
        'confirmed_date' => 'date'
    ];

    /**
     * Get the parent model (event, cover, find, etc.)
     */
    public function linkable()
    {
        return $this->morphTo(__FUNCTION__, 'table_name', 'table_id');
    }
}
