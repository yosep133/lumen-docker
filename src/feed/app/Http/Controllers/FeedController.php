<?php

namespace App\Http\Controllers;

use App\Jobs\ExampleJob;
use App\Jobs\AllProcessJob;
use Illuminate\Support\Facades\Route;
use App\Services\FeedServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;


class FeedController extends Controller{

    public $feedService;

    public function __construct(FeedServices $feedService)
    {
        $this->feedService = $feedService;
        // $this->middleware('auth:api');
    }
 
    public function allProcess(Request $request){
        // $queue = Queue::push('AllProcessJob', $this->feedService, $request);
        // echo 'test '.$request->mydate;
        // $varDate = $this->feedService->varDate($request->mydate);
        $args =[
            "request"=>$request->all(),
            "feedservice"=>$this->feedService
        ];
        dispatch(new AllProcessJob($this->feedService,$request->all()));
        // return response()
        // ->json(['processAlert' => 'Virat Gandhi', 'state' => $varDate['tglmysql']]);
    }
    //process satu
    public function dailyFeedProcess(Request $request)
    {
        $this->feedService->porcessSatu($request);
    }
    //process dua
    public function monthlyFeedProcess(Request $request)
    {
        $this->feedService->porcessDua($request);
    }

    public function dailyTransProcess(Request $request)
    {
        $this->feedService->processDailyTrans($request);
    }

    public function branchTransProcess(Request $request)
    {
        $this->feedService->processBranchTrans($request);
    }

    public function mobileTransProcess(Request $request)
    {
        $this->feedService->processMobileTrans($request);
    }

    public function detailTransProcess(Request $request)
    {
        $this->feedService->processDetailTrans($request);
    }

    public function negoTransProcess(Request $request)
    {
        $this->feedService->processNegoTrans($request);
    }

    public function clientTransProcess(Request $request)
    {
        $this->feedService->processClientTrans($request);
    }

    public function commissionProcess(Request $request)
    {
        $this->feedService->processKomisiInterest($request);
    }

    public function clientNameProcess(Request $request)
    {
        $this->feedService->processClientName($request);
    }

    public function redisProscess(Request $request){
        $blog = $request->input('blog');
        echo 'redis_process';
        // Redis::publish('create:blog',json_encode($blog));
        // Event::fire(new EventBrod($blog));
        // $message = ['message','event published'];
        Event(new \App\Events\SendMessage($blog));
    
        array_push($blog);
        return response()->json($blog);
    }

    public function getProcessDate(Request $request){
        $result = $this->feedService->getProgress($request->mydate);
        
        return response()
        ->json([ 'data' => $result]);
    }
}