<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'title',
        'description',
        'event_date',
        'image',
    ];

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