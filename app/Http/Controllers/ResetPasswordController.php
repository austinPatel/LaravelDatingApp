<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use App\Http\Requests\ResetPasswordRequest;

class ResetPasswordController extends Controller
{
    //
    public function showResetForm(Request $request)
    {
        $data = [
            "token" => $request->token,
            "email" => $request->email
        ];
        return view("mails.reset", compact('data'));
    }
    public function reset(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill(
                    ['password' => Hash::make($password)]
                )->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->back()->with('success', 'Password has been successfully reset')
            : back()->withErrors(['email' => [__($status)]]);
    }
}
