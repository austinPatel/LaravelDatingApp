<?php

namespace App\Http\Resources;

use App\Models\Locations;
use App\Models\UserLocation;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\State;

class UserLocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // dd(StateResource::collection($this->state_id)->first());
        return [
            "state"=>new StateResource(State::find($this->state_id)),
            "suburb"=>new SuburbResource(Locations::find($this->suburb_id))
        ];
    }
}
