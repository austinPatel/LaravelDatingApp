<?php

use App\Models\User;
use App\Models\UserSubscription;
use Carbon\carbon;

if (!function_exists('pagination')) {
    function pagination($request)
    {
        $page = (isset($request['page']) && $request['page'] >= 1) ? (int)$request['page'] : 1;
        $perPage = (isset($request['perPage']) && $request['perPage'] >= 1) ? (int)$request['perPage'] : 10;
        $offset = ($page - 1) * $perPage;
        $totalPageCount = ceil($request['totalRecord'] / $perPage);

        $response = [
            'page' => $page,
            'perPage' => $perPage,
            'offset' => $offset,
            'totalPageCount' => $totalPageCount,
        ];

        return $response;
    }
}


if (!function_exists('userStatus')) {
    function userStatus($status)
    {
        $userStatus = User::USER_STATUS;
        return $userStatus[$status];
    }
}

if (!function_exists('getUserSubscriptionStatus')) {
    function getUserSubscriptionStatus($status)
    {
        $userSubscriptionStatus = UserSubscription::USER_SUBSCRIPTION_STATUS;
        return $userSubscriptionStatus[$status];
    }
}


if (!function_exists('userPaymentStatus')) {
    function userPaymentStatus($status)
    {
        $userPaymentStatus = UserSubscription::USER_PAYMENT_STATUS;
        return $userPaymentStatus[$status];
    }
}

if (!function_exists('convertToViewDateOnly')) {
    function convertToViewDateOnly($date)
    {
        return Carbon::createFromFormat(config('app.db.datetime.format'), $date)
            ->format(config('app.date.format_view'));
    }
}
