<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionPlan extends Model
{
    use HasFactory;
    use SoftDeletes;

    const MONTHLY_STATUS = 1;
    const HALF_YEARLY_STATUS = 2;
    const YEARLY_STATUS = 3;

    const PLAN_STATUS = [
        self::MONTHLY_STATUS => 'Monthly',
        self::HALF_YEARLY_STATUS => 'Half Yearly',
        self::YEARLY_STATUS => 'Yearly',
    ];

    protected $fillable = [
        'plan_name',
        'plan_type',
        'amount',
        'description',
    ];
}
