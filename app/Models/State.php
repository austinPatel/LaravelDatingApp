<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'name',
        'code'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function userLocationState(){
        return $this->belongsTo(UserLocation::class,'state_id');
    }
}
