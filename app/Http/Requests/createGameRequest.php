<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class createGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'no_of_questions'=>'required|integer',
            'no_of_players'=>'required|integer',
            'code'=>'string',
            'difficulty_id'=>'required|exists:difficulties,id',
            'category_id'=>'required|exists:categories,id',
            'time_for_each_question'=>'required|integer'
        ];
    }
}
