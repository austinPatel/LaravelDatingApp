<?php

namespace App\Repositories;

use App\Models\{
    UserPreference
};
use Illuminate\Support\Facades\Auth;

class PreferenceRepository
{
    public function userPreferences()
    {
        $user_id = Auth::user()->id;
        $query = UserPreference::where('user_id', $user_id)->first();
        return $query;
    }

    public function saveCliqMode($request)
    {
        $request['user_id'] = Auth::user()->id;
        $check = UserPreference::where('user_id', $request['user_id'])->first();

        if (!$check) {
            UserPreference::create($request);
        } else {
            $check->update($request);
        }

        return true;
    }

    public function saveLocation($request)
    {
        $check = UserPreference::where('user_id', $request['user_id'])->first();

        if (!$check) {
            UserPreference::create([
                'user_id' => $request['user_id'],
                'current_lat' => $request['current_lat'],
                'current_long' => $request['current_long']
            ]);
        } else {
            $check->current_lat = $request['current_lat'];
            $check->current_long = $request['current_long'];
            $check->save();
        }

        return true;
    }
}
