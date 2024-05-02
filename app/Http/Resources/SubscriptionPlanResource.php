<?php

namespace App\Http\Resources;

use App\Models\SubscriptionPlan;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPlanResource extends JsonResource
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
            "plan_name" => $this->plan_name,
            "plan_type" => SubscriptionPlan::PLAN_STATUS[$this->plan_type],
            "amount" => $this->amount,
            'description' => $this->description
        ];
    }
}
