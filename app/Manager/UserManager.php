<?php

namespace App\Manager;

use App\Models\UserDeviceToken;
use App\Repositories\UserDeviceTokenRepository;
use App\Repositories\UserMobileVerifyRepository;
use App\Services\TwilioSMSService;
use App\Repositories\UserRepository;

class UserManager
{
    public $twilioSMSService;
    public $userRepository;
    public $response;
    public $userMobileVerifyRepository;
    public $userDeviceTokenRepository;
    public function __construct()
    {
        $this->response = null;
    }
    public function verify(array $data)
    {
        $this->twilioSMSService = new TwilioSMSService;
        $responseData = $this->twilioSMSService->verify($data);
        $this->response['valid'] = $responseData->valid;
        if ($this->response['valid']) {
            $updateData = array(
                "mobile" => $data['mobile'],
                "isVerifiyMobile" => true
            );
            $this->response['user'] = $this->updateVerifyMobile($updateData);
            $this->response['message'] = "Mobile number verified";
        } else {
            $this->response['message'] = "Invalid verification code entered!";
        }
        return $this->response;
    }
    public function updateVerifyMobile(array $data)
    {
        $this->userRepository = new UserRepository;
        return $this->userRepository->updateUser($data);
    }
    public function verifySMSCode(array $data)
    {
        $this->userMobileVerifyRepository = new UserMobileVerifyRepository;
        $isOTPExpired = $this->userMobileVerifyRepository->isOTPCodeExpired($data);
        if ($isOTPExpired) {
            $this->response['valid'] = false;
            $this->response['message'] = "OTP code is expired";
            $removeExpireOtp = $this->userMobileVerifyRepository->removeExpireOTP($data);
            return $this->response;
        }
        $userMobileVerify = $this->userMobileVerifyRepository->verifySMSCode($data);
        if (!$userMobileVerify) {
            $this->response['valid'] = false;
            $this->response['message'] = "Invalid verification code entered!";
        } else {
            $this->response['valid'] = true;
            $updateData = array(
                "mobile" => $data['mobile'],
                "isVerifiyMobile" => true
            );
            $this->response['user'] = $this->updateVerifyMobile($updateData);
            $this->response['message'] = "Mobile number verified";
        }
        return $this->response;
    }
    public function updateUserDeviceToken(array $data)
    {
        $this->userDeviceTokenRepository = new UserDeviceTokenRepository;
        $userDeviceToken = $this->userDeviceTokenRepository->updateUserDeviceToken($data);
        return $userDeviceToken;
    }
}
