<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/user/register',[
	'as' => 'register_form',
	'uses' => 'UserController@create'
]);
Route::post('/api/user/register',[
	'as' => 'register',
	'uses' => 'UserController@store'
]);

Route::post('/api/user/login',[
	'as' => 'login',
	'uses' => 'UserController@index'
]);

Route::get('/api/user/login',[
	'as' => 'login_form',
	'uses' => 'UserController@getLogin'
]);

Route::post('/api/user/logout',[
	'as' => 'logout',
	'uses' => 'UserController@logout'
]);

Route::get('/api/user/logout',[
	'as' => 'logout_form',
	'uses' => 'UserController@getLogout'
]);

Route::get('/api/user/pull/{latitude}/{longitude}',[
	'as' => 'pullLocation',
	'uses' => 'UserController@pullLocation'
]);

/////////////////////////////////////////////////////////////////
Route::get('/api/post/shout',[
	'as' => 'post_form',
	'uses' => 'PostController@create'
]);
Route::post('/api/post/shout',[
	'as' => 'post',
	'uses' => 'PostController@store'
]);
Route::get('/api/post/home/{token}', [
	'as' => 'home',
	'uses' => 'PostController@index'
]);
Route::get('/api/post/location/{lat}/{long}', [
	'as' => 'timeline',
	'uses' => 'PostController@show'
]);



