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

Route::get('/recipes/{recipeId}/photo/{dia}', [
    'as' => 'recipes.photo',
    'uses' => '\App\Http\Controllers\RecipeController@photo'
])->where('recipeId', '[0-9]+')
  ->where('dia', '[0-9]+x[0-9]+');

Route::group(['middleware' => ['autologin', 'guest']], function($router) {
    Route::get('/', function () {
        return view('welcome');
    });
});

Auth::routes();

Route::group(['middleware' => 'auth'], function($router) {
    
    $router->resource('recipes', 'RecipeController');
    
    $router->get('/home', [
        'as' => 'home',
        'uses' => 'HomeController@index'
    ]);
    
    $router->get('/recipes/mirror/{id}', [
        'as' => 'recipes.mirror',
        'uses' => 'RecipeController@mirror'
    ]);
    
    $router->get('/search', [
        'as' => 'search',
        'uses' => 'SearchController@search'
    ]);
    
    $router->get('/export', [
        'as' => 'recipes.export',
        'uses' => 'RecipeController@export'
    ]);
    
    $router->get('/import', [
        'as' => 'recipes.import.show',
        'uses' => 'RecipeController@importShow'
    ]);
    
    $router->post('/import', [
        'as' => 'recipes.import',
        'uses' => 'RecipeController@import'
    ]);
    
});


