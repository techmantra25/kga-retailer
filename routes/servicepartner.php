<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ServicePartner\AuthController;
use App\Http\Controllers\Api\ServicePartner\InstallationController;
use App\Http\Controllers\Api\ServicePartner\RepairController;
use App\Http\Controllers\Api\ServicePartner\ReportController;
use App\Http\Controllers\Api\ServicePartner\ProductController;
use App\Http\Controllers\Api\ServicePartner\MaintenanceController;
use App\Http\Controllers\Api\ServicePartner\DapController;
use App\Http\Controllers\Api\ServicePartner\CrpController;
use App\Http\Controllers\Api\ServicePartner\AmcController;
use App\Http\Controllers\Api\ServicePartner\DapRepairStartController;

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
// Route::get('invoice/{id}', [DapController::class,'dap_invoice'])->name('invoice');

Route::prefix('auth')->name('auth.')->group(function(){
    Route::post('login', [AuthController::class,'login'])->name('login');
    Route::post('logout', [AuthController::class,'logout'])->name('logout');
});
# Installation
Route::prefix('installations')->name('installations.')->group(function(){
    Route::get('list', [InstallationController::class,'list'])->name('list');
    Route::get('closed-list', [InstallationController::class,'closed_list'])->name('closed-list');
    Route::post('request-to-close', [InstallationController::class,'request_to_close'])->name('request-to-close');
    Route::post('submit-close', [InstallationController::class,'submit_close'])->name('submit-close');
    
});
# Repair
Route::prefix('repairs')->name('repairs.')->group(function(){
    Route::get('list', [RepairController::class,'list'])->name('list');
    Route::get('closed-list', [RepairController::class,'closed_list'])->name('closed-list');
    Route::post('request-to-close', [RepairController::class,'request_to_close'])->name('request-to-close');
    Route::post('submit-close', [RepairController::class,'submit_close'])->name('submit-close');
    Route::post('add-spares', [RepairController::class,'add_spares'])->name('add-spares');
    
});
# Report
Route::prefix('report')->name('report.')->group(function(){
    Route::get('ledger', [ReportController::class,'ledger'])->name('ledger');
    Route::get('ledger-csv', [ReportController::class,'ledger_csv'])->name('ledger-csv');    
});
# Product
Route::prefix('product')->name('product.')->group(function(){
    Route::get('list', [ProductController::class,'list'])->name('list');
    Route::get('details/{id}', [ProductController::class,'details'])->name('details');
});
# Maintenace
Route::prefix('maintenance')->name('maintenance.')->group(function(){
    Route::get('list/{service_type?}', [MaintenanceController::class,'list'])->name('list');
    // Route::get('repaire_list/{service_type?}', [MaintenanceController::class,'repaire_list'])->name('repaire-list');
    Route::get('closed-list/{service_type?}', [MaintenanceController::class,'closed_list'])->name('closed-list');
    Route::post('request-to-close', [MaintenanceController::class,'request_to_close'])->name('request-to-close');
    Route::post('submit-close', [MaintenanceController::class,'submit_close'])->name('submit-close');
    Route::post('add-spares', [MaintenanceController::class,'add_spares'])->name('add-spares');
});
# Dap Product
Route::prefix('dap-product')->name('dap-product.')->group(function(){
    Route::get('list/{id}', [DapController::class,'list'])->name('list');
    Route::get('global-search-dap/{id}/{barcode}', [DapController::class,'engg_global_dap_product_search'])->name('engg-global-dap-product-search');
    Route::get('engg-scan-dap-barcode/{id}/{barcode}', [DapController::class,'engg_scan_dap_barcode'])->name('engg-scan-dap-barcode');
    Route::get('engg-scan-spear-part/{sbarcode}/{pid}/{dap_id}', [DapController::class,'engg_scan_spear_part_barcode'])->name('engg-scan-spear-part-barcode');
    Route::get('engg-scan-spear-part-final/{sbarcode}/{pid}/{dap_id}', [DapController::class,'engg_scan_spear_part_barcode_final'])->name('engg-scan-spear-part-barcode-final');
    Route::post('spear-parts-order-new', [DapController::class,'spear_parts_order'])->name('spear-parts-order');
    Route::post('spear-parts-order-final', [DapController::class,'spear_parts_order_final'])->name('spear-parts-order-final');
    Route::get('dap-quotation-list/{id}', [DapController::class,'dap_quotation_list'])->name('dap-quotation-list');
    Route::post('quotation/notify-customer', [DapController::class,'quotation_send_customer'])->name('quotation-send-customer');
    Route::post('dap-discount-request', [DapController::class,'dap_discount_request'])->name('dap-discount-request');
    Route::get('quotation/otp-verify', [DapController::class,'quotation_otp_verify'])->name('quotation-otp-verify');
    Route::get('dap-repair-start/{dap_id}/{engg_id}', [DapRepairStartController::class,'dap_repair_start'])->name('dap-repair-start');
    Route::post('dap-payment-cancelled-is-closed', [DapController::class,'dap_payment_cancelled_is_closed'])->name('dap-payment-cancelled-is-closed');
    Route::get('dap-service-list-cancelled/{id}', [DapController::class,'dap_service_list_cancelled'])->name('dap-service-list-cancelled');
    Route::get('dap-service-list-successed/{id}', [DapController::class,'dap_service_list_successed'])->name('dap-service-list-successed');
    Route::post('return-road-challan-generate', [DapController::class,'return_road_challan_generate'])->name('return-road-challan-generate');
    Route::get('download-return-road-challan/{id}', [DapController::class,'download_return_road_challan'])->name('download-return-road-challan');
    Route::get('display-all-detais-before-dispatched-from-showrrom/{barcode}', [DapController::class,'display_all_detais_before_dispatched_from_showrrom'])->name('display-all-detais-before-dispatched-from-showrrom');
    Route::get('dap-product-return-showroom/{barcode}', [DapController::class,'dap_product_return_showroom'])->name('dap-product-return-showroom');
    
    
});


///custormer repair point
Route::prefix('crp')->name('crp.')->group(function(){
    Route::get('list/{id}', [CrpController::class,'list'])->name('list');
    Route::get('add-spare/{auth_id}/{cpr_id}', [CrpController::class,'add_spare'])->name('add-spare');
    Route::post('add-spare-store', [CrpController::class,'add_spare_store'])->name('add-spare-store');
    Route::get('details/{id}', [CrpController::class,'product_details'])->name('details');
    Route::post('service-closed-with-out-spare', [CrpController::class,'service_closed_with_out_spare'])->name('service-closed-with-out-spare');
    Route::post('service-closed-with-warranty-no-payment', [CrpController::class,'service_closed_with_warranty_no_payment'])->name('service-closed-with-warranty-no-payment');
    Route::post('service-close/otp-verify', [CrpController::class,'service_closed_otp_verify'])->name('service-closed-otp-verify');
    Route::post('service-closed-otp-verify-with-warranty-no-payment', [CrpController::class,'service_closed_otp_verify_with_warranty_no_payment'])->name('service-closed-otp-verify-with-warranty-no-payment');
    Route::get('spare-check-product-no-damage/{crp_id}/{sp_barcode}', [CrpController::class,'sapre_warranty_check_product_no_damage'])->name('sapre-warranty-check-product-no-damage');
    Route::get('spare-check-product-damage/{crp_id}/{sp_barcode}', [CrpController::class,'sapre_warranty_check_product_damage'])->name('sapre-warranty-check-product-damage');
    Route::post('final-spare-warranty-check', [CrpController::class,'final_spare_warranty_check'])->name('final_spare_warranty_check');
    Route::post('final-spare-warranty-confirm', [CrpController::class,'final_spare_warranty_confirm'])->name('final_spare_warranty_confirm');
    Route::get('quotation-send-customer/{crp_id}', [CrpController::class,'quotation_send_customer'])->name('quotation_send_customer');
    Route::get('quotation-otp-verify', [CrpController::class,'quotation_otp_verify'])->name('quotation-otp-verify');
    Route::post('offline-payment', [CrpController::class,'offline_payment'])->name('offline-payment');
    Route::get('regenerate-payment-link/{crp_id}', [CrpController::class,'regenerate_payment_link'])->name('regenerate-payment-link');

});


///AMC
Route::prefix('amc')->name('amc.')->group(function(){

    Route::get('amc-plan-type', [AmcController::class,'amc_plan_type'])->name('amc_plan_type');
    Route::get('product-list', [AmcController::class,'product_list'])->name('product-list');
    Route::get('fetch-product', [AmcController::class,'fetch_product'])->name('fetch-product');//this is appilicable for product search
    Route::get('fetch-amc-plan/{product_id}', [AmcController::class,'fetch_amc_plan'])->name('fetch-amc-plan');
    Route::get('fetch-customer', [AmcController::class,'fetch_customer'])->name('fetch-customer');
    Route::get('select-customer/{kga_sales_id}', [AmcController::class,'select_customer'])->name('select-customer');
    Route::post('discount-request', [AmcController::class,'discount_request'])->name('discount_request');
    Route::post('send-payment-link', [AmcController::class,'send_payment_link'])->name('send-payment-link');
    

});
