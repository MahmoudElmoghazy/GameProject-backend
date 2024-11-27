<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MemesController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\QuickChatController;
use App\Http\Controllers\RegisterController;
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

Route::middleware('guest')->group( function (){
    Route::controller(RegisterController::class)->group(function(){
        Route::post('register', 'register');

        Route::post('activate/email', 'activateEmail');
    });

    Route::controller(LoginController::class)->group(function(){
        Route::post('login', 'login');
        Route::post('/forget-password',  'forgetPassword');
        Route::post('/reset-password', 'resetPassword');
    });

});
Route::middleware('auth:sanctum')->group( function () {
    Route::controller( GameController::class)->group(function(){
        Route::get('/games', 'list');
        Route::post('/games/create', 'create');
    });
    Route::controller( GameController::class)->group(function(){
        Route::get('/games', 'list');
        Route::post('/games/create', 'create');
    });
    Route::controller( MemesController::class)->group(function(){
        Route::get('/memes', 'list');
    });
    Route::controller( QuickChatController::class)->group(function(){
        Route::get('/quick-chat', 'list');
    });
    Route::controller( PurchaseController::class)->group(function(){
        Route::post('/purchase', 'purchase');
        Route::post('/purchase/coins', 'purchaseCoins');
        Route::post('/subscribe/video/package', 'subscribe');
    });

});
