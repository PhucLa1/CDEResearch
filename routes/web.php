<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleLoginController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
// Route::group(['middleware' => 'web'], function () {
//     
// });
Route::group(['middleware' => 'web'], function () {
    Route::get('/google/redirect', [GoogleLoginController::class, 'redirectToGoogle']);
    Route::get('auth/google/callback', [GoogleLoginController::class, 'handleGoogleCallback']);
});