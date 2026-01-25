<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Category;
use App\Models\Subcategory;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'subcategory_id',
        'item_name',
        'item_description',   
        'item_location',
        'latitude',       
        'longitude',    
        'item_image',
        'is_available',    
    ];

    /* Relationships */

    // User relationship with fallback for deleted user
    public function user()
    {
        return $this->belongsTo(User::class)
            ->withDefault([
                'name' => '[User Deleted]',
            ]);
    }

    // Category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Subcategory
    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }
}
