<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PestControlService extends Model
{
    protected $fillable = [
        'province_id',
        'name',
        'address',
        'phone',
        'email',
        'website',
        'specialty',
        'rating',
        'review_count',
        'latitude',
        'longitude',
        'description',
        'services_offered',
        'is_active',
    ];

    protected $casts = [
        'services_offered' => 'array',
        'rating' => 'decimal:1',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByProvince($query, $provinceName)
    {
        return $query->whereHas('province', function ($q) use ($provinceName) {
            $q->where('name', $provinceName);
        });
    }
}
