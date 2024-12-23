<?php

namespace App\Http\Controllers;

use App\Events\CorrectAnswer;
use App\Events\GameFinished;
use App\Events\GameObjectCreated;
use App\Events\GameObjectUpdated;
use App\Events\GameStarted;
use App\Events\NextQuestion;
use App\Events\PlayerJoined;
use App\Events\PlayerLeft;
use App\Events\WrongAnswer;
use App\Http\Requests\createGameRequest;
use App\Http\Resources\GameCollection;
use App\Jobs\PodcastNextQuestion;
use App\Models\Game;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Http\JsonResponse;

class GameController extends Controller
{

    public function list()
    {
        return GameCollection::collection(Game::with('owner','category')->where('status','pending')->get());
    }

    public function create(createGameRequest $request)
    {
            $game = new Game();
            $game->no_of_questions = $request->no_of_questions;
            $game->no_of_players = $request->no_of_players;
            $game->code = $request->code;
            $game->difficulty_id = $request->difficulty_id;
            $game->time_for_each_question = $request->time_for_each_question;
            $game->category_id = $request->category_id;
            $game->user_id= auth()->id();
            $game->save();
            $questions = Question::where([['category_id', $request->category_id],['difficulty_id',$request->difficulty_id]])->inRandomOrder()->limit($request->no_of_questions)->get();
            $game->gameQuestions()->createMany($questions->map(function($question){
                return ['question_id'=>$question->id];
            })->toArray());
            $game->load('owner');
            broadcast(new GameObjectCreated($game));
            return GameCollection::make($game);
    }

    public function joinGame(Game $game): JsonResponse
    {
        if($game->status != 'pending'){
            return response()->json(['message'=>'Game has already started or finished'], 400);
        }
        $game->increment('no_of_joined_players');
        $game->save();
        $user = auth()->user();
        $game->users()->attach($user);
        $game->load('users');
        broadcast(new PlayerJoined($user,$game));
        broadcast(new GameObjectUpdated($game));
        if($game->no_of_joined_players == $game->no_of_players){
            $game->status = 'started';
            $game->started_at = now();
            $game->save();
            $game->load('gameQuestions.question.answers');
            broadcast(new GameStarted($game));
            PodcastNextQuestion::dispatch($game);
        }
        return response()->json(['message' => 'joined successfully'], 200);
    }

    public function leaveGame(Game $game): JsonResponse
    {
        if($game->status == 'pending' || $game->status == 'started'){
            $game->decrement('no_of_joined_players');
            $game->save();
            $user = auth()->user();
            $game->users()->detach($user);
            broadcast(new PlayerLeft($user,$game));
            broadcast(new GameObjectUpdated($game));
            return response()->json(['message' => 'left successfully'], 200);
        }else{
            return response()->json(['message'=>'Game has already finished'], 400);
        }
    }

    public function answerQuestion(Game $game,Answer $answer,Question $question): JsonResponse
    {
        if($game->status == 'started'){
            $question = $game->gameQuestions()->where('question_id',$question->id)->first();
            if($game->current_question != $question->id){
                return response()->json(['message'=>'This question is not the current question'], 400);
            }
            $user= auth()->user();
            if($answer->id == $question->right_answer_id){
                $game->gameQuestions()->where('question_id',$question->id)->update(['answered_at'=>now(),'answered_by'=>$user->id,'is_answered'=>true]);
                broadcast(new CorrectAnswer($game,$question,$answer,$user));
                $next_question = $game->gameQuestions()->get()->where('is_answered',false)->first();
                $game->current_question = $next_question->question_id;
                Broadcast(new NextQuestion($game,$next_question))->delay(now()->addSeconds(5));
                if($game->gameQuestions()->get()->where('is_answered',true)->count() == $game->no_of_questions){
                    $game->status = 'finished';
                    $game->save();
                    broadcast(new GameFinished($game));
                }
                return response()->json(['message'=>'correct answer','status'=>true], 200);
            }
            broadcast(new WrongAnswer($game,$question,$answer,$user));
            return response()->json(['message'=>'wrong answer','status'=>false], 200);
        }else{
            return response()->json(['message'=>'Game has not started yet or finished'], 400);
        }
    }

}
