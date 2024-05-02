<?php

namespace App\Repositories\Admin;

use App\Models\{
    Locations,
    Question,
    ReportUser,
    State,
    SubscriptionPlan,
    User,
    UserLocation,
    UserSubscription,
};
use App\Repositories\SubscriptionPlanRepository;
use Carbon\Carbon;
use DateTime;
use App\Http\Resources\UserLocationResource;
use App\Repositories\LocationRepository;
use Illuminate\Support\Facades\DB;

class UserRepository
{

    public function allUsers()
    {
        $users = User::doesntHave('roles')->orderBy('id', 'DESC')->get();
        return $users;
    }

    public function findUserById($id)
    {
        $genderQuestionId = Question::where('title', 'Gender')->first()->id;
        $user = User::with(['answers' => function ($q) use ($genderQuestionId) {
            $q->where('question_id', $genderQuestionId);
        }])->find($id);
        return $user;
    }

    public function updateUser($input, $id)
    {
        $years = Carbon::parse($input['birthdate'])->age;
        $input['age'] = $years;

        $user = User::find($id);
        $user->update($input);
        $userSubscription = UserSubscription::whereUserId($id)->latest()->first();
        if (!empty($userSubscription)) {
            // set expire at null for the user shannon.ryan@s4g.com.au due to apple store approval
            if( strtolower($user->email) == 'shannon.ryan@s4g.com.au'){
                $input['expire_at'] =null;
            }else{
                $subscription_expire_at=date('Y-m-d', strtotime('+1 year', strtotime($input['payment_date'])) );
                $input['expire_at']=$subscription_expire_at;    
            }
            $userSubscription->update($input);
        }
        if(!empty($input['states']) && !empty($input['suburb'])){
            $userLocationUpdate=array();
            $userLocationUpdate['state_id']= $input['states']??null;
            $userLocationUpdate['suburb_id']= $input['suburb']??null;
            $userLocationUpdate['user_id']= $id??null;
            $query = UserLocation::whereUserId($id)->latest();
            if(!$query->exists()){
                return $query->create($userLocationUpdate);
            }else{
                return $query->update($userLocationUpdate);
            }

        }
        return $user;
    }

    public function deleteUser($id)
    {
        $user = User::find($id);
        $user->delete();
        return true;
    }

    public function deleteReportUser($id)
    {
        $user = User::withTrashed()->find($id);
        $user->forcedelete();
        return true;
    }

    public function getAllUsers($request)
    {
        $userStatus = User::USER_STATUS;
        $userSubscriptionStatus = UserSubscription::USER_SUBSCRIPTION_STATUS;
        $userPaymentStatus = UserSubscription::USER_PAYMENT_STATUS;
        $planStatus = SubscriptionPlan::PLAN_STATUS;

        $columns = array(
            0 => 'id',
            1 => 'first_name',
            2 => 'email',
            3 => 'state',
            4 => 'suburb',
            5 => 'status',
            6 => 'subscription_status',
            7 => 'remaning_days',
            8 => 'payment_status',
            9 => 'created_at',
            10 => 'id',
        );



        $totalData = User::doesntHave('roles')->orderBy('id', 'DESC')->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $users = User::doesntHave('roles')->with(['userSubscription','userLocation'])->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $status_search = preg_grep("/^$search/i", User::USER_STATUS);
            $user_status_key = array_key_first($status_search) ?? null;

            $subscription_status_search = preg_grep("/^$search/i", $userSubscriptionStatus);
            $user_subscription_status_key = array_key_first($subscription_status_search) ?? null;

            $payment_status_search = preg_grep("/^$search/i", $userPaymentStatus);
            $payment_status_key = array_key_first($payment_status_search) ?? null;
            
            // DB::enableQueryLog();
            $users =  User::doesntHave('roles')->with(['userSubscription','userLocation'])
                ->where('id', 'LIKE', "%{$search}%")
                ->orWhere('first_name', 'LIKE', "%{$search}%")
                ->orWhere('last_name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%")
                ->orWhere('created_at', 'LIKE', "%{$search}%")
                ->orWhere('status', $user_status_key)
                ->orWhereHas('userSubscription', function ($query) use ($user_subscription_status_key) {
                    $query->where('subscription_status', 'LIKE', "{$user_subscription_status_key}");
                })
                ->orWhereHas('userSubscription', function ($query) use ($payment_status_key) {
                    $query->where('payment_status', 'LIKE', "{$payment_status_key}");
                })
                ->orWhereHas('userLocation.userState',function($query) use($search){
                    $query->where('name','LIKE',"%{$search}%");
                })
                ->orWhereHas('userLocation.userSuburb',function($query) use($search){
                    $query->where('suburb_name','LIKE',"%{$search}%");
                })

                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

                // dd(DB::getQueryLog());

            // $totalFiltered = User::doesntHave('roles')->with(['userSubscription','userLocation'])
            //     ->where('id', 'LIKE', "%{$search}%")
            //     ->orWhere('first_name', 'LIKE', "%{$search}%")
            //     ->orWhere('last_name', 'LIKE', "%{$search}%")
            //     ->orWhere('email', 'LIKE', "%{$search}%")
            //     ->orWhere('created_at', 'LIKE', "%{$search}%")
            //     ->orWhere('status', $user_status_key)
            //     ->orWhereHas('userSubscription', function ($query) use ($user_subscription_status_key) {
            //         $query->where('subscription_status', 'LIKE', "%{$user_subscription_status_key}%");
            //     })
            //     ->orWhereHas('userSubscription', function ($query) use ($payment_status_key) {
            //         $query->where('payment_status', 'LIKE', "%{$payment_status_key}%");
            //     })
            //     ->count();
            $totalFiltered = count($users);
        }

        $data = array();
        if (!empty($users)) {            
            foreach ($users as $user) {
                $userState = $userSuburb ='';
                if(isset($user->userLocation) && !empty($user->userLocation)){
                    $userState = State::find($user->userLocation->state_id);
                    // $userSuburb= Locations::find($user->userLocation->suburb_id);
                    $userLocationRepository= new LocationRepository;
                    $userSuburb= $userLocationRepository->getSuburbById($user->userLocation->suburb_id);
                }
                $show =  route('admin.users.show', $user->id);
                $edit =  route('admin.users.edit', $user->id);

                $currentTime = Carbon::now();
                $currentDatetime = new DateTime($currentTime);
                $userCreatedAt = $user->created_at;
                $userCreatedAtDateTime = new DateTime($userCreatedAt);
                if (count($user->userSubscription) > 0) {
                    $planId = $user->userSubscription[0]['subscription_plan_id'];
                    $subscriptionPlanRepository = new SubscriptionPlanRepository;
                    $subscriptionPlan = $subscriptionPlanRepository->findSubscriptionPlanById($planId);
                    $subscriptionDate = $user->userSubscription[0]['subscription_date'];
                    $subscriptionDateTime = new DateTime($subscriptionDate);
                    $interval = $currentDatetime->diff($subscriptionDateTime); //difference b/w two dates.
                    $days = $interval->format('%a');
                    if ($user->userSubscription[0]['payment_status'] == 2) {
                        //Users subscibed with payment: 3 = Yearly,2 = Half-Yearly,1 = Monthly
                        if ($subscriptionPlan->plan_type ==  3) {
                            $remainingDays = 365 - $days;
                        } else if ($subscriptionPlan->plan_type ==  2) {
                            $remainingDays = 180 - $days;
                        } else {
                            $remainingDays = 30 - $days;
                        }
                    } else {
                        //users subscibed but payment not done
                        $interval = $currentDatetime->diff($userCreatedAtDateTime);
                        $days = $interval->format('%a');
                        $extraDays = 30 - $days == 0 ? 7 : 30 - $days;
                        $remainingDays = $extraDays >= 0 ? $extraDays : "N/A";
                    }
                } else {
                    //unsubscribed users
                    $interval = $currentDatetime->diff($userCreatedAtDateTime);
                    $days = $interval->format('%a');
                    $extraDays = 30 - $days == 0 ? 7 : 30 - $days;
                    $remainingDays = $extraDays >= 0 ? $extraDays : "N/A";
                }


                $user_state = 'N/A';
                if(!empty($userState) && !empty($userState->name)){
                    $user_state = $userState->name; 
                }
                $user_suburb = 'N/A';
                if(!empty($userSuburb) && !empty($userSuburb->suburb_name)){
                    $user_suburb = $userSuburb->suburb_name; 
                }
                $nestedData['id'] = $user->id;
                $nestedData['name'] = "<span class='userNameTd'> {$user->first_name} {$user->last_name} </span>";
                $nestedData['email'] = $user->email;
                $nestedData['state'] = $user_state;
                $nestedData['suburb'] = $user_suburb;
                $nestedData['status'] = $user->status != null ? userStatus($user->status) : "N/A";
                $nestedData['subscription_status'] = count($user->userSubscription) > 0  ? getUserSubscriptionStatus($user->userSubscription[0]['subscription_status']) : "N/A";
                $nestedData['remaning_days'] = $remainingDays;
                $nestedData['payment_status'] = count($user->userSubscription) > 0  ? userPaymentStatus($user->userSubscription[0]['payment_status']) : "N/A";
                $nestedData['created_at'] = convertToViewDateOnly($user->created_at);
                // Edit and delete button dont show for subscription plan id is 1 due to approval in app store.
                if( strtolower($user->email) == 'shannon.ryan@s4g.com.au'){
                    $nestedData['options'] = "&emsp;<a class='btn btn-info' href='{$show}'>View</a>";
                }else{
                    $nestedData['options'] = "&emsp;<a class='btn btn-info' href='{$show}'>View</a>
                    &emsp;<a class='btn btn-primary' href='{$edit}'>Edit</a> &emsp;<button type='button' class='btn btn-danger' onclick='deleteUser({$user->id})'>Delete</button>";
                }
                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        return  $json_data;
    }

    public function getReportUsers($request)
    {
        $columns = array(
            0 => 'id',
            1 => 'user_id',
            2 => 'to_user_id',
            3 => 'file',
            4 => 'type',
            5 => 'reason',
            6 => 'channel_url',
            7 => 'objectional_type',
            8 => 'created_at',
            9 => 'id',
        );

        $totalData = ReportUser::orderBy('id', 'DESC')->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $users = ReportUser::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $objectional_search = preg_grep("/^$search/i", ReportUser::REPORT_TYPE);
            $objectional_search_id = array_key_first($objectional_search);

            $users =  ReportUser::with(['fromUser', 'toUser'])
                ->where('id', 'LIKE', "%{$search}%")
                ->orWhere('type', 'LIKE', "%{$search}%")
                ->orWhere('reason', 'LIKE', "%{$search}%")
                ->orWhere('channel_url', 'LIKE', "%{$search}%")
                ->orWhere('created_at', 'LIKE', "%{$search}%")
                ->orWhere('objectional_type', $objectional_search_id)
                ->orWhereHas('fromUser', function ($query) use ($search) {
                    $query->where('first_name', 'LIKE', "%{$search}%");
                    $query->orWhere('last_name', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('toUser', function ($query) use ($search) {
                    $query->where('first_name', 'LIKE', "%{$search}%");
                    $query->orWhere('last_name', 'LIKE', "%{$search}%");
                })
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = ReportUser::with(['fromUser', 'toUser'])
                ->where('id', 'LIKE', "%{$search}%")
                ->orWhere('type', 'LIKE', "%{$search}%")
                ->orWhere('reason', 'LIKE', "%{$search}%")
                ->orWhere('channel_url', 'LIKE', "%{$search}%")
                ->orWhere('created_at', 'LIKE', "%{$search}%")
                ->orWhere('objectional_type', $objectional_search_id)
                ->orWhereHas('fromUser', function ($query) use ($search) {
                    $query->where('first_name', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('toUser', function ($query) use ($search) {
                    $query->where('first_name', 'LIKE', "%{$search}%");
                    $query->orWhere('last_name', 'LIKE', "%{$search}%");
                })
                ->count();
        }

        $data = array();
        if (!empty($users)) {
            foreach ($users as $user) {
                $edit =  route('admin.users.edit', $user->to_user_id);

                $nestedData['id'] = $user->id;
                $nestedData['fromUser'] = $user->fromUser ? $user->fromUser->first_name . " " . $user->fromUser->last_name : "--";
                $nestedData['toUser'] = $user->toUser ? $user->toUser->first_name . " " . $user->toUser->last_name : "--";
                $nestedData['file'] = $user->file_type == 'audio' ? "<div class='file'> " . $user->file . "</div>" : "<img src='{$user->file}' />";
                $nestedData['type'] = $user->type;
                $nestedData['reason'] = $user->reason;
                $nestedData['channelUrl'] = $user->channel_url;
                $nestedData['objectionalType'] = ReportUser::REPORT_TYPE[$user->objectional_type];
                $nestedData['created_at'] = convertToViewDateOnly($user->created_at);
                $nestedData['options'] = "&emsp;<a class='btn btn-primary' href='{$edit}'>Edit</a> &emsp;<button type='button' class='btn btn-danger' onclick='deleteUser({$user->to_user_id})'>Delete</button>";
                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        return $json_data;
    }


    public function getUserStatusList()
    {
        return $statusList = [
            User::TRAIL_STATUS => User::USER_STATUS[User::TRAIL_STATUS],
            User::SUSPENDED_STATUS => User::USER_STATUS[User::SUSPENDED_STATUS],
            User::BLOCKED_STATUS => User::USER_STATUS[User::BLOCKED_STATUS],
            User::ON_HOLD_STATUS => User::USER_STATUS[User::ON_HOLD_STATUS],
            User::ACTIVE_STATUS => User::USER_STATUS[User::ACTIVE_STATUS],
            User::NEW_STATUS => User::USER_STATUS[User::NEW_STATUS],
        ];
    }
}
