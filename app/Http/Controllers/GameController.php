<?php

namespace App\Http\Controllers;

use App\Events\CorrectAnswer;
use App\Events\GameObjectCreated;
use App\Events\GameObjectUpdated;
use App\Events\GameStarted;
use App\Events\PlayerJoined;
use App\Events\PlayerLeft;
use App\Events\UpdateGameFinished;
use App\Events\WrongAnswer;
use App\Http\Requests\AnswerQuestionRequest;
use App\Http\Requests\createGameRequest;
use App\Http\Resources\GameCollection;
use App\Jobs\UpdateNextQuestionJob;
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
            $game->load('users');
            broadcast(new PlayerLeft($user,$game));
            broadcast(new GameObjectUpdated($game));
            return response()->json(['message' => 'left successfully'], 200);
        }else{
            return response()->json(['message'=>'Game has already finished'], 400);
        }
    }

    public function answerQuestion(Game $game,Question $question,AnswerQuestionRequest $request): JsonResponse
    {
        if($game->status == 'started'){
            if($game->current_question != $question->id){
                return response()->json(['message'=>'This question is not the current question'], 400);
            }
            $user= auth()->user();
            if($request->has('answer_id')){
                $answer = Answer::find($request->answer_id);
                if($answer->id == $question->right_answer_id){
                    $game->gameQuestions()->where('question_id',$question->id)->update(['answered_at'=>now(),'answered_by'=>$user->id,'is_answered'=>true]);
                    broadcast(new CorrectAnswer($game,$question,$answer,$user));
                    if($game->gameQuestions()->get()->where('is_answered',true)->count() == $game->no_of_questions){
                        $game->status = 'finished';
                        $game->save();
                        broadcast(new UpdateGameFinished($game));
                    }
                    $next_question = $game->gameQuestions()->get()->where('is_answered',false)->first();
                    if($next_question){
                        $game->current_question = $next_question->question_id;
                        $game->save();
                        $next_question->load('question.answers');
                        $game->gameQuestions()->where('question_id',$question->id)->update(['sent_at'=>now()]);
                        UpdateNextQuestionJob::dispatch($game->id, $next_question->question->id)->delay(now()->addSeconds(5));
                    }
                    return response()->json(['data'=>['message'=>'correct answer','status'=>true]], 200);
                }
                broadcast(new WrongAnswer($game,$question,$answer,$user));
            }else{
                $no_of_secs_since_start = now()->diffInSeconds($game->created_at);
                $answered_question=$game->gameQuestions->where('is_answered', true)->count();
                $no_of_secs = $game->time_for_each_question;
                if($no_of_secs_since_start >= $no_of_secs * $answered_question) {
                    $game->gameQuestions()->where('question_id', $game->current_question)->update(['is_answered' => true]);
                    $next_question = $game->gameQuestions()->where('is_answered', false)->first();
                    if($next_question){
                        $previous_question = Question::find($game->current_question);
                        $previous_answer =$previous_question->right_answer_id;
                        $game->current_question = $next_question->question_id;
                        $game->save();
                        $game->load('gameQuestions.question.answers');
                        broadcast(new CorrectAnswer($game, $next_question,$previous_answer,null));
                        $next_question->load('question.answers');
                        $game->gameQuestions()->where('question_id',$question->id)->update(['sent_at'=>now()]);
                        $game->current_question = $next_question->question_id;
                        $game->save();
                        UpdateNextQuestionJob::dispatch($game->id, $next_question->question->id)->delay(now()->addSeconds(5));
                        return response()->json(['data'=>['message'=>'question changed']], 200);
                    }else{
                        $game->status = 'finished';
                        $game->save();
                        broadcast(new UpdateGameFinished($game));
                        return response()->json(['message'=>'no more questions'], 400);
                    }
                }
            }

            return response()->json(['data'=>['message'=>'wrong answer','status'=>false]], 200);
        }else{
            return response()->json(['message'=>'Game has not started yet or finished'], 400);
        }
    }

}
