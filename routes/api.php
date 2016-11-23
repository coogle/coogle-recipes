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


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');
