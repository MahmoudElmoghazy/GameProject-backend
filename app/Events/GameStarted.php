<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class GameStarted implements ShouldBroadcast
{
    use SerializesModels;

    public $gameObject  ;

    public function __construct($gameObject)
    {
        $this->gameObject = $gameObject;
    }

    public function broadcastOn()
    {
        return new Channel('games-start-'.$this->gameObject->id);
    }

    public function broadcastWith()
    {
        $next_question = $this->gameObject->gameQuestions->where('is_answered',false)->first();
        $next_question->load('answers');
        return ['game' => $this->gameObject,'next_question' => $next_question];
    }
}
