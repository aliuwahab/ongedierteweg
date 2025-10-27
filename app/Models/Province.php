<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    protected $fillable = [
        'name',
        'population',
        'latitude',
        'longitude',
        'geojson_data',
    ];

    protected $casts = [
        'geojson_data' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    public function pestControlServices(): HasMany
    {
        return $this->hasMany(PestControlService::class);
    }

    public function activePestControlServices(): HasMany
    {
        return $this->pestControlServices()->where('is_active', true);
    }

    public function towns(): HasMany
    {
        return $this->hasMany(Town::class);
    }
}
