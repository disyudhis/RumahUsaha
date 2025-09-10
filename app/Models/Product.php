<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    const CATEGORIES = [
        'kuliner' => 'Kuliner',
        'jasa' => 'Jasa',
        'digital' => 'Digital',
        'fashion' => 'Fashion',
        'kerajinan' => 'Kerajinan',
        'lainnya' => 'Lainnya',
    ];
    protected $fillable = ['name', 'description', 'price', 'category', 'image', 'umkm_profile_id', 'is_active'];

    /**
     * Get the formatted price.
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', '.');
    }

    /**
     * Get the UMKM profile that owns the product.
     */
    public function umkmProfile()
    {
        return $this->belongsTo(UmkmProfile::class);
    }

    public function getCategoryNameAttribute()
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $term)
    {
        return $query
            ->where('name', 'like', '%' . $term . '%')
            ->orWhere('description', 'like', '%' . $term . '%')
            ->orWhereHas('umkmProfile', function ($q) use ($term) {
                $q->where('business_name', 'like', '%' . $term . '%');
            });
    }
}