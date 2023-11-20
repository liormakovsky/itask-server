<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\TasksController;

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
Route::post('auth/updateUser',[AuthController::class, 'updateUser']);
Route::get('tasks/{userId}', [TasksController::class, 'index']);
Route::resource('tasks', TasksController::class);


