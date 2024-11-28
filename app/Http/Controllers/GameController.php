<?php

namespace App\Http\Controllers;

use App\Http\Requests\createGameRequest;
use App\Http\Resources\GameCollection;
use App\Models\Game;
use App\Models\Question;

class GameController extends Controller
{

    public function list()
    {
        return GameCollection::collection(Game::all());
    }

    public function create(createGameRequest $request)
    {
            $game = new Game();
            $game->no_of_questions = $request->no_of_questions;
            $game->no_of_players = $request->no_of_players;
            $game->code = $request->code;
            $game->difficulty_id = $request->difficulty_id;
            $game->time_for_each_question = $request->time_for_each_question;
            $game->save();
            $questions = Question::where([['category_id', $request->category_id],['difficulty_id',$request->difficulty_id]])->inRandomOrder()->limit($request->no_of_questions)->get();
            $game->gameQuestions()->createMany($questions->toArray());
            $game->load('gameQuestions.question.answers');
            return GameCollection::make($game);
    }
}
