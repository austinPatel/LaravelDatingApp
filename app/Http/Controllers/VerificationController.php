<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerificationController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        // $this->middleware('auth');
        // $this->middleware('signed')->only('verify');
        // $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    public function verifyEmail(Request $request)
    {
        $current_time = strtotime(date("Y-m-d H:i:s"));
        $hasValidSig = $request->expires >= $current_time ? true : false;

        $user = $this->userRepository->getUserById($request->id);

        if ($hasValidSig && !$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($request->user()));
        }
        $verified = $user->hasVerifiedEmail();
        $roles = $user->roles->pluck('name')->toArray();

        if ($user && !empty($roles) && in_array(User::ROLE_SUPER_ADMIN, $roles)) {
            if ($verified) {
                $msg = 'Email is verified, try to login';
                return redirect('login')->with('success', $msg);
            } else {
                return view("mails.verifyEmail", compact(['user', 'verified', 'hasValidSig']));
            }
        }

        return view("mails.verifyEmail", compact(['user', 'verified', 'hasValidSig']));

        // return redirect($this->redirectPath())->with('verified', true);
        // return redirect()->away('app://open'); // The deep link
        // return $this->sendResponse($user->email, "User email is verified.Please open mobile app and login");
    }
    public function resend(Request $request)
    {
        $user = $this->userRepository->getUserById($request->id);

        if ($user->hasVerifiedEmail()) {
            return redirect()->back()->with('verifyMessage', "Email already verified.");
        }

        $user->sendEmailVerificationNotification();

        return redirect()->back()->with('verifyMessage', "A fresh verification link has been sent to your email address.");
    }
}
