<?php

namespace App\Events;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class UpdateGameFinished implements ShouldBroadcast
{
    use SerializesModels;

    public $gameObject;


    public function __construct($gameObject)
    {
        $this->gameObject = $gameObject;
    }

    public function broadcastOn(): Channel
    {
        return new Channel('games-finished-'. $this->gameObject->id);
    }

    public function broadcastWith(): array
    {
        $scores = [];
        foreach ($this->gameObject->users as $user) {
            $answeredQuestionsScore = 0;
            $answeredQuestions = $this->gameObject->gameQuestions()->where('answered_by', $user->id)->get();
            $answeredQuestions->load('question.difficulty');
            $secs = 0;
            foreach ($answeredQuestions as $answeredQuestion) {
                $answeredQuestionsScore += $answeredQuestion->question->difficulty->score;
                $date = Carbon::parse($answeredQuestion->answered_at);
                $secondsDifference = $date->diffInSeconds(Carbon::now());
                $secs += $secondsDifference;
            }
            $user->experience += $answeredQuestionsScore;
            $user->save();
            $scores[] = [
                'name' => $user->name,
                'avatar' => $user->avatar_path,
                'score' => $answeredQuestionsScore,
                'time' => $secs,
                'id' => $user->id,

            ];
            $user->score = $answeredQuestionsScore;
            $user->coins = $user->coins + Setting::where('key', 'coins_per_game')->first()->value;
            $user->save();
        }
        //sort scores based on score and time
        usort($scores, function ($a, $b) {
            if ($a['score'] == $b['score']) {
                return $a['time'] - $b['time'];
            }
            return $b['score'] - $a['score'];
        });
        $winners = $scores;

        return [
            'game' => $this->gameObject,
            'winners' => $winners,
        ];
    }
}
