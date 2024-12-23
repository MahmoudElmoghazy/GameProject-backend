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
            $answeredQuestions= $this->gameObject->gameQuestions->where('answered_by', $user->id);
            $score = $answeredQuestions->sum('score');
            $secs = 0;
            foreach ($answeredQuestions->get as $answeredQuestion) {
                    $secs += $answeredQuestion->answered_at->diffInSeconds($answeredQuestion->sent_at);
            }
            $scores[] = [
                'user' => $user->name,
                'score' => $score,
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
        $user->save();
        return [
            'game' => $this->gameObject,
            'winner' => $winner,
        ];
    }
}
