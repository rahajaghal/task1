<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\ClientAuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserAuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::controller(UserAuthController::class)
    ->prefix('user')
    ->group(function (){
        Route::post('register','register');
        Route::post('login', 'login');
        Route::post('logout','logout');
    });
Route::controller(AdminAuthController::class)
    ->prefix('admin')
    ->group(function (){
        Route::post('register','register');
        Route::post('login', 'login');
        Route::post('logout','logout');
    });
Route::controller(ClientAuthController::class)
    ->prefix('client')
    ->group(function (){
        Route::post('register','register');
        Route::post('login', 'login');
        Route::post('logout','logout');
    });
Route::controller(PostController::class)
    ->prefix('post')
    ->group(function (){
        Route::post('store','store')->middleware('auth:client');
        Route::post('update/{id}','update')->middleware('auth:client');
        Route::get('delete/{id}','delete')->middleware('auth:client');
        Route::get('approve/{id}','approve')->middleware('auth:client');
        Route::get('get/approved','getApproved');
        Route::get('search','search');

    });
