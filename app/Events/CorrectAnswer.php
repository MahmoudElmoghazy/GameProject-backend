<?php
namespace App\Events;

use App\Http\Resources\QuestionCollection;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class CorrectAnswer implements ShouldBroadcast
{
    use SerializesModels;

    public $user;
    public $gameObject;
    public $answer;
    public $question;


    public function __construct($gameObject,$question,$answer,$user)
    {
        $this->user = $user;
        $this->gameObject = $gameObject;
        $this->answer = $answer;
        $this->question = $question;

    }

    public function broadcastOn()
    {
        return new Channel('games-answer-'.$this->gameObject->id);
    }

    public function broadcastWith()
    {
        return [
            'game' => $this->gameObject,
            'user' => $this->user,
            'answer' => $this->answer,
            'question' => $this->question,
        ];
    }
}
