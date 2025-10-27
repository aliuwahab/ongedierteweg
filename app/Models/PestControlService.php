<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PestControlService extends Model implements HasMedia
{
    use InteractsWithMedia;
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

    /**
     * Register media collections for pest control service images
     */
    public function registerMediaCollections(): void
    {
        // Main photo - single image used as the primary photo
        $this->addMediaCollection('main_photo')
            ->singleFile() // Only one main photo allowed
            ->registerMediaConversions(function () {
                $this->addMediaConversion('thumb')
                    ->width(300)
                    ->height(300)
                    ->sharpen(10);

                $this->addMediaConversion('preview')
                    ->width(800)
                    ->height(600)
                    ->sharpen(10);
            });

        // Gallery - multiple photos of the service, facilities, team, etc.
        $this->addMediaCollection('gallery')
            ->registerMediaConversions(function () {
                $this->addMediaConversion('thumb')
                    ->width(300)
                    ->height(300)
                    ->sharpen(10);

                $this->addMediaConversion('large')
                    ->width(1200)
                    ->height(900)
                    ->sharpen(10);
            });
    }

    /**
     * Get the main photo URL or a placeholder
     */
    public function getMainPhotoUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('main_photo', 'preview') ?: 'https://via.placeholder.com/800x600?text=No+Photo';
    }

    /**
     * Get the main photo thumbnail URL
     */
    public function getMainPhotoThumbAttribute(): string
    {
        return $this->getFirstMediaUrl('main_photo', 'thumb') ?: 'https://via.placeholder.com/300x300?text=No+Photo';
    }

    /**
     * Get all gallery photos
     */
    public function getGalleryPhotosAttribute()
    {
        return $this->getMedia('gallery');
    }
}
