<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\UserController;
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

/**
 * This application created by 0x17 
 * Github username  ashrafali23
 */



Route::middleware(['lang', 'api_password'])->group(function () {
    Route::post('/login', [AuthController::class, 'login']);



    // Admin Dashboard
    Route::middleware(['auth:sanctum', 'abilities:admin-access'])->group(function () {


        //  Users CRUD
        Route::apiResource('/user', UserController::class);
        Route::put('/change-password/{id}', [UserController::class, 'updatePassword']);


        //  Category CRUD
        Route::apiResource('/category', CategoryController::class);


        //  Company CRUD
        Route::apiResource('/company', CompanyController::class);


        // Events CRUD
        Route::apiResource('/event', EventsController::class);

        //  Blogs CRUD
        Route::apiResource('/blog', BlogController::class);

        // Food CRUD
        Route::apiResource('/food', FoodController::class);







        Route::post('/logout', [AuthController::class, 'logout']);
    });
});