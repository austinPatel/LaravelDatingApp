<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FCMPushNotificationService
{
    public static function send($notification)
    {
        $http = Http::withHeaders([
            'Authorization' => "key=" . config('firebase.token'),
            'Content-Type' => 'application/json'
        ])->post(config('firebase.fcm_url'), $notification);
        return $http;
    }
}
