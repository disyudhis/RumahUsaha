<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use Sluggable;

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
        ];
    }

    const CATEGORIES = [
        'kolaborasi-sosial' => 'Kolaborasi Sosial & Pemberdayaan UMKM',
        'riset-inovasi' => 'Riset & Inovasi untuk Solusi UMKM',
        'pengembangan-kapasitas' => 'Pengembangan Kapasitas & Keterampilan UMKM',
        'kemitraan-strategis' => 'Kemitraan Strategis & Jejaring Kolaborasi',
        'info-kampus' => 'Info Kampus & Kolaborasi UMKM',
    ];

    protected $fillable = ['title', 'categories', 'slug', 'link_url', 'description', 'event_date', 'image'];

    protected $casts = [
        'event_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the formatted event date.
     */
    public function getFormattedEventDateAttribute()
    {
        return $this->event_date ? $this->event_date->format('d M Y H:i') : null;
    }
}
