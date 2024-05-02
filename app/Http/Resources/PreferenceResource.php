<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PreferenceResource extends JsonResource
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
            'mode' => $this->mode,
            'min_age' => $this->min_age,
            'max_age' => $this->max_age,
            'distance' => $this->distance,
            'current_lat' => $this->current_lat,
            'current_long' => $this->current_long,
            'interested_in' => $this->interested_in,
            'show_more_people' => $this->show_more_people,
        ];
    }
}
