<?php

//use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
//use App\Http\Controllers\Admin\Main\IndexController as IndexController;
use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\Upa\Helper\IndexController;
//use App\Http\Controllers\HomeController;
//use App\Http\Controllers\Upa\Control\IndexController;
//use App\Http\Controllers\Main\IndexController as IndexController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['namespase' => 'Start'], function () {
    Route::get('/', \App\Http\Controllers\Start\IndexController::class);
});

Route::group(['namespace' => 'App\Http\Controller\Upa', 'prefix' => 'upa', 'middleware' => ['auth']], function () {
    Route::group(['namespace' => '\App\Http\Controllers\Upa\Main'], function () {
        Route::get('/', \App\Http\Controllers\Upa\Main\IndexController::class)->name('upa.main.index');
    });

    Route::group(['namespace' => '\App\Http\Controllers\Upa\Comment', 'prefix' => 'comments'], function () {
        Route::get('/', \App\Http\Controllers\Upa\Comment\IndexController::class)->name('upa.comment.index');
    });

    Route::group(['namespace' => '\App\Http\Controllers\Upa\Control', 'prefix' => 'controls'], function () {
        Route::get('/', \App\Http\Controllers\Upa\Control\IndexController::class)->name('upa.control.index');
    });

    Route::group(['namespace' => '\App\Http\Controllers\Upa\Leader', 'prefix' => 'leaders'], function () {
        Route::get('/', \App\Http\Controllers\Upa\Leader\IndexController::class)->name('upa.leader.index');
    });

    Route::group(['namespace' => '\App\Http\Controllers\Upa\Journal', 'prefix' => 'journals'], function () {
        Route::get('/', \App\Http\Controllers\Upa\Journal\IndexController::class)->name('upa.journal.index');
    });

    Route::group(['namespace' => '\App\Http\Controllers\Upa\Motivation', 'prefix' => 'motivations'], function () {
        Route::get('/', \App\Http\Controllers\Upa\Motivation\IndexController::class)->name('upa.motivation.index');
    });

    Route::group(['namespace' => '\App\Http\Controllers\Upa\Helper', 'prefix' => 'helpers'], function () {
        Route::get('/', \App\Http\Controllers\Upa\Helper\IndexController::class)->name('upa.helper.index');
    });
});

Auth::routes();
