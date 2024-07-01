<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\WalletController;
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
Route::post('login',[AuthController::class ,'login']);
Route::post('register',[AuthController::class ,'register']);
Route::group(['prefix' => ''], function() {
    Route::post('createWallet',[WalletController::class ,'createWallet']);
    Route::get('listWallet',[WalletController::class ,'listWallet']);
    Route::post('balance' ,[WalletController::class,'balance']);
    Route::post('transaction' ,[WalletController::class,'transaction']);
    Route::post('transactionhash' ,[WalletController::class,'transactionHash']);


});


