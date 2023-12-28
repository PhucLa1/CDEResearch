<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ToDoController;
use App\Models\ToDo;

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

Route::post('signup',[AuthController::class,'Register']);
Route::post('login',[AuthController::class,'Login']);

//CRUD
Route::apiResource('tag',TagController::class)->middleware('auth:api');
Route::prefix('todo')->group(function(){
    Route::get('/{project_id}',[ToDoController::class,'index']);
    Route::post('/',[ToDoController::class,'store']);
    Route::put('/',[ToDoController::class,'update']);
    Route::delete('/',[ToDoController::class,'destroy']);
});
Route::post('/folder',[FolderController::class,'store'])->middleware('auth:api');