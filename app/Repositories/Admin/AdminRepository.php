<?php

namespace App\Repositories\Admin;

use App\Models\{
    User
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminRepository
{
    public function changePassword($request)
    {
        User::find(Auth::user()->id)->update(['password' => Hash::make($request->new_password)]);
        return true;
    }

    public function profileUpdate($request)
    {
        User::find(Auth::user()->id)->update($request);

        if ($request['email'] != Auth::user()->email) {
            User::find(Auth::user()->id)->update(['email_verified_at' => null]);

            $user = User::find(Auth::user()->id);
            $user->sendEmailVerificationNotification();
        }
        return true;
    }
}
