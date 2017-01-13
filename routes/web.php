<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', ['as' => 'home', 'uses' => 'ArticleController@index']);

Route::get('/publish', ['as' => 'publish', 'uses' => 'ArticleController@publish']);
Route::post('/publish', ['as' => 'publish.save', 'uses' => 'ArticleController@store']);
Route::get('/profile', ['as' => 'user.profile', 'uses' => 'UserController@profile']);

Route::get('/articles/{slug}', ['as' => 'article.details', 'uses' => 'ArticleController@details']);
Route::get('/articles/download/{slug}', ['as' => 'article.download', 'uses' => 'ArticleController@download']);
Route::get('/articles/delete/{slug}', ['as' => 'article.delete', 'uses' => 'ArticleController@delete']);
Route::get('/feed', ['as' => 'article.feed', 'uses' => 'ArticleController@feed']);

Auth::routes();

Route::get('/register/email-confirmation/{token}', ['as' => 'register.email.verification', 'uses' => 'Auth\RegisterController@emailVerification']);
Route::post('/register/final-step', ['as' => 'register.final.step', 'uses' => 'Auth\RegisterController@finalizedRegistration']);