<?php

namespace App\Repositories;

use App\Models\{
    User,
    SubscriptionPlan,
    UserSubscription
};
use Carbon\Carbon;

class SubscriptionPlanRepository
{

    public function addSubscriptionPlan($input)
    {
        $subscriptionPlan = SubscriptionPlan::create($input);
        return $subscriptionPlan;
    }

    public function findSubscriptionPlanById($id)
    {

        $subscriptionPlan = SubscriptionPlan::find($id);
        return $subscriptionPlan;
    }

    public function updateSubscriptionPlan($input, $id)
    {
        $subscriptionPlan = SubscriptionPlan::find($id);
        $subscriptionPlan->update($input);
        return $subscriptionPlan;
    }

    public function deleteSubscriptionPlan($id)
    {
        $subscriptionPlan = SubscriptionPlan::find($id);
        $subscriptionPlan->delete();
        return true;
    }

    public function getAllSubscriptionPlans($request)
    {

        $columns = array(
            0 => 'id',
            1 => 'plan_name',
            2 => 'plan_type',
            3 => 'amount',
            4 => 'created_at',
            5 => 'id',
        );

        $planStatus = SubscriptionPlan::PLAN_STATUS;

        $totalData = SubscriptionPlan::orderBy('id', 'DESC')->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $subscriptionPlans = SubscriptionPlan::offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $status_search = preg_grep("/^$search/i", $planStatus);
            $plan_status_key = array_key_first($status_search);

            $subscriptionPlans =  SubscriptionPlan::where('id', 'LIKE', "%{$search}%")
                ->orWhere('plan_name', 'LIKE', "%{$search}%")
                ->orWhere('plan_type', $plan_status_key)
                ->orWhere('amount', 'LIKE', "%{$search}%")
                ->orWhere('created_at', 'LIKE', "%{$search}%")
                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $totalFiltered = SubscriptionPlan::where('id', 'LIKE', "%{$search}%")
                ->orWhere('plan_name', 'LIKE', "%{$search}%")
                ->orWhere('plan_type', $plan_status_key)
                ->orWhere('amount', 'LIKE', "%{$search}%")
                ->orWhere('created_at', 'LIKE', "%{$search}%")
                ->count();
        }
        $data = array();
        if (!empty($subscriptionPlans)) {
            foreach ($subscriptionPlans as $subscriptionPlan) {
                $show =  route('admin.subscriptionPlans.show', $subscriptionPlan->id);
                $edit =  route('admin.subscriptionPlans.edit', $subscriptionPlan->id);

                $nestedData['id'] = $subscriptionPlan->id;
                $nestedData['plan_name'] = "<span class='subscriptionPlanNameTd'> {$subscriptionPlan->plan_name} </span>";
                $nestedData['plan_type'] = $subscriptionPlan->plan_type != null ? $planStatus[$subscriptionPlan->plan_type] : "N/A";
                $nestedData['amount'] = $subscriptionPlan->amount;
                $nestedData['created_at'] = convertToViewDateOnly($subscriptionPlan->created_at);

                // Edit and delete button dont show for subscription plan id is 1 due to approval in app store.
                if($subscriptionPlan->id !=1 && count($subscriptionPlans)>1){
                    $nestedData['options'] = "&emsp;<a class='btn btn-info' href='{$show}'>View</a>
                           &emsp;<a class='btn btn-primary' href='{$edit}'>Edit</a> &emsp;<button type='button' class='btn btn-danger' onclick='deleteSubscriptionPlan({$subscriptionPlan->id})'>Delete</button>";

                }else{
                    $nestedData['options'] = "&emsp;<a class='btn btn-info' href='{$show}'>View</a>";
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

        return $json_data;
    }

    public function getPlanList()
    {
        return  $planList = [
            SubscriptionPlan::MONTHLY_STATUS => SubscriptionPlan::PLAN_STATUS[SubscriptionPlan::MONTHLY_STATUS],
            SubscriptionPlan::HALF_YEARLY_STATUS => SubscriptionPlan::PLAN_STATUS[SubscriptionPlan::HALF_YEARLY_STATUS],
            SubscriptionPlan::YEARLY_STATUS => SubscriptionPlan::PLAN_STATUS[SubscriptionPlan::YEARLY_STATUS],
        ];
    }

    public function getSubscriptionStatusList()
    {
        return $subscriptionStatusList = [
            UserSubscription::TRAIL_STATUS => UserSubscription::USER_SUBSCRIPTION_STATUS[UserSubscription::TRAIL_STATUS],
            UserSubscription::IN_ACTIVE_STATUS => UserSubscription::USER_SUBSCRIPTION_STATUS[UserSubscription::IN_ACTIVE_STATUS],
            UserSubscription::ACTIVE_STATUS => UserSubscription::USER_SUBSCRIPTION_STATUS[UserSubscription::ACTIVE_STATUS],
        ];
    }

    public function getPaymentStatusList()
    {
        return  $paymentStatus = [
            UserSubscription::PENDING_STATUS => UserSubscription::USER_PAYMENT_STATUS[UserSubscription::PENDING_STATUS],
            UserSubscription::PAID_STATUS => UserSubscription::USER_PAYMENT_STATUS[UserSubscription::PAID_STATUS],
        ];
    }
    public function getSubscriptionPlans()
    {
        $subscriptionPlans = SubscriptionPlan::all();
        return $subscriptionPlans;
    }
}
