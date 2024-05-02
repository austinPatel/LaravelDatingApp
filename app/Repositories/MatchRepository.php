<?php

namespace App\Repositories;

use App\Models\{
    NotInterestedUser,
    Question,
    User,
    UserPreference
};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MatchRepository
{
    public function viewMatches($request)
    {
        $user_lat = $request['user_lat'] ?? null;
        $user_long = $request['user_long'] ?? null;
        $user_id = Auth::user()->id;
        $searchUserID = $request['user_id'] ?? null;
        if ($user_lat && $user_long) {
            UserPreference::where('user_id', $user_id)->update(['current_lat' => $user_lat, 'current_long' => $user_long]);
        }

        $user = User::where('id', $user_id)->with('userInterest', 'preference', 'notInterestedUser', 'reportToUsers', 'reportFromUsers')->first();
        $genderQuestionId = Question::where('title', 'Gender')->first()->id;
        $userInterestIds = $user->userInterest->pluck('interest_id')->toArray();
        $showMorePeople = $user->preference->show_more_people;
        $in = '(' . implode(',', $userInterestIds) . ')';
        $notInterestedUserIds = $user->notInterestedUser->pluck('recommendation_user_id')->toArray();

        $reportToUserIds = $user->reportToUsers->pluck('to_user_id')->toArray();
        $reportFromUserIds = $user->reportFromUsers->pluck('user_id')->toArray();
        $reportUserIds = array_unique(array_merge($reportFromUserIds, $reportToUserIds));

        $distanceRaw = DB::raw('( 6371 * acos( cos( radians(' . $user->preference->current_lat . ') ) 
                * cos( radians( user_preferences.current_lat ) ) 
                * cos( radians( user_preferences.current_long ) 
                - radians(' . $user->preference->current_long  . ') ) 
                + sin( radians(' . $user->preference->current_lat  . ') ) 
                * sin( radians( user_preferences.current_lat ) ) ) )');

        $interestRaw = DB::raw("(select COUNT(user_interests.interest_id) from user_interests where interest_id IN $in AND user_interests.user_id=users.id GROUP BY users.id)");

        $query = User::selectRaw("users.*, {$distanceRaw} AS distance, {$interestRaw} AS interests, (CASE WHEN user_connections.from_user_id = $user_id AND user_connections.status = 'pending' THEN 'requested' ELSE user_connections.status END) as connectionStatus")
            ->with('userInterest.interest', 'preference', 'answers.question')
            ->leftjoin('user_preferences', 'user_preferences.user_id', 'users.id');

        if ($user->preference->interested_in != 'Everyone') {
            $query = $query->leftjoin('answers', 'answers.user_id', 'users.id')
                ->leftjoin('question_options', 'question_options.id', 'answers.answer_id');
        }

        $query = $query->leftjoin('user_connections', function ($join) use ($user_id) {
            $join->on(function ($query) use ($user_id) {
                $query->on('user_connections.to_user_id', '=', 'users.id');
                $query->on('user_connections.from_user_id', DB::raw($user_id));
            });
            $join->orOn(function ($q) use ($user_id) {
                $q->on('user_connections.from_user_id', '=', 'users.id');
                $q->on('user_connections.to_user_id', DB::raw($user_id));
            });
        });

        if ($user->preference->interested_in != 'Everyone') {
            $query = $query->where('answers.question_id', $genderQuestionId)
                ->where('question_options.title', $user->preference->interested_in);
        }

        $query = $query->where('user_preferences.mode', $user->preference->mode)
            ->whereBetween('users.age', [$user->preference->min_age, $user->preference->max_age])
            ->whereNot('users.id', $user_id)
            ->where(
                function ($query) {
                    $declined_curr_date = Carbon::now();
                    $query->whereNot('user_connections.status', 'disconnect');
                    $query->whereNot('user_connections.status', 'connect');
                    // $query->whereNot('user_connections.status', 'declined');
                    $query->whereNot('user_connections.declined_date_expire','>=',$declined_curr_date); // After 30days users profile will be shown once declined request
                    $query->orWhere('user_connections.status', null);
                }
            )
            ->where(
                function ($query) {
                    // $query->whereNot('users.status', User::BLOCKED_STATUS);
                    //CC-98(12-06-2023)
                    $query->whereNotIn('users.status',[User::BLOCKED_STATUS,User::ON_HOLD_STATUS,User::SUSPENDED_STATUS]);
                }
            );

        if (!$showMorePeople) {
            $query = $query->havingRaw('distance <= ' . $user->preference->distance);
        }

        if ($user->preference->mode == 'dating') {
            $query = $query->havingRaw('interests >= 1');
        }

        $query->whereNotIn('users.id', $notInterestedUserIds);  //remove not interested users from list

        $query->whereNotIn('users.id', $reportUserIds);  //remove reported users from list

        if ($searchUserID) {
            $query = $query->where('users.id', $searchUserID);
        }

        $request['totalRecord'] = $query->count();

        $pagination = pagination($request);

        $query = $query->offset($pagination['offset'])->limit($pagination['perPage']);
        // $query = $query->groupBy('users.id');
        $query->orderByRaw("FIELD(users.status,". User::NEW_STATUS .") DESC");
        $query->orderBy('id','DESC');
        $query = $query->get();

        $response = [
            'data' => $query,
            'totalPageCount' => $pagination['totalPageCount'],
            'currentPage' => $pagination['page'],
            'totalRecord' => $request['totalRecord']
        ];

        return $response;
    }

    public function notInterested($request)
    {
        $user_id = Auth::user()->id;
        $recommendation_user_id = $request->user_id;

        $check = NotInterestedUser::where(['user_id' => $user_id, 'recommendation_user_id' => $recommendation_user_id])->first();

        if (!$check) {
            NotInterestedUser::create([
                'user_id' => $user_id,
                'recommendation_user_id' => $recommendation_user_id
            ]);
        }

        return true;
    }
}
