<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ExchangeRequest;
use App\Models\User;

class Notification extends Model
{
    use HasFactory;

    public $table = 'notifications';
    
    protected $fillable = [
        'user_id',
        'exchangerequest_id',
        'notification_type',
        'notification_content',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exchangeRequest()
    {
        return $this->belongsTo(ExchangeRequest::class);
    }
}
