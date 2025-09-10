<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['product_id', 'customer_name', 'comment', 'rating'];

    protected $casts = [
        'product_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    /**
     * Get the UMKM profile that owns the testimonial.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}