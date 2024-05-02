<?php

namespace App\Repositories;

use Illuminate\Support\Carbon;
use App\Models\UserMobileVerify;

class UserMobileVerifyRepository
{
    public function create(array $data)
    {
        $userMobileVerify = UserMobileVerify::create($data);
        return $userMobileVerify;
    }
    public function verifySMSCode(array $data)
    {
        $userMobileVerify = UserMobileVerify::where('mobile', $data['mobile'])->orderBy('id', 'desc')->first();
        return $userMobileVerify->otp_code == $data['otp_code'] ? true : false;
    }
    public function isOTPCodeExpired(array $data)
    {
        $userVerify = UserMobileVerify::where('mobile', $data['mobile'])->orderBy('id', 'desc')->first();
        return $userVerify->created_at->diffInSeconds(Carbon::now()) > 300 ? true : false;
    }
    public function removeExpireOTP(array $data)
    {
        $usermobile = UserMobileVerify::where('mobile', $data['mobile'])->orderBy('id', 'desc')->first()->delete();
        return $usermobile;
    }
}
