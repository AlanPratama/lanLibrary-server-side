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

Route::get('/writer', [BookController::class, 'getAllWriter']);
Route::get('/writer/{slug}', [BookController::class, 'getOneWriter']);

// SIDE DISH OF BOOK
Route::get('/side-dish-book', [BookController::class, 'sideDishBook']);


Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::get('/test', [AdminController::class, 'test']);
// AUTH AUTH AUTH AUTH

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::get('/user/{slug}', [UserController::class, 'getOneUser']);

    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::put('/user-update/{slug}', [UserController::class, 'update']);
    Route::put('/user-changePass/{slug}', [UserController::class, 'changePass']);


    Route::post('/favorite', [BookController::class, 'fav']);

    // START OF RENTLOGS:
    // 1. NEED VERIFICATION
    // 2. CANCELED

    // WHERE RENTLOGS ISACTIVE OR NOTACTIVE:
    // 3. VERIFIED
    // 4. RETURNED

    // RENTLOGS VIOLATION STATUS:
    // 5. RETURNED OVERDUE
    // 6. BROKEN
    // 7. MISSING

    // COMBO RENTLOGS VIOLATION STATUS:
    // 8. BROKEN & OVERDUE
    // 9. MISSING & OVERDUE

    Route::get('/my-rent/{code}', [RentController::class, 'getOneMyRent']);
    Route::get('/my-rent', [RentController::class, 'getAllMyRent']);
    Route::get('/my-rent-normal', [RentController::class, 'getMyNormalRent']);
    Route::get('/my-rent-violation', [RentController::class, 'getMyViolationRent']);

    Route::get('/my-rent-overdue', [RentController::class, 'getViolationRent']);
    Route::get('/my-rent-normal', [RentController::class, 'normalRent']);
    Route::get('/my-rent-violation', [RentController::class, 'violationRent']);

    // MAKE NEW RENT LOGS => STATUS == 'NEED VERIFICATION'
    Route::post('/rent', [RentController::class, 'newRent']);

    // CHANGE STATUS RENT LOGS FROM 'NEED VERIFICATION' TO 'CANCELED'
    Route::post('/rent-cancel/{code}', [RentController::class, 'cancelRent']);


    // REVIEW RENT || REVIEW RENT || REVIEW RENT || REVIEW RENT || REVIEW RENT || REVIEW RENT || REVIEW RENT
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


            // GET RENTLOGS DATA
            Route::get('/rentlogs', [RentController::class, 'aGetAllRent']);
            Route::get('/rentlogs/need-verification', [RentController::class, 'aGetNVRent']);
            Route::get('/rentlogs/verified', [RentController::class, 'aGetVerifiedRent']);
            Route::get('/rentlogs/returned', [RentController::class, 'aGetReturnedRent']);


            Route::get('/rentlogs/violation', [RentController::class, 'aGetAllRentVio']);





            // USER USER USER USER
            Route::get('/user', [OfficerController::class, 'getUser']);
            Route::post('/activing-user/{slug}', [OfficerController::class, 'activingUser']);
            Route::post('/user', [OfficerController::class, 'addUser']);
            Route::post('/user/{slug}', [OfficerController::class, 'editUser']);
            Route::delete('/user/{slug}', [OfficerController::class, 'delUser']);

            Route::post('/change-pass-user/{slug}', [OfficerController::class, 'changePassUser']);
            Route::get('/user-get-pass/{slug}', [OfficerController::class, 'getPassUser']);

            // BOOK BOOK BOOK BOOK
            Route::post('/book', [BookController::class, 'add']);
            Route::post('/book/{slug}', [BookController::class, 'edit']);
            Route::delete('/book/{slug}', [BookController::class, 'delete']);

            Route::post('/writer', [BookController::class, 'addWriter']);
            Route::put('/writer/{slug}', [BookController::class, 'editWriter']);
            Route::delete('/writer/{slug}', [BookController::class, 'delWriter']);


        });
    });
});
