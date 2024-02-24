<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\OfficerController;
use App\Http\Controllers\RentController;
use App\Http\Controllers\UserController;
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

// NOT AUTH NOT AUTH NOT AUTH
Route::get('/book', [BookController::class, 'index']);
Route::get('/book/{slug}', [BookController::class, 'detail']);

Route::get('/type', [BookController::class, 'getType']);
Route::get('/type/{slug}', [BookController::class, 'detailType']);

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::get('/test', [AdminController::class, 'test']);
// AUTH AUTH AUTH AUTH

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user-update/{slug}', [UserController::class, 'update']);
    Route::put('/user-changePass/{slug}', [UserController::class, 'changePass']);


    Route::post('/favorite', [BookController::class, 'fav']);

    // RENT LOGS STATUS:
    // 1. NEED VERIFICATION
    // 2. VERIFIED
    // 3. RETURNED
    // 4. CANCELED

    // VIOLATION STATUS:
    // 5. OVERDUE
    // 6. RETURNED OVERDUE
    // 7. BROKEN

    // MAKE NEW RENT LOGS => STATUS == 'NEED VERIFICATION'
    Route::get('/my-rent/{code}', [RentController::class, 'getOneMyRent']);
    Route::get('/my-rent', [RentController::class, 'getAllMyRent']);
    Route::get('/my-rent-normal', [RentController::class, 'getMyNormalRent']);
    Route::get('/my-rent-violation', [RentController::class, 'getMyViolationRent']);

    Route::get('/rent-needVerification', [RentController::class, 'getViolationRent']);
    Route::get('/rent-normal', [RentController::class, 'normalRent']);
    Route::get('/rent-violation', [RentController::class, 'violationRent']);
    Route::post('/rent', [RentController::class, 'newRent']);

    // CHANGE STATUS RENT LOGS FROM 'NEED VERIFICATION' TO 'CANCELED'
    Route::post('/rent-cancel/{code}', [RentController::class, 'cancelRent']);

    Route::post('/rent-review/{code}', [RentController::class, 'rentReview']);
    Route::put('/rent-review/{code}', [RentController::class, 'updateReview']);
    Route::delete('/rent-review/{code}', [RentController::class, 'deleteReview']);

    Route::middleware('libManager')->group(function () {
        Route::prefix('/libManager')->group(function () {


            // RENT RENT RENT RENT
            // CHANGE STATUS RENT LOGS FROM 'NEED VERIFICATION' TO 'VERIFIED'
            Route::post('/rent-verify/{code}', [RentController::class, 'verifyRent']);

            // CHANGE STATUS RENT LOGS FROM 'VERIFIED' TO 'RETURNED' OR VIOLATION STATUS
            Route::post('/rent-return/{code}', [RentController::class, 'returnRent']);


            // VIOLATION RENT LOGS STATUS
            // CHANGE STATUS RENT LOGS FROM 'VERIFIED' TO 'BROKEN'
            Route::post('/rent-violation/{code}', [RentController::class, 'violationRent']);





            // USER USER USER USER
            Route::get('/user', [OfficerController::class, 'getUser']);
            Route::post('/user', [OfficerController::class, 'addUser']);
            Route::put('/user/{slug}', [OfficerController::class, 'editUser']);
            Route::delete('/user/{slug}', [OfficerController::class, 'delUser']);


            // BOOK BOOK BOOK BOOK
            Route::post('/book', [BookController::class, 'store']);
            Route::put('/book/{slug}', [BookController::class, 'update']);
            Route::delete('/book/{slug}', [BookController::class, 'delete']);
        });
    });
});
