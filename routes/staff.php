<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Staff\AuthController;
use App\Http\Controllers\Api\Staff\ScanController;
use App\Http\Controllers\Api\Staff\PurchaseOrderController;
use App\Http\Controllers\Api\Staff\PackingslipController;
use App\Http\Controllers\Api\Staff\ReturnSpareController;

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
# PO
Route::prefix('po')->name('po.')->group(function(){
    Route::get('list', [PurchaseOrderController::class, 'list'])->name('list');
    Route::post('bulk-goods-in', [PurchaseOrderController::class, 'bulk_goods_in'])->name('bulk-goods-in');
});
# Scan
Route::prefix('scan')->name('scan.')->group(function(){
    Route::post('stockin', [ScanController::class, 'stockin'])->name('stockin');
    Route::post('stockout', [ScanController::class, 'stockout'])->name('stockout');
    Route::post('return-spares-stockin', [ScanController::class, 'return_spares_stockin'])->name('return-spares-stockin');
});
# PS
Route::prefix('ps')->name('ps.')->group(function(){
    Route::get('list', [PackingslipController::class, 'list'])->name('list');
    Route::post('bulk-goods-out', [PackingslipController::class, 'bulk_goods_out'])->name('bulk-goods-out');
});
# Return Spares
Route::prefix('returnspare')->name('returnspare.')->group(function(){
    Route::get('list', [ReturnSpareController::class, 'list'])->name('list');
    Route::post('bulk-goods-in', [ReturnSpareController::class, 'bulk_goods_in'])->name('bulk-goods-in');
});
