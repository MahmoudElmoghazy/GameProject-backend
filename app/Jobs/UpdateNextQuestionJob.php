<?php

namespace App\Jobs;

use App\Events\NextQuestion;
use App\Events\NextQuestionUpdate;
use App\Models\Game;
use App\Models\Question;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateNextQuestionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $game_id;
    public $next_question_id;

    public function __construct(int $game_id, int $next_question_id)
    {
        $this->game_id = $game_id;
        $this->next_question_id = $next_question_id;
    }

    public function handle(): void
    {
        // Retrieve the models inside the handle method
        $game = Game::find($this->game_id);
        $next_question = Question::find($this->next_question_id);

        // Broadcast the event
        broadcast(new NextQuestionUpdate($game, $next_question));
    }
}
