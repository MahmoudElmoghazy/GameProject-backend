<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class GameCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'no_of_questions'=>$this->no_of_questions,
            'no_of_players'=>$this->no_of_players,
            'code'=>$this->code,
            'difficulty_id'=>$this->difficulty_id,
            'owner_id'=> $this->user_id,
            'time_for_each_question'=>$this->time_for_each_question,
            'category_id'=>$this->category_id,
            'category_name'=>$this->category->name,
            'no_of_joined_players'=>$this->no_of_joined_players,
            'owner_name' =>$this->owner->name,
            'users' => $this->whenLoaded('users',function (){
                return UserCollection::collection($this->users);
            }),
        ];
    }
}
