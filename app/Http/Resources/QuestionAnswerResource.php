<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class QuestionAnswerResource extends JsonResource
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
            'id' => $this->question->id,
            'title' => $this->question->title,
            'type' => $this->question->type,
            'options' => QuestionOptionResource::collection($this->question->options),
            'answer' => $this->question->type == 'text' ? $this->answer : $this->answer_id
        ];
    }
}
