<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPreference extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'mode',
        'min_age',
        'max_age',
        'distance',
        'current_lat',
        'current_long',
        'interested_in',
        'show_more_people'
    ];
}
