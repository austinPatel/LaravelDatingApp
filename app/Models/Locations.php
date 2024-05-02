<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locations extends Model
{
    use HasFactory;
    protected $fillable = [
        'postcode',
        'suburb_name',
        'state_id',
        'latitude',
        'longitude',
        'accuracy'
    ];
    public function locationState()
    {
        return $this->belongsTo(State::class);
    }
    public function userLocationSuburb(){
        return $this->belongsTo(UserLocation::class,'user_id');
    }

}
