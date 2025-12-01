<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Employee\AuthController;
// use App\Http\Controllers\Api\Staff\ScanController;
// use App\Http\Controllers\Api\Staff\PurchaseOrderController;
// use App\Http\Controllers\Api\Staff\PackingslipController;
// use App\Http\Controllers\Api\Staff\ReturnSpareController;


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
Route::prefix('employee')->name('employee.')->group(function(){
    Route::post('/login', [AuthController::class,'login'])->name('login');
    Route::post('logout', [AuthController::class,'logout'])->name('logout');
});