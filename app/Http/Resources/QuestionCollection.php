<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class QuestionCollection extends JsonResource
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
            'title'=>$this->title,
            'type'=>$this->type,
            'category_id'=>$this->category_id,
            'image'=>$this->image,
            'start_range'=>$this->start_range,
            'end_range'=>$this->end_range,
            'difficulty_id'=>$this->difficulty_id,
            'score'=>$this->difficulty->score,
            'answers' =>AnswerCollection::collection($this->answers),
        ];
    }
}
