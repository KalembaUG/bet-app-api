<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApiRootController;

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

// Public routes
Route::get('/', [ApiRootController::class, 'endpoints']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/signup', [AuthController::class, 'signup']);


// Protected routes - require tokens
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/exams', [ExamController::class, 'examsIndex']);
    Route::get('/exams/{id}', [ExamController::class, 'show']);
    Route::get('/exams/search/{name}', [ExamController::class, 'search']);
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{id}/exams', [UserController::class, 'userExams']);
    Route::post('/exams', [ExamController::class, 'store']);
    Route::put('/exams/{id}', [ExamController::class, 'update']);
    Route::delete('/exams/{id}', [ExamController::class, 'destroy']);
    Route::get('/logout/{id}', [AuthController::class, 'logout']);
});