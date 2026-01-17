<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRule extends Model
{
    protected $fillable = [
        'price_gap_level',
        'same_category',
        'same_subcategory',
        'distance_level',
        'recommendation',
    ];
}

