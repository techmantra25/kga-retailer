<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServiceCentre\AuthController;
use App\Http\Controllers\Api\ServiceCentre\BranchController;
use App\Http\Controllers\Api\ServiceCentre\ServiceController;
use App\Http\Controllers\Api\ServiceCentre\ScanController;


/*
|--------------------------------------------------------------------------
| Service Centre API Routes
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
# Branch
Route::prefix('branch')->name('branch.')->group(function(){
    Route::get('list', [BranchController::class,'list'])->name('list');
     
});
# Service
Route::prefix('services')->name('services.')->group(function(){
    Route::get('list', [ServiceController::class,'list'])->name('list');
    Route::post('receive-items', [ServiceController::class,'receive_items'])->name('receive-items');
    Route::get('search-item-spares', [ServiceController::class,'search_item_spares'])->name('search-item-spares');
    Route::post('set-spares', [ServiceController::class,'set_spares'])->name('set-spares');
    Route::post('complete-repair', [ServiceController::class,'complete_repair'])->name('complete-repair');
    Route::get('repaired-list', [ServiceController::class,'repaired_list'])->name('repaired-list');
    Route::post('return-items', [ServiceController::class,'return_items'])->name('return-items');

    Route::post('set-customer-calling-status', [ServiceController::class,'set_customer_calling_status'])->name('set-customer-calling-status');
    Route::post('request-repairing-otp', [ServiceController::class,'request_repairing_otp'])->name('request-repairing-otp');
    Route::post('validate-repairing-otp', [ServiceController::class,'validate_repairing_otp'])->name('validate-repairing-otp');
    Route::get('repairable-items', [ServiceController::class,'repairable_items'])->name('repairable-items');
    
});
# Scan
Route::prefix('scan')->name('scan.')->group(function(){
    Route::post('goods-in', [ScanController::class,'goods_in'])->name('goods-in');
    Route::post('goods-out', [ScanController::class,'goods_out'])->name('goods-out');    
});


