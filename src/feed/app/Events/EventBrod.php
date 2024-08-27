<?php 
    namespace App\Events;
    use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
    class EventBrod extends Event implements ShouldBroadcast
    {
        
        public $message;

        public function __construct($message)
        {
            $this->message = $message;
        }

        public function broadcastOn()
        { 
            return ['create:blog'];
        }
    }
?>