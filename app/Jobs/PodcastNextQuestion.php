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
        $next_question = $this->game->gameQuestions()->where('is_answered', false)->first();
        broadcast(new NextQuestion($this->game, $next_question));
/*        while ($unanswered_count > 0) {
            $no_of_secs_since_start = now()->diffInSeconds($this->game->created_at);
            $answered_question=$this->game->gameQuestions->where('is_answered', true)->count();
            if($no_of_secs_since_start >= $no_of_secs * $answered_question){
                $this->game->gameQuestions()->where('question_id', $this->game->current_question)->update(['is_answered'=>true]);
                dump($next_question->count());
                if($next_question->count() == 0){
                    $this->game->status = 'finished';
                    $this->game->save();
                    broadcast(new GameFinished($this->game));
                    break;
                }

                sleep(5);
                sleep($no_of_secs);
            }
            $unanswered_count = $this->game->gameQuestions()->where('is_answered', false)->count();
        }*/
    }
}
