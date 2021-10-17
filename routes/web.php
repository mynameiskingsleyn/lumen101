<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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



$router->group(['prefix'=>'api'], function () use ($router) {
    $router->post('/login', 'AuthController@login');
    $router->post('/register', 'AuthController@register');

    $router->get('/posts', ['as'=>'posts','uses'=>'PostController@index']);
    $router->post('/create_post', ['as'=>'createPost','uses'=>'PostController@store']);
    $router->put('/post/{id}', ['as'=>'updatePost','uses'=>'PostController@update']);
    $router->delete('/post/{id}', ['as'=>'destroyPost','uses'=>'PostController@destroy']);
});
