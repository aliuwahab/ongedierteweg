<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Town extends Model
{
    protected $fillable = [
        'name',
        'province_id',
        'population',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }
}
