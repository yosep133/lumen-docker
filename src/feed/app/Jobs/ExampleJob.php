<?php

namespace App\Jobs;

use App\Services\FeedServices;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;

class ExampleJob extends Job implements ShouldQueue
{
    public FeedServices $feedService;
    public Request $request;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->feedService->allProcess($this->request);
    }

}
