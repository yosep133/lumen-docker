<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class SendMessage extends Event implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;
    public $data = ['asas'];

    public $message ;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function broadcastAs(){
        return 'UserEvent';
    }

    public function broadcastWith(){
        return $this->message;//['title'=>'This notification from it solution'];
    }

    public function broadcastOn()
    {
        // return new Channel('user-channel');
        return new Channel('create:blog');
    }
    
}


?>