<?php

namespace App\Models;

use Hash;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable implements MustVerifyEmail, HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, InteractsWithMedia;
    use SoftDeletes;

    const ROLE_SUPER_ADMIN = 'super-admin';
    const ROLE_APP_USER = 'app-user';

    const TRAIL_STATUS = 1;
    const SUSPENDED_STATUS = 2;
    const BLOCKED_STATUS = 3;
    const ON_HOLD_STATUS = 4;
    const ACTIVE_STATUS = 5;
    const NEW_STATUS =6;

    const USER_STATUS = [
        self::TRAIL_STATUS => 'Trial',
        self::SUSPENDED_STATUS => 'Suspended',
        self::BLOCKED_STATUS => 'Blocked',
        self::ON_HOLD_STATUS => 'On-hold',
        self::ACTIVE_STATUS => 'Active',
        self::NEW_STATUS=>'New'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'birthdate',
        'mobile',
        'email',
        'password',
        'age',
        'ndis_number',
        'email_verified_at',
        'status',
        'term_conditions',
    ];

    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Hash passwords on set.
     *
     * @param $value
     */
    // public function setPasswordAttribute($value)
    // {
    //     if (trim($value) != '') {
    //         $this->attributes['password'] = Hash::make(trim($value));
    //     }
    // }

    public function preference()
    {
        return $this->belongsTo(UserPreference::class, 'id', 'user_id');
    }

    public function userInterest()
    {
        return $this->hasMany(UserInterest::class);
    }

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }

    public function getNameAttribute()
    {
        return ucfirst($this->first_name) . ' ' . ucfirst($this->last_name);
    }

    public function notInterestedUser()
    {
        return $this->hasMany(NotInterestedUser::class);
    }

    public function reportToUsers()
    {
        return $this->hasMany(ReportUser::class);
    }

    public function reportFromUsers()
    {
        return $this->hasMany(ReportUser::class, 'to_user_id');
    }

    public function userSubscription()
    {
        return $this->hasMany(UserSubscription::class)->latest();
    }
    public function userLocation(){
        return $this->belongsTo(UserLocation::class, 'id', 'user_id');
    }
    public function state(){
        return $this->hasOne(State::class);
    }
}
