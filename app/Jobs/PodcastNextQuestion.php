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
    public $next_question;
    /**
     * Create a new job instance.
     */
    public function __construct(Game $game, Question $next_question)
    {
        $this->game= $game;
        $this->next_question= $next_question;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        broadcast(new NextQuestion($this->game, $this->next_question));
    }
}
