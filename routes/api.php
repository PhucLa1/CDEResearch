<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ToDoController;

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

//Auth
Route::post('signup',[AuthController::class,'Register']);
Route::post('login',[AuthController::class,'Login']);
Route::get('checkRole',[AuthController::class,'checkRole']);

//CRUD
//Tag
Route::prefix('tag')->group(function(){
    Route::get('/{project_id}',[TagController::class,'index']);
    Route::get('/{id}',[TagController::class,'show']);
    Route::post('/',[TagController::class,'store']);
    Route::put('/{id}/{project_id}',[TagController::class,'update']);
    Route::delete('/{id}/{project_id}',[TagController::class,'destroy']);
    Route::delete('/removeAll/{project_id}',[TagController::class,'removeAll']);
})->middleware('auth:api');
Route::delete('removeAllTag',[TagController::class,'removeAll'])->middleware('auth:api');
Route::apiResource('todo',ToDoController::class)->middleware('auth:api');
Route::apiResource('project',ProjectController::class)->middleware('auth:api');