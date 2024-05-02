<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class LoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $expire_trail_date = $this->created_at->addDays(10);
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
            'term_conditions' => $this->term_conditions,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'is_trail_valid' => $is_trail_valid,
        ];
    }
}
