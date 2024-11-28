<?php

namespace App\Http\Controllers;

use App\Http\Resources\DifficultyCollection;
use App\Models\Difficulty;

class DifficultyController extends Controller
{
    public function list()
    {
        return DifficultyCollection::make(Difficulty::query()->get());
    }
}
