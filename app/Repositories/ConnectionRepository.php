<?php

namespace App\Repositories;

use App\Models\{
    User,
    UserConnection,
    UserFavorite,
    UserPreference,
};
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ConnectionRepository
{
    public function connectRequest($data)
    {
        // User Connection request

        $check = UserConnection::where(
            function ($query) use ($data) {
                $query->where(['from_user_id' => $data['from_user_id'], 'to_user_id' => $data['to_user_id']]);
            }
        )->orWhere(
            function ($q) use ($data) {
                $q->where(['from_user_id' => $data['to_user_id'], 'to_user_id' => $data['from_user_id']]);
            }
        )->first();

        $userPreferenceId = UserPreference::where('user_id', $data['from_user_id'])->first()->id;

        if (!$check) {
            $response = UserConnection::create([
                'from_user_id' => $data['from_user_id'],
                'to_user_id' => $data['to_user_id'],
                'status' => "pending",
                'user_prefercne_id' => $userPreferenceId
            ]);
        } else {
            if(!empty($check->declined_date_expire))
            {
                // After Declined user able to send request                
                $check->from_user_id = $data['from_user_id'];
                $check->to_user_id = $data['to_user_id'];
                $check->status = "pending";
                $check->user_prefercne_id = $userPreferenceId;
                $check->declined_by = 0;
                $check->declined_date = null;
                $check->declined_date_expire = null;
    
                $check->save();
                $response = $check;
            }
            if ($data['from_user_id'] != $check->from_user_id) {
                $check->status = "connect";
                $check->accepted_at = Carbon::now();
                $check->save();
            }
            $response = $check;
        }

        return $response;
    }

    public function connections($request)
    {
        // get user connections
        $user_id = Auth::user()->id;
        $blokedStatus = User::BLOCKED_STATUS;

        $reportToUserIds = Auth::user()->reportToUsers->pluck('to_user_id')->toArray();
        $reportFromUserIds = Auth::user()->reportFromUsers->pluck('user_id')->toArray();
        $reportUserIds = array_unique(array_merge($reportFromUserIds, $reportToUserIds));

        $connections = UserConnection::selectRaw("user_connections.*, (CASE WHEN user_connections.from_user_id = $user_id THEN user_connections.to_user_id ELSE user_connections.from_user_id END) AS connection_id, (select COUNT(user_favorites.id) from user_favorites where user_favorites.user_id=$user_id AND user_favorites.favorite_user_id=connection_id) AS is_favorite")
            ->with(['fromUser', 'toUser'])
            ->whereRAW("(select status from users where users.id = (CASE WHEN user_connections.from_user_id = $user_id THEN user_connections.to_user_id ELSE user_connections.from_user_id END)) != $blokedStatus AND deleted_at IS NULL")
            ->where(
                function ($query) use ($user_id, $reportUserIds) {
                    $query->where(['from_user_id' => $user_id, 'status' => 'connect']);
                    $query->whereNotIn('to_user_id', $reportUserIds); //remove reported users from list
                }
            )->orWhere(
                function ($q) use ($user_id, $reportUserIds) {
                    $q->where(['to_user_id' => $user_id, 'status' => 'connect']);
                    $q->whereNotIn('from_user_id', $reportUserIds); //remove reported users from list
                }
            )
            ->whereHas('fromUser')
            ->whereHas('toUser')
            ->orderBy('is_favorite', 'desc')
            ->orderBy('accepted_at', 'desc');

        $request['totalRecord'] = $connections->count();

        $pagination = pagination($request);

        $connections = $connections->offset($pagination['offset'])->limit($pagination['perPage']);
        $connections = $connections->get();

        $response = [
            'data' => $connections,
            'totalPageCount' => $pagination['totalPageCount'],
            'currentPage' => $pagination['page'],
            'totalRecord' => $request['totalRecord']
        ];

        return $response;
    }

    public function disconnectUser($request)
    {
        $authUserId = Auth::user()->id;
        $connectionId = $request->user_id;

        $check = UserConnection::where(
            function ($query) use ($authUserId, $connectionId) {
                $query->where(['from_user_id' => $authUserId, 'to_user_id' => $connectionId]);
            }
        )->orWhere(
            function ($q) use ($authUserId, $connectionId) {
                $q->where(['from_user_id' => $connectionId, 'to_user_id' => $authUserId]);
            }
        )->first();

        if ($check) {
            $check->status = "disconnect";
            $check->disconnected_from = $authUserId;
            $check->disconnected_at = Carbon::now();
            $check->save();

            $response = $check;
        }

        return $response;
    }

    public function addFavorite($request)
    {
        $user_id = Auth::user()->id;
        $favorite_user_id = $request->user_id;

        $check = UserFavorite::where(['user_id' => $user_id, 'favorite_user_id' => $favorite_user_id])->first();

        if (!$check) {
            UserFavorite::create([
                'user_id' => $user_id,
                'favorite_user_id' => $favorite_user_id
            ]);

            $message = "Favorite connection added successfully";
        } else {
            $check->delete();
            $message = "Favorite connection deleted successfully";
        }

        return $message;
    }

    public function declinedRequest(array $data)
    {
        $authUserId = $data['from_user_id'];
        $declinedUserId = $data['to_user_id'];

        $check = UserConnection::where(
            function ($query) use ($authUserId, $declinedUserId) {
                $query->where(['from_user_id' => $declinedUserId, 'to_user_id' => $authUserId]);
            }
        )->first();
        $response =array();
        if ($check) {
            $check->status = "declined";
            $check->declined_by = $authUserId;
            $check->declined_date = Carbon::now();
            $check->declined_date_expire = Carbon::now()->addDays(30);
            $check->save();
            $response = $check;
        }

        return $response;
    }
}
