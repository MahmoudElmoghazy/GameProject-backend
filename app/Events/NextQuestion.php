<?php
namespace App\Events;

use App\Http\Resources\QuestionCollection;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class NextQuestion implements ShouldBroadcast
{
    use SerializesModels;
    public $gameObject;
    public $nextQuestion;

    public function __construct($gameObject,$next_question)
    {
        $this->gameObject = $gameObject;
        $this->nextQuestion = $next_question;
    }

    public function broadcastOn()
    {
        return new Channel('next-question-'.$this->gameObject->id);
    }

    public function broadcastWith()
    {
        return [
            'next_question'=>QuestionCollection::make($this->nextQuestion),
        ];
    }
}
