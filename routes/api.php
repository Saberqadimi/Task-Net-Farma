<?php

use App\Http\Controllers\Api\V1\ArticleController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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




//auth routes
Route::post('v1/user-register', [AuthController::class, 'register']);
Route::post('v1/user-login', [AuthController::class, 'login']);
//User Controller
// Route::resource('v1/users', [UserController::class]);
Route::group(['prefix' => 'v1/users',], function () {

    Route::get('/', [UserController::class, 'index'])->name('api.user.index');
    Route::post('/create', [UserController::class, 'store'])->name('api.user.store');
    Route::get('/{user}', [UserController::class, 'show'])->name('api.user.single');
    Route::patch('/update/{user}', [UserController::class, 'update'])->name('api.user.update');
    Route::delete('/delete/{user}', [UserController::class, 'destroy'])->name('api.user.delete');
});


//Articles Controller

Route::group(['prefix' => 'v1/articles', 'middleware' => ['auth:api']], function () {

    Route::get('/', [ArticleController::class, 'index'])->name('api.post.index');
    Route::post('/create', [ArticleController::class, 'store'])->name('api.post.store');
    Route::get('/{article}', [ArticleController::class, 'show'])->name('api.post.single');
    Route::patch('/update/{article}', [ArticleController::class, 'update'])->name('api.post.update');
    Route::delete('/delete/{article}', [ArticleController::class, 'destroy'])->name('api.post.delete');
});
