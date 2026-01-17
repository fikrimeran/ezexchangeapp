<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ExchangeRequest;
use App\Models\User;

class Chat extends Model
{
    use HasFactory;

    public $table = 'chats';
    
    protected $fillable = [
        'exchangerequest_id',
        'from_user_id',
        'to_user_id',
        'chat_message',
    ];

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function exchangerequest()
    {
        return $this->belongsTo(ExchangeRequest::class);
    }
}
