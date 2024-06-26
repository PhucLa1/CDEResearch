<?php

use App\Http\Controllers\ActivitiesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\GoogleLoginController;
use App\Http\Controllers\JoinController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ToDoController;
use App\Http\Controllers\FilesController;
use App\Models\Activities;

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

//Auth - Đã test
Route::post('signup', [AuthController::class, 'Register']);
Route::post('login', [AuthController::class, 'Login']);
Route::get('checkRole/{project_id}', [AuthController::class, 'checkRole'])->middleware('auth:api');
Route::get('check', [AuthController::class, 'check'])->middleware('auth:api');
//Google login



//CRUD
//Tag - đã test
// Activity - Done
Route::prefix('tag')->group(function () {
    Route::get('showAll/{project_id}', [TagController::class, 'index'])->middleware('auth:api');
    Route::get('/{id}', [TagController::class, 'show'])->middleware('auth:api');
    Route::post('/', [TagController::class, 'store'])->middleware('auth:api');
    Route::put('/{id}/{project_id}', [TagController::class, 'update'])->middleware('auth:api');
    Route::delete('/{id}/{project_id}', [TagController::class, 'destroy'])->middleware('auth:api');
    Route::get('/removeAll/{project_id}', [TagController::class, 'removeAll'])->middleware('auth:api');
});

//Teams join - Đẫ test
//Activity - Done
Route::prefix('teams')->group(function () {
    Route::get('/{project_id}', [JoinController::class, 'index'])->middleware('auth:api');
    Route::get('join/{project_id}/{user_id}', [JoinController::class, 'AcceptRequest']);
    Route::post('/sendEmail', [JoinController::class, 'SendEmail'])->middleware('auth:api');
    Route::put('/changeRole/{project_id}/{user_id}/{role}', [JoinController::class, 'updateRole'])->middleware('auth:api');
    Route::delete('/{project_id}/{user_id}', [JoinController::class, 'destroy'])->middleware('auth:api');
});

//Project - Đã test
//Activity - Done
Route::prefix('project')->group(function () {
    Route::get('/', [ProjectController::class, 'index'])->middleware('auth:api');
    Route::post('/', [ProjectController::class, 'store'])->middleware('auth:api');;
    Route::get('/{id}', [ProjectController::class, 'show'])->middleware('auth:api');
    Route::put('/{id}', [ProjectController::class, 'update'])->middleware('auth:api');
    Route::put('/changePermiss/{id}', [ProjectController::class, 'changePermission'])->middleware('auth:api');
    Route::delete('/{id}', [ProjectController::class, 'destroy'])->middleware('auth:api');
});

//Folder - Đang làm(Da Hoan Thanh)
//Activity - Done
Route::prefix('folder')->group(function () {
    Route::get('/{project_id}/{folder_id}', [FolderController::class, 'listFolderAndFiles'])->middleware('auth:api');
    Route::get('/', [FolderController::class, 'listFolderCanMove'])->middleware('auth:api');
    Route::get('/{id}', [FolderController::class, 'show'])->middleware('auth:api');
    Route::post('/', [FolderController::class, 'store'])->middleware('auth:api');
    Route::put('/{id}/{option}', [FolderController::class, 'update'])->middleware('auth:api');
    Route::delete('/{id}/{project_id}', [FolderController::class, 'destroy'])->middleware('auth:api');
});

//Files - Đang làm(Đã hoàn thành)
//Activity - Done
Route::prefix('files')->group(function () {
    Route::post('/', [FilesController::class, 'store'])->middleware('auth:api');
    Route::get('/history/{first_version}', [FilesController::class, 'historyOfFiles'])->middleware('auth:api');
    Route::get('/dowload/{id}/{project_id}', [FilesController::class, 'dowload'])->middleware('auth:api');
    Route::put('/{id}/{option}', [FilesController::class, 'update'])->middleware('auth:api');
    Route::delete('/{id}/{project_id}', [FilesController::class, 'deleteByPermis'])->middleware('auth:api');
    Route::get('/{id}/{option}', [FilesController::class, 'show'])->middleware('auth:api');
});

//Comment - Đang làm(Đã hoàn thành)
//Activity - Done
Route::prefix('comment')->group(function () {
    Route::get('/{type}/{another_id}', [CommentController::class, 'index'])->middleware('auth:api');
    Route::post('/', [CommentController::class, 'store'])->middleware('auth:api');
    Route::put('/{id}/{project_id}', [CommentController::class, 'update'])->middleware('auth:api');
    Route::delete('/{id}/{project_id}', [CommentController::class, 'destroy'])->middleware('auth:api');
    Route::get('/{id}', [CommentController::class, 'show'])->middleware('auth:api');
});


//Todo - Đang làm(Đã hoàn thành)
//Activity - Done
Route::prefix('todo')->group(function () {
    Route::get('/{project_id}', [ToDoController::class, 'index'])->middleware('auth:api');
    Route::post('/', [ToDoController::class, 'store'])->middleware('auth:api');
    Route::put('/{id}/{project_id}', [ToDoController::class, 'update'])->middleware('auth:api');
    Route::delete('/{id}/{project_id}', [ToDoController::class, 'destroy'])->middleware('auth:api');
    Route::get('/{id}', [ToDoController::class, 'show'])->middleware('auth:api');
});

//Activities - Đang làm(Đã hoàn thành)
Route::prefix('activities')->group(function () {
    Route::get('/', [ActivitiesController::class, 'index'])->middleware('auth:api');
    Route::get('/{project_id}', [ActivitiesController::class, 'listAllUserInProject'])->middleware('auth:api');
});
Route::get('/fileUp',[FilesController::class, 'convert']);
