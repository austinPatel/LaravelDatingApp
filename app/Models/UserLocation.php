<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
    use HasFactory;
    protected $fillable = [
        'state_id',
        'suburb_id',
        'user_id'
    ];
    public function userLocationState(){
        return $this->belongsTo(State::class);
    }
    public function userLocationSuburb(){
        return $this->belongsTo(Locations::class);
    }
    public function userState(){
        return $this->hasOne(State::class,'id','state_id');
    }
    public function userSuburb(){
        return $this->hasOne(Locations::class,'id','suburb_id');
    }
}
