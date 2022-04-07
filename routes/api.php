<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CDRController;

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

Route::post('auth/login', [AuthController::class, 'signin']);
Route::post('auth/register', [AuthController::class, 'signup']);
Route::post('getCdrs', [CDRController::class, 'getCdrs']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('auth/updateUser',[AuthController::class, 'updateUser']);
    Route::post('auth/logout', [AuthController::class, 'logout']); 
    Route::post('uploadFile', [CDRController::class, 'uploadFile']);
    Route::post('getCdrCalls', [CDRController::class, 'getCdrCalls']);
});

