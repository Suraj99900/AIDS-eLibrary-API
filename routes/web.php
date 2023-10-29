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

$router->group(['prefix' => 'api',['middleware' => 'ClientAuth' ]], function () use ($router) {
    //! User authentication ....
    $router->post('register', ['uses' => 'AidsUserController@addUser']);
    $router->put('register', ['uses' => 'AidsUserController@updateUser']);
    $router->get('login', ['uses' => 'AidsUserController@loginUser']);
    $router->get('semester', ['uses' => 'SemesterController@getAllSemester']);

    // upload Sestion API
    $router->post('upload', ['uses' => 'AIDSUploadController@addUploadData']);
    $router->get('fetch/book', ['uses' => 'AIDSUploadController@getUploadedBook']);
    $router->get('download', ['uses' => 'AIDSUploadController@downloadFile']);
});
