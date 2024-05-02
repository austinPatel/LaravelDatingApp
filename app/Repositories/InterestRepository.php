<?php

namespace App\Repositories;

use App\Models\{
    Interest,
    UserInterest
};
use Illuminate\Support\Facades\Auth;

class InterestRepository
{
    public function interests($filter)
    {
        $query = Interest::orderBy('id', 'asc');
        if (!empty($filter)) {
            foreach ($filter as $key => $type) {
                $query = $query->orWhere('type', $type);
            }
        }

        return $query->get();
    }

    public function saveUserInterest($interests)
    {
        $user_id = Auth::user()->id;
        UserInterest::where('user_id', $user_id)->delete();

        foreach ($interests as $key => $interest) {
            UserInterest::create([
                'user_id' => $user_id,
                'interest_id' => $interest['id']
            ]);
        }
        return true;
    }
}
