<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class PlayerJoined implements ShouldBroadcast
{
    use SerializesModels;

    public $user;
    public $gameObject;

    public function __construct($user,$gameObject)
    {
        $this->user = $user;
        $this->gameObject = $gameObject;
    }

    public function broadcastOn()
    {
        return new Channel('games-join-'.$this->gameObject->id);
    }

    public function broadcastWith()
    {
        return [
            'game' => $this->gameObject,
            'user' => $this->user
        ];
    }
}
