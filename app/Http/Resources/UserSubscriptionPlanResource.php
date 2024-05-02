<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSubscriptionPlanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "user_id" => $this->user_id,
            "plan_manager_name" => $this->plan_manager_name,
            "plan_manager_email" => $this->plan_manager_email,
            "send_invoice"=>$this->send_invoice,
            "subscription_status" => getUserSubscriptionStatus($this->subscription_status),
            "subscription_date" => $this->subscription_date,
            'payment_status' => userPaymentStatus($this->payment_status),
            'payment_date' => $this->payment_date,
            'expire_at'=>$this->expire_at,
            'subscriptionPlan' => new SubscriptionPlanResource($this->subscriptionPlan),
        ];
    }
}
