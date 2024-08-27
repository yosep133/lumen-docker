<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Services\FeedServices;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Symfony\Contracts\Service\Attribute\Required;
use Throwable;

class AllProcessJob extends Job implements ShouldQueue
{

    public FeedServices $feedService;
    public Request $request;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(FeedServices $feedService,$all)
    {
        $this->feedService = $feedService;
        $this->request = new \Illuminate\Http\Request($all);
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

    public function failed(Throwable $exception) : void {
        echo '-- '.$exception;
    }

    public function fire($job,FeedServices $feedservice,Request $request) {
        $feedservice->allProcess($request);
        $job->delete();
    }
}
