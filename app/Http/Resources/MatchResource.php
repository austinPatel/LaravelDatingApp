<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserInterestResource;
use App\Http\Resources\PreferenceResource;
use App\Http\Resources\AvatarResource;
use App\Http\Resources\QuestionAnswerResource;
use App\Models\User;
use Carbon\Carbon;

class MatchResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $expire_trail_date = $this->created_at->addDays(30);
        $is_trail_valid = $expire_trail_date >= Carbon::now() ? true : false;

        return [
            "id" => $this->id,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "email" => $this->email,
            "mobile" => $this->mobile,
            "birthdate" => $this->birthdate,
            "ndis_number" => $this->ndis_number,
            "age" => $this->age,
            "email_verified_at" => $this->email_verified_at,
            "isVerifiyMobile" => $this->isVerifiyMobile,
            "status" => User::USER_STATUS[$this->status],
            'userInterest' => UserInterestResource::collection($this->userInterest),
            'preference' => new PreferenceResource($this->preference),
            'questionsAnswer' => QuestionAnswerResource::collection($this->answers),
            'avatar' => AvatarResource::collection($this->getMedia('avatar'))->first(),
            'photos' => AvatarResource::collection($this->getMedia('photos')),
            'connectionStatus' => $this->connectionStatus,
            'term_conditions' => $this->term_conditions,
            'is_trail_valid' => $is_trail_valid,
            'distance' => $this->distance ? $this->distance : null,
            'userSubscription' => UserSubscriptionPlanResource::collection($this->userSubscription),
            'userLocation'=>new UserLocationResource($this->userLocation)
        ];
    }
}
