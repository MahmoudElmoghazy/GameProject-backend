<?php
namespace App\Events;

use App\Http\Resources\GameCollection;
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
        $this->gameObject->current_question = $next_question->question->id;
        $this->gameObject->save();
        $this->gameObject->gameQuestions()->where('question_id',$next_question->question->id)->update(['sent_at'=>now()]);
        return ['game' => GameCollection::make($this->gameObject),'next_question' => $next_question];
    }
}
