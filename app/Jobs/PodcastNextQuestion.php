<?php

namespace App\Jobs;

use App\Events\CorrectAnswer;
use App\Events\GameFinished;
use App\Events\NextQuestion;
use App\Models\Game;
use App\Models\Question;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PodcastNextQuestion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $game;
    /**
     * Create a new job instance.
     */
    public function __construct(Game $game)
    {
        $this->game= $game;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $no_of_secs = $this->game->time_for_each_question;
        sleep($no_of_secs);
        $unanswered_count = $this->game->gameQuestions->where('is_answered', false)->count();
        while ($unanswered_count > 0) {
            $no_of_secs_since_start = now()->diffInSeconds($this->game->created_at);
            $answered_question=$this->game->gameQuestions->where('is_answered', true)->count();
            if($no_of_secs_since_start >= $no_of_secs * $answered_question){
                $this->game->gameQuestions()->where('question_id', $this->game->current_question)->update(['is_answered'=>true]);
                $next_question = $this->game->gameQuestions()->where('is_answered', false);
                dump($next_question->count());
                if($next_question->count() == 0){
                    $this->game->status = 'finished';
                    $this->game->save();
                    broadcast(new GameFinished($this->game));
                    break;
                }
                $next_question = $this->game->gameQuestions()->where('is_answered', false)->first();
                $previous_question = Question::find($this->game->current_question);
                $previous_answer =$previous_question->right_answer_id;
                $this->game->current_question = $next_question->question_id;
                $this->game->save();
                $this->game->load('gameQuestions.question.answers');
                broadcast(new CorrectAnswer($this->game, $next_question,$previous_answer,null));
                sleep(5);
                broadcast(new NextQuestion($this->game, $next_question,$previous_answer));
                sleep($no_of_secs);
            }
            $unanswered_count = $this->game->gameQuestions()->where('is_answered', false)->count();
        }
    }
}
