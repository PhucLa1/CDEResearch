<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\JoinController;
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
Route::get('checkRole/{project_id}',[AuthController::class,'checkRole'])->middleware('auth:api');

//CRUD
//Tag
Route::prefix('tag')->group(function(){
    Route::get('showAll/{project_id}',[TagController::class,'index'])->middleware('auth:api');
    Route::get('/{id}',[TagController::class,'show'])->middleware('auth:api');
    Route::post('/',[TagController::class,'store'])->middleware('auth:api');
    Route::put('/{id}/{project_id}',[TagController::class,'update'])->middleware('auth:api');
    Route::delete('/{id}/{project_id}',[TagController::class,'destroy'])->middleware('auth:api');
    Route::delete('/removeAll/{project_id}',[TagController::class,'removeAll'])->middleware('auth:api');
});

//Teams join
Route::prefix('teams')->group(function(){
    Route::get('/{project_id}',[JoinController::class,'index'])->middleware('auth:api');
    Route::get('join/{project_id}/{user_id}',[JoinController::class,'AcceptRequest']);
    Route::post('/sendEmail',[JoinController::class,'SendEmail'])->middleware('auth:api');

});


Route::apiResource('todo',ToDoController::class)->middleware('auth:api');
Route::apiResource('project',ProjectController::class)->middleware('auth:api');