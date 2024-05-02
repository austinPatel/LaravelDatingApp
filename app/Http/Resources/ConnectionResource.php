<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ConnectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user_id = Auth::user()->id;
        return [
            'id' => ($this->fromUser->id == $user_id) ? $this->toUser->id : $this->fromUser->id,
            'first_name' => ($this->fromUser->id == $user_id) ? $this->toUser->first_name : $this->fromUser->first_name,
            'last_name' => ($this->fromUser->id == $user_id) ? $this->toUser->last_name : $this->fromUser->last_name,
            'connection_since' => $this->accepted_at,
            'is_favorite' => $this->is_favorite,
            'avatar' => ($this->fromUser->id == $user_id) ? AvatarResource::collection($this->toUser->getMedia('avatar')) : AvatarResource::collection($this->fromUser->getMedia('avatar'))
        ];
    }
}
