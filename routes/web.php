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




$router->group(['prefix'=>'api'], function() use($router){

  $router->group(['middleware'=>'auth'], function() use($router){
    $router->get('/posts',['as'=>'posts','uses'=>'PostController@index']);
    $router->post('/create_post',['as'=>'createPost','uses'=>'PostController@store']);
    $router->put('/post/{id}',['as'=>'updatePost','uses'=>'PostController@update']);
    $router->delete('/post/{id}',['as'=>'destroyPost','uses'=>'PostController@destroy']);
    $router->post('/logout', ['as'=>'logout', 'uses'=>'AuthController@logout']);
  });

  $router->post('/login', ['as'=>'login', 'uses'=>'AuthController@login']);
  $router->post('/register', ['as'=>'register', 'uses'=>'AuthController@register']);

});
