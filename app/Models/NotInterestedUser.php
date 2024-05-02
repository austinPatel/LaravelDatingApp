<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotInterestedUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recommendation_user_id'
    ];
}
