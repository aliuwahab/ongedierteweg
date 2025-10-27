<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name',
        'iso_code',
        'iso3_code',
        'phone_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function provinces(): HasMany
    {
        return $this->hasMany(Province::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
