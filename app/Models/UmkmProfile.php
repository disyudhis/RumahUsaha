<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UmkmProfile extends Model
{
    use Sluggable;

     public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'business_name'
            ]
        ];
    }
     const CATEGORIES = [
        'kuliner' => 'Kuliner',
        'fashion' => 'Fashion',
        'jasa' => 'Jasa dan Layanan',
        'kerajinan' => 'Kerajinan dan Seni',
        'kecantikan' => 'Kecantikan dan Perawatan Diri',
        'kesehatan' => 'Kesehatan dan Herbal',
        'pariwisata' => 'Pariwisata dan Kearifan Lokal',
        'pertanian' => 'Komoditas Pertanian dan Peternakan, perkebunan dan perikanan',
        'digital' => 'Otomotif, Produk Digital, dan Elektronik',
        'edukasi' => 'Edukasi dan Pelatihan',
        'lainnya' => 'Lainnya',
    ];
    protected $fillable = ['user_id', 'slug', 'categories', 'kecamatan', 'business_name', 'owner_name', 'address', 'whatsapp', 'asal_komunitas', 'instagram', 'description', 'link_website', 'logo', 'is_active', 'is_approved'];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Accessors
    public function getFormattedAddressAttribute()
    {
        return $this->address ? nl2br(e($this->address)) : null;
    }

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return Storage::url($this->logo);
        }

        return null;
    }

    public function getWhatsappUrlAttribute()
    {
        if ($this->whatsapp) {
            // Remove any non-numeric characters and ensure it starts with country code
            $number = preg_replace('/[^0-9]/', '', $this->whatsapp);

            // If it starts with 0, replace with 62 (Indonesia country code)
            if (substr($number, 0, 1) === '0') {
                $number = '62' . substr($number, 1);
            }

            return "https://wa.me/{$number}";
        }

        return null;
    }

    public function getInstagramUrlAttribute()
    {
        if ($this->instagram) {
            // Remove @ symbol if present
            $username = ltrim($this->instagram, '@');
            return "https://instagram.com/{$username}";
        }

        return null;
    }

    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->business_name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }

        return strtoupper(substr($this->business_name, 0, 2));
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeWithProducts($query)
    {
        return $query->with([
            'products' => function ($query) {
                $query->where('is_active', true)->limit(3);
            },
        ]);
    }

    // Helper Methods
    public function hasLogo()
    {
        return !empty($this->logo) && Storage::exists($this->logo);
    }

    public function hasContact()
    {
        return !empty($this->whatsapp) || !empty($this->instagram);
    }
}
