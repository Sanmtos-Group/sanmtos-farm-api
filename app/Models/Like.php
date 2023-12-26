<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    
    protected $fillable = [
        'user_id',
        'likeable_id',
        'likeable_type'
    ];

    /**
     * Get the parent likeable model (shop, product or image).
     */
    public function likeable()
    {
        return $this->morphTo();
    }
}
