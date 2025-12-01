<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ServicePartner\HomeController;
use App\Http\Controllers\ServicePartner\PincodeController;
use App\Http\Controllers\ServicePartner\NotificationController;
use App\Http\Controllers\ServicePartner\ReportController;
use App\Http\Controllers\ServicePartner\RepairSpareController;
use App\Http\Controllers\ServicePartner\MaintenanceController;
use App\Http\Controllers\ServicePartner\AmcController;
use App\Http\Controllers\ServicePartner\CustomerRepairPointController;

Route::prefix('servicepartnerweb')->name('servicepartnerweb.')->group(function(){
    Route::get('/login',[LoginController::class,'showServicePartnerLoginForm'])->name('login-view');
    Route::post('/loginpost',[LoginController::class,'servicepartnerLogin'])->name('login');

    Route::middleware(['auth:servicepartner'])->group(function () {
        Route::get('/dashboard',[HomeController::class, 'index'])->name('dashboard');
        Route::get('/myprofile',[HomeController::class, 'my_profile'])->name('myprofile');
        Route::get('/changepassword',[HomeController::class, 'change_password'])->name('changepassword');
        Route::post('/saveprofile', [HomeController::class, 'save_profile'])->name('saveprofile');
        Route::post('/savepassword', [HomeController::class, 'save_password'])->name('savepassword');
        # My PIN Codes
        Route::prefix('pincode')->name('pincode.')->group(function(){
            Route::get('/list',[PincodeController::class, 'index'])->name('list');
        });
        # My Service Notification
        Route::prefix('notification')->name('notification.')->group(function(){
            Route::get('/list-installation',[NotificationController::class, 'list_installation'])->name('list-installation');
            Route::get('/list-repair',[NotificationController::class, 'list_repair'])->name('list-repair');
            
            Route::get('/list-customer-repair-point',[NotificationController::class, 'list_customer_repair_point'])->name('list-customer-repair-point');

            Route::get('/close-otp-installation/{id}/{getQueryString?}',[NotificationController::class, 'close_otp_installation'])->name('close-otp-installation');
            Route::post('/submit-otp-installation/{getQueryString?}',[NotificationController::class, 'submit_otp_installation'])->name('submit-otp-installation');
            Route::post('/submit-invoice-image-installation/{getQueryString?}',[NotificationController::class, 'submit_invoice_image_installation'])->name('submit-invoice-image-installation');
            Route::get('/close-otp-repair/{id}/{getQueryString?}',[NotificationController::class, 'close_otp_repair'])->name('close-otp-repair');
            Route::post('/submit-otp-repair/{getQueryString?}',[NotificationController::class, 'submit_otp_repair'])->name('submit-otp-repair');

            Route::post('/save-remarks-installation',[NotificationController::class, 'save_remarks_installation'])->name('save-remarks-installation');
            Route::post('/save-remarks-repair',[NotificationController::class, 'save_remarks_repair'])->name('save-remarks-repair');
            
        });
        # Report
        Route::prefix('report')->name('report.')->group(function(){
            Route::get('/ledger', [ReportController::class, 'ledger'])->name('ledger');
            Route::get('/ledger-csv', [ReportController::class, 'ledger_csv'])->name('ledger-csv');
        });
        # Repair Spare Parts
        Route::prefix('repair-spare')->name('repair-spare.')->group(function(){
            Route::get('/add/{id}/{getQueryString?}', [RepairSpareController::class, 'add'])->name('add');
            Route::post('/save/{getQueryString?}', [RepairSpareController::class, 'save'])->name('save');
            Route::get('/clear/{id}/{getQueryString?}', [RepairSpareController::class, 'clear'])->name('clear');
            Route::post('/save-requisition-note/{getQueryString?}',[RepairSpareController::class, 'save_requisition_note'])->name('save-requisition-note');
        });
        # Repair Spare Parts
        Route::prefix('customer-repair-point')->name('customer-repair-point.')->group(function(){
            Route::get('/add-spare/{id}/{getQueryString?}', [CustomerRepairPointController::class, 'add_spare'])->name('add_spare');
            Route::post('/save-spare', [CustomerRepairPointController::class, 'save_spare'])->name('save-spare');
            Route::post('/delete-spare', [CustomerRepairPointController::class, 'delete_spare'])->name('delete-spare');

            // Route::get('/clear/{id}/{getQueryString?}', [RepairSpareController::class, 'clear'])->name('clear');
            // Route::post('/save-requisition-note/{getQueryString?}',[RepairSpareController::class, 'save_requisition_note'])->name('save-requisition-note');
        });
        # Maintenance
        Route::prefix('maintenance')->name('maintenance.')->group(function(){
            Route::get('/list', [MaintenanceController::class, 'list'])->name('list');
            Route::post('/save_remark', [MaintenanceController::class, 'save_remark'])->name('save_remark');
            Route::get('/close-otp-request/{id}/{getQueryString?}', [MaintenanceController::class, 'close_otp_request'])->name('close-otp-request');
            Route::post('/submit-closing-otp/{getQueryString?}', [MaintenanceController::class, 'submit_closing_otp'])->name('submit-closing-otp');
            Route::get('/add-spare-parts/{getQueryString?}', [MaintenanceController::class, 'add_spare_parts'])->name('add-spare-parts');
            Route::post('/save-spare-parts/{getQueryString?}', [MaintenanceController::class, 'save_spare_parts'])->name('save-spare-parts');
            Route::get('/clear-spares/{id}/{getQueryString?}', [MaintenanceController::class, 'clear_spares'])->name('clear-spares');
            
        });
        # AMC
        Route::prefix('amc')->name('amc.')->group(function(){
            Route::get('/add', [AmcController::class, 'add'])->name('add');
            Route::get('/amc-by-product/{id}/{product_id}', [AmcController::class, 'amc_by_product'])->name('amc-by-product');
            Route::get('/prepare-for-purchase-amc-plan/{kga_sale_id}/{amc_id}', [AmcController::class, 'prepare_for_purchase_amc_plan'])->name('prepare-for-purchase-amc-plan');
            Route::post('/send-payment-link', [AmcController::class, 'send_payment_link'])->name('send-payment-link');
            Route::post('/discount-request', [AmcController::class, 'discount_request'])->name('discount-request');
            Route::get('/peding-discount-request-list', [AmcController::class, 'pending_discount_request_list'])->name('peding-discount-request-list');
            Route::post('/after-discount-send-payment-link', [AmcController::class, 'after_discount_send_payment_link'])->name('after-discount-send-payment-link');
		
		   Route::get('/subscription-amc-data', [AmcController::class, 'subscription_amc_data'])->name('subscription-amc-data');
		   Route::get('/subscription-amc-data-view/{id}', [AmcController::class, 'subscription_amc_data_view'])->name('subscription-amc-data-view');
			Route::get('/subscription-amc-data-pdf/{id}', [AmcController::class, 'subscription_amc_data_pdf'])->name('subscription-amc-data-pdf');
		    Route::get('/subscription/csv', [AmcController::class, 'subscription_amc_csv'])->name('subscription.csv');
            // Route::post('/save_remark', [MaintenanceController::class, 'save_remark'])->name('save_remark');
            // Route::get('/close-otp-request/{id}/{getQueryString?}', [MaintenanceController::class, 'close_otp_request'])->name('close-otp-request');
            // Route::post('/submit-closing-otp/{getQueryString?}', [MaintenanceController::class, 'submit_closing_otp'])->name('submit-closing-otp');
            // Route::get('/add-spare-parts/{getQueryString?}', [MaintenanceController::class, 'add_spare_parts'])->name('add-spare-parts');
            // Route::post('/save-spare-parts/{getQueryString?}', [MaintenanceController::class, 'save_spare_parts'])->name('save-spare-parts');
            // Route::get('/clear-spares/{id}/{getQueryString?}', [MaintenanceController::class, 'clear_spares'])->name('clear-spares');
            
        });
    });    
    # Logout
    Route::post('/logout',function(){
        Auth::guard('servicepartner')->logout();
        return redirect()->route('servicepartnerweb.login-view');
    })->name('logout');
    
});


