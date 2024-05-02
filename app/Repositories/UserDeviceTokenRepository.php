<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Carbon;
use App\Models\UserDeviceToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UserDeviceTokenRepository
{
    public function create(array $data)
    {
        $userDeviceToken = UserDeviceToken::create($data);
        return $userDeviceToken;
    }
    public function updateUserDeviceToken(array $data)
    {
        $userId = Auth::user()->id;
        $user_device_token_data = array(
            "device_token" => $data['pushToken'],
            "device_type" => $data['deviceType'],
            "user_id" => $userId
        );

        if (empty($data['oldPushToken']) || $data['oldPushToken'] == null) {
            $existUserDeviceToken = $this->checkduplicateDeviceToken($data['pushToken']);
            if ($existUserDeviceToken->exists()) {
                $userDevicetoken = $existUserDeviceToken->update($user_device_token_data);
                $response['update'] = true;
                $response['insert'] = false;
            } else {
                $userDevicetoken = $this->create($user_device_token_data);
                $response['insert'] = true;
                $response['update'] = false;
            }
            $response['success'] = true;
        } else {
            // $query = UserDeviceToken::select();
            $userDevicetoken = UserDeviceToken::where('device_token', $data['oldPushToken'])->update($user_device_token_data);
            $response['update'] = true;
            $response['insert'] = false;
            $response['success'] = true;
        }
        return $response;
    }
    public function getUserDeviceTokens($userId = null)
    {
        $userDeviceToken = UserDeviceToken::select(['device_token', 'device_type'])->where('user_id', $userId)->whereNotNull('device_token')->distinct('device_token')->get();
        return $userDeviceToken;
    }
    public function checkduplicateDeviceToken($token = null)
    {
        $userDevicetoken = UserDeviceToken::where('device_token', $token);
        return $userDevicetoken;
    }
}
