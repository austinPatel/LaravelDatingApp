<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class UserRepository
{
    // Use ResponseAPI Trait in this repository


    public function getUserById($id)
    {

        $user = User::findOrFail($id);
        return $user;
    }

    /*
        User Create|Update
    */
    public function requestUser(array $data)
    {
        $years = isset($data['birthdate']) ? Carbon::parse($data['birthdate'])->age : null; // get age from the birthdate
        $data['age'] = $years;
        $data['password'] = Hash::make($data['password']);
        $data['status']=User::NEW_STATUS;
        $user = User::create($data);
        event(new Registered($user));
        return $user;
    }

    public function login(array $data)
    {
        $user = User::where('email', $data['email'])->first();
        if (!$user || !$token = Auth::attempt($data)) {
            return $user;
        }
    }

    public function checkUserByEmail($email)
    {
        $user = User::all()->where('email', $email)->first(); // need to verify email_verify_at not null
        return $user;
    }

    // public function updateVerifyMobile(array $data)
    // {
    //     $user = User::where('mobile', $data['mobile'])->update(['isVerifiyMobile' => true]);

    //     return $user;
    // }

    public function updateUser(array $data)
    {
        $userId = Auth::user()->id;
        $user = User::where('id', $userId)->update($data);
        return $user;
    }

    public function userProfile($userId = null)
    {

        if(empty($userId)){
            $userId = Auth::user()->id;
        }
        $query = User::with('userInterest.interest', 'preference', 'answers.question', 'answers.question.options','userLocation.userLocationState')->where('id', $userId)->first();
        return $query;
    }

    public function isMobileNumberExist(array $data)
    {
        // Check mobile is exist or not
        $user = User::where('mobile', $data['mobile'])->whereNull('deleted_at')->first();
        return !$user ? true : false;
    }
    public function checkUserByEmailOrPhone($email)
    {
        $user = User::where('email', $email)->orWhere('mobile', $email)->first();
        // $user = User::all()->where(function ($query) use ($email) {
        //     $query->where('email', $email)
        //         ->orWhere('mobile', $email);
        // })->first();
        // $user = User::all()->where('email', $email)->orWhere('mobile', $email)->get(); // need to verify email_verify_at not null
        return $user;
    }

    public function getUserSubscriptionDetails($id)
    {
        $user = User::with('userSubscription.subscriptionPlan')->find($id);
        return $user;
    }
}
