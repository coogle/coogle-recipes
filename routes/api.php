<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::resource('ingredients', '\App\Http\Controllers\Api\IngredientsController');

Route::get('recipes/search', [
    'as' => 'recipes.search',
    'uses' => '\App\Http\Controllers\Api\RecipeController@search'
]);

Route::resource('recipes', '\App\Http\Controllers\Api\RecipeController', [
    'only' => ['index', 'show']
]);

Route::any('recipes/favorite/{id}', [
    'as' => 'recipes.favorite',
    'uses' => '\App\Http\Controllers\Api\RecipeController@favorite'
]);

Route::any('recipes/unfavorite/{id}', [
    'as' => 'recipes.favorite',
    'uses' => '\App\Http\Controllers\Api\RecipeController@unfavorite'
]);

Route::any('recipes/mirror-callback/{id}', [
    'as' => 'recipes.mirror-callback',
    'uses' => '\App\Http\Controllers\Api\RecipeController@mirrorCallback'
])->where('id', '[0-9]+');


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');
