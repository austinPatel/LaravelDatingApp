<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserSubscription;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class UserSubscriptionRepository
{
    public function saveUserSubscription(array $data)
    {
        $userId = Auth::user()->id;

        $data['user_id'] = $userId;
        $data['subscription_date'] = Carbon::now();
        $userSubscription = UserSubscription::create($data);
        if($userSubscription){
            // update user status as trail
            $user=User::where('id',$userId)->update(['status'=>USER::TRAIL_STATUS]);
        }
        return $userSubscription;
    }
    public function checkUserSubscriptionExist($userId = null)
    {
        $userSubscription = UserSubscription::where('user_id', $userId);
        return $userSubscription->exists() ? true : false;
    }
    public function getUserSubscriptionsDetails()
    {
        $userId = Auth::user()->id;
        $userSubscription = UserSubscription::where('user_id', $userId)->first();
        return $userSubscription;
    }
}
