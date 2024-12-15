<?php
namespace App\Events;

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
        return new Channel('games-finished-'.$this->gameObject->id);
    }

    public function broadcastWith(): array
    {
        return [
            'game' => $this->gameObject
        ];
    }
}
