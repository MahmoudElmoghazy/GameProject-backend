<?php
namespace App\Events;

use App\Http\Resources\GameCollection;
use App\Http\Resources\UserCollection;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class PlayerLeft implements ShouldBroadcast
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
        return new Channel('games-left-'.$this->gameObject->id);
    }

    public function broadcastWith()
    {
        return [
            'game' => GameCollection::make($this->gameObject),
            'user' =>  UserCollection::make($this->user)
        ];
    }
}
