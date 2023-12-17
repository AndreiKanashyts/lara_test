<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V2\ApiController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/get-assessments', [ApiController::class, 'getAssessments']);
Route::get('/check-list', [ApiController::class, 'getAssessmentCheckList']);
Route::get('/badge', [ApiController::class, 'getBadge']);
Route::get('/comment', [ApiController::class, 'getAssessmentComment']);
Route::get('/file', [ApiController::class, 'getFileObject']);
Route::get('/linked-object', [ApiController::class, 'getLinkedObject']);
Route::get('/get-wiu-list', [ApiController::class, 'getWiuList']);
Route::get('/get-wiu', [ApiController::class, 'getWiu']);
Route::post('/status', [ApiController::class, 'setAssessmentStatus']);
Route::post('/add-comment', [ApiController::class, 'addComment']);
Route::post('/wiu-employee-familiarize', [ApiController::class, 'setWiuEmployeeFamiliarize']);