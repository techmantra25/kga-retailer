<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Dealer\AuthController;
use App\Http\Controllers\Api\Dealer\ServiceController;

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

# Auth
Route::prefix('auth')->name('auth.')->group(function(){
    Route::post('login', [AuthController::class,'login'])->name('login');
    Route::post('logout', [AuthController::class,'logout'])->name('logout');
});
# Service
Route::prefix('service')->name('service.')->group(function(){
    Route::post('create', [ServiceController::class,'create'])->name('create');
    Route::post('upload-snapshot', [ServiceController::class,'upload_snapshot'])->name('upload-snapshot');
    Route::post('search-product', [ServiceController::class,'search_product'])->name('search-product');
});
