<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function redirectPath()
    {
        $token = request()->token;
        $email = request()->email;
        $roles = Auth::user()->roles->pluck('name')->toArray();

        if (Auth::user() && !empty($roles) && in_array(User::ROLE_SUPER_ADMIN, $roles)) {
            return $this->redirectTo;
        }

        Auth::logout();
        Session::flash('success', 'Password reset successfully!');
        return 'password/reset/' . $token . '?email=' . $email;
    }
}
