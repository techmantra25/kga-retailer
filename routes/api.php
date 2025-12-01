<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\PincodeController;
use App\Http\Controllers\Api\Employee\AuthController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

# Testing purpose
Route::prefix('test')->name('test.')->group(function(){
    Route::get('/index', [TestController::class, 'index'])->name('index');
    Route::get('/success', [TestController::class, 'success'])->name('success');
    Route::get('/error', [TestController::class, 'error'])->name('error');
    Route::post('/save', [TestController::class, 'save'])->name('save');
    Route::get('/create-token-by-userid', [TestController::class, 'create_token_by_userid'])->name('create-token-by-userid');
});

# Pincode
Route::prefix('pincode')->name('pincode.')->group(function(){
    Route::get('/available', [PincodeController::class, 'available'])->name('available');
   
});
# Employee
Route::prefix('employee')->name('employee.')->group(function(){
    Route::post('/login', [AuthController::class,'login'])->name('login');
    Route::post('/logout', [AuthController::class,'logout'])->name('logout');
    Route::get('/dap-barcode-info/{barcode}', [AuthController::class,'DapBarcodeInfo'])->name('dap-barcode-info');
    Route::get('/dap-service', [AuthController::class,'CheckDapItem'])->name('check_dap_item_status');
    Route::get('/dap-service/add/{id}', [AuthController::class,'create'])->name('add');
    Route::post('/dap-service/store', [AuthController::class,'store'])->name('store');
    Route::get('/dap-service/wear-house', [AuthController::class,'wearhouse'])->name('wearhouse');
    Route::get('/dap-service/barcode/{barcode}', [AuthController::class,'barcode'])->name('barcode');
    Route::get('/send-service-centre/{id}', [AuthController::class,'send_service_centre'])->name('send-service-centre');
    Route::post('/dap-service/dispatch-from-branch', [AuthController::class,'dispatch_from_branch'])->name('dispatch-from-branch');
    Route::post('/dap-service/receive-at-wearhouse', [AuthController::class,'receive_at_wearhouse'])->name('receive-at-wearhouse');
    Route::post('/dap-service/generate-road-challan', [AuthController::class,'generate_road_challan'])->name('generate-road-challan');
    Route::get('/dap-service/download-road-challan/{barcode}', [AuthController::class,'download_road_challan'])->name('download-road-challan');
    Route::get('/dap-service/all-showroom', [AuthController::class,'all_showroom'])->name('all-showroom');
    Route::get('/dap-service/branch-wise-dap-product/{id}', [AuthController::class,'branch_wise_dap_product'])->name('branch-wise-dap-product');
    Route::get('/dap-service/receive-repaire-dap-product-at-showroom/{showrromId}/{barcode}', [AuthController::class,'receive_repaire_dap_product_at_showroom'])->name('receive-repaire-dap-product-at-showroom');
    Route::get('/dap-service/customer-delivery-otp/{uniqueId}', [AuthController::class,'customer_delivery_otp'])->name('customer-delivery-otp');
    Route::post('/dap-service/customer-delivery-otp-verify', [AuthController::class,'customer_delivery_otp_verify'])->name('customer-delivery-otp-verify');
    Route::get('/dap-service/return-service-centre-product/{showrromId}', [AuthController::class,'return_service_centre_product'])->name('return-service-centre-product');

});
