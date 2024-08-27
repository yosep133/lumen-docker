namespace App\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Support\Carbon;

class ParseFilesEvent extends Event implements ShouldBroadcast
{

    public $message;

    public $user_id;

    public $noti_time;

    public $noti_type;

    public function __construct($user_id, $message, $noti_type=1){

        $this->user_id = $user_id;
        $this->message = $message;
        $this->noti_type = $noti_type;
        $this->noti_time = Carbon::now()->format('Y-m-d H:i:s');
    }

    public function broadcsatOn()
    {
        return new Channel('parsenoti.'.$this->user_id);
    }
}