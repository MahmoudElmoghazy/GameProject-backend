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
    public $previousAnswer;

    public function __construct($gameObject,$next_question,$previous_answer=null)
    {
        $this->gameObject = $gameObject;
        $this->nextQuestion = $next_question;
        $this->previousAnswer= $previous_answer;
    }

    public function broadcastOn()
    {
        return new Channel('next-question-'.$this->gameObject->id);
    }

    public function broadcastWith()
    {
        $this->nextQuestion->load('question.answers');
        return [
            'previous_answer' => $this->previousAnswer,
            'next_question' => QuestionCollection::make($this->nextQuestion->question),
        ];
    }
}
