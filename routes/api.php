<?php

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

use App\Http\Middleware\Authenticate;

$router->group(['prefix' => Authenticate::AUTH_SERVICE], function () use ($router) {

    $router->post('/signin', ['uses' => 'AuthController@signIn']);
    $router->post('/signup', ['uses' => 'AuthController@signUp']);

    $router->get('/users', 'AuthController@getUser');

    $router->post('/profile_images', 'ProfileImageController@create');

    $router->post('/test', ['uses' => 'AuthController@test']);


});



