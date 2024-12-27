<?php
namespace App\Events;

use App\Models\Setting;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class GameFinished implements ShouldBroadcast
{
    use SerializesModels;

    public $gameObject;


    public function __construct($gameObject)
    {
        $this->gameObject = $gameObject;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('games-finished');
    }

    public function broadcastWith(): array
    {
        $scores = [];
        foreach ($this->gameObject->users as $user) {
            $answeredQuestionsScore = 0;
            $answeredQuestions= $this->gameObject->gameQuestions()->where('answered_by', $user->id)->get();
            $answeredQuestions->load('question.difficulty');
            $secs = 0;
            foreach ($answeredQuestions as $answeredQuestion) {
                    $answeredQuestionsScore += $answeredQuestion->question->difficulty->score;
                    $secs += $answeredQuestion->answered_at->diffInSeconds($answeredQuestion->sent_at);
            }
            $user->experience += $answeredQuestionsScore;
            $user->save();
            $scores[] = [
                'user' => $user->name,
                'score' => $answeredQuestionsScore,
                'time' => $secs,
                'id' => $user->id,
            ];
        }
        $winner=null;
        foreach ($scores as $score) {
            if ($winner == null) {
                $winner = $score;
            } else {
                if ($score['score'] > $winner['score']) {
                    $winner = $score;
                } elseif ($score['score'] == $winner['score']) {
                    if ($score['time'] < $winner['time']) {
                        $winner = $score;
                    }
                }
            }
        }
        $this->gameObject->users->find($winner['id'])->update(['score' => $winner['score']]);
        $user = $this->gameObject->users->find($winner['id']);
        $user->coins = $user->coins + Setting::where('key', 'coins_per_game')->first()->value;
        return [
            'game' => $this->gameObject,
            'winner' => $winner,
        ];
    }
}
