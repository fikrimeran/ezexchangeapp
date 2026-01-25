<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\User;

class ExchangeRequest extends Model
{
    use HasFactory;

    public $table = 'exchangerequests';
    
    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'from_item_id',
        'to_item_id',
        'status',
    ];

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id')
            ->withDefault([
                'name' => '[User Deleted]',
            ]);
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id')
            ->withDefault([
                'name' => '[User Deleted]',
            ]);
    }

    public function fromItem()
    {
        return $this->belongsTo(Item::class, 'from_item_id')
            ->withDefault([
                'item_name' => '[Item Removed]',
            ]);
    }

    public function toItem()
    {
        return $this->belongsTo(Item::class, 'to_item_id')
            ->withDefault([
                'item_name' => '[Item Removed]',
            ]);
    }
}
