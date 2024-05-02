<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class UserSubscription extends Model
{
    use HasFactory;
    use SoftDeletes;

    const TRAIL_STATUS = 1;
    const IN_ACTIVE_STATUS = 2;
    const ACTIVE_STATUS = 3;

    const USER_SUBSCRIPTION_STATUS = [
        self::TRAIL_STATUS => 'Trial',
        self::IN_ACTIVE_STATUS => 'In-Active',
        self::ACTIVE_STATUS => 'Active',
    ];

    const PENDING_STATUS = 1;
    const PAID_STATUS = 2;

    const USER_PAYMENT_STATUS = [
        self::PENDING_STATUS => 'Pending',
        self::PAID_STATUS => 'Paid',
    ];


    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'plan_manager_name',
        'plan_manager_email',
        'send_invoice',
        'subscription_status',
        'subscription_date',
        'payment_status',
        'payment_date',
        'expire_at'
    ];

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }
    // public function setPaymentDateAttribute($date){
    //     $this->attributes['payment_date'] = empty($date) ? null : Carbon::parse($date)->format('m/d/Y');

    // }
}
