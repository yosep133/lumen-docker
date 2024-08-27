<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\FeedController;
use App\Service\DailyFeedService;
use FastRoute\Route;
use Illuminate\Support\Facades\Redis;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->get('/version', function () use ($router){
    return $router->app->version();
});

$router->group(['prefix'=> 'api'], function ($router){
    $router->post('login', 'AuthController@login');
    $router->post('logout', 'AuthController@logout');
    $router->post('refresh', 'AuthController@refresh');
    $router->post('user-profile', 'AuthController@me');

    $router->group(['prefix'=>'feed'], function($router){
        $router->post('dailyfeed','FeedController@dailyFeedProcess');
        $router->post('monthlyfeed','FeedController@monthlyFeedProcess');
        $router->post('dailytrans','FeedController@dailyTransProcess');
        $router->post('branchtrans','FeedController@branchTransProcess');
        $router->post('mobiletrans','FeedController@mobileTransProcess');
        $router->post('clienttrans','FeedController@clientTransProcess');
        $router->post('detailtrans','FeedController@detailTransProcess');
        $router->post('negotrans','FeedController@negoTransProcess');
        $router->post('commission','FeedController@commissionProcess');
        $router->post('clientname','FeedController@clientNameProcess');
        $router->post('redis','FeedController@redisProscess');
        $router->post('allProcess','FeedController@allProcess');
        $router->post('getProcessDate','FeedController@getProcessDate');
    }) ;
});




// Route::group([

//     'prefix' => 'api'

// ], function ($router) {
//     Route::post('login', 'AuthController@login');
//     Route::post('logout', 'AuthController@logout');
//     Route::post('refresh', 'AuthController@refresh');
//     Route::post('user-profile', 'AuthController@me');

// });