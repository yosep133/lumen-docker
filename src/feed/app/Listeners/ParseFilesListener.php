namespace App\Listeners;

use App\Events\ParseFilesEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ParseFilesNoti;
<!-- use App\User; -->

class ParseFilesListener
{
    public function __construct(){

    }

    public function handle(ParseFileEvent $event)
    {
        Notification::send()
    }
}