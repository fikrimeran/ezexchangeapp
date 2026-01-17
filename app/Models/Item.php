<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    public function user()     { return $this->belongsTo(User::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function subcategory()
{
    return $this->belongsTo(Subcategory::class);
}

}
