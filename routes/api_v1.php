<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\ApiController;

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

Route::get('/get-asr-list', [ApiController::class, 'getAsrList']);
Route::get('/get-asr-entry', [ApiController::class, 'getAsrEntry']);
Route::get('/asr/{idCommutation}', [ApiController::class, 'getAsrResult']);
Route::post('/update-asr-entry', [ApiController::class, 'updateAsrEntry']);
Route::post('/export', [ApiController::class, 'exportData']);
