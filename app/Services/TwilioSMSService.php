<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserMobileVerifyRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Twilio\Rest\Client;

class TwilioSMSService
{
    public $response;
    public $token;
    public $twilio_sid;
    public $twilio_verify_sid;
    public $twilio_client;
    public $twilio_caller_id;
    public function __construct()
    {
        $this->response = null;
        $this->token = getenv("TWILIO_AUTH_TOKEN");
        $this->twilio_sid = getenv("TWILIO_ACCOUNT_SID");
        $this->twilio_verify_sid = getenv("TWILIO_VERIFY_SID");
        $this->twilio_caller_id = getenv("TWILIO_CALLER_ID");
        $this->twilio_client = new Client($this->twilio_sid, $this->token);
    }
    public function sendOTP(array $data)
    {
        $this->response = $this->twilio_client->verify->v2->services($this->twilio_verify_sid)
            ->verifications
            ->create($data['mobile'], "sms");
        return $this->response;
    }
    public function verify(array $data)
    {
        $options = [
            'code' => $data['otp_code'],
            'to' => $data['mobile']
        ];
        $verification = $this->twilio_client->verify->v2->services($this->twilio_verify_sid)
            ->verificationChecks
            ->create($options);
        return $verification;
        // $this->response['valid'] = $verification->valid;
        // // if ($this->response['valid']) {
        // //     $this->response['user'] = app(UserRepository::class)->updateVerifyMobile($data);
        // //     $this->response['message'] = "Mobile number verified";
        // // } else {
        // //     $this->response['message'] = "Invalid verification code entered!";
        // // }
        // return $this->response;
    }
    public function sendMessage(array $data)
    {
        $string = "0123456789";
        $string_shuffled = str_shuffle($string);
        $password = substr($string_shuffled, 1, 6);
        $options = [
            'from' => $this->twilio_caller_id,
            'body' => "Use Verification code $password for authentication"
        ];
        $createData = [
            'mobile' => $data['mobile'],
            'otp_code' => $password,
            'user_id' => Auth::user()->id,
        ];
        $message = $this->twilio_client->messages->create($data['mobile'], $options);

        if ($message->sid) {
            app(UserMobileVerifyRepository::class)->create($createData);
            return true;
        }
        return false; //$message;
    }
}
