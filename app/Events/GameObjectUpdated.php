<?php
namespace App\Events;

use App\Http\Resources\GameCollection;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class GameObjectUpdated implements ShouldBroadcast
{
    use SerializesModels;

    public $gameObject  ;

    public function __construct($gameObject)
    {
        $this->gameObject = $gameObject;
    }

    public function broadcastOn()
    {
        return new Channel('GameUpdated');
    }

    public function broadcastWith()
    {
        return ['game'=>GameCollection::make($this->gameObject)];
    }
}
