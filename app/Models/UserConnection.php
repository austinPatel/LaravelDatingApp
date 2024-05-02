<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserConnection extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'user_prefercne_id',
        'accepted_at',
        'status',
        'disconnected_from',
        'disconnected_at'
    ];

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
