<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AjaxController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DealerController;
use App\Http\Controllers\ServicePartnerController;
use App\Http\Controllers\CustomerpointRepairController;
use App\Http\Controllers\RepairController;
use App\Http\Controllers\InstallationController;
use App\Http\Controllers\IncompleteInstallationController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\GRNController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\PackingslipController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\AmcController;

use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\CallbackController;
use App\Http\Controllers\CronController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnSpareController;
use App\Http\Controllers\AccountingController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\ServiceCentreController;
use App\Http\Controllers\DAPServiceController;
use App\Http\Controllers\CustomerPaymentController;
use App\Http\Controllers\RoleManagementController;
use App\Http\Controllers\DealerPurchaseOrderController;
use App\Http\Controllers\SpareReturnController;
use App\Http\Controllers\SpareInventoryController;

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

/*Route::get('/', function () {
    return view('welcome');
});*/

# Test Purpose
Route::prefix('test')->name('test.')->group(function(){
    Route::get('/index', [TestController::class, 'index'])->name('index');
    Route::get('/mail_send', [TestController::class, 'mail_send'])->name('mail_send');
    Route::get('/cookie', [TestController::class, 'cookie'])->name('cookie');
    Route::get('/changelog', [TestController::class, 'changelog'])->name('changelog');
    Route::get('/getKGAAPI', [TestController::class, 'getKGAAPI'])->name('getKGAAPI');
    Route::get('/whatsapp', [TestController::class, 'whatsapp'])->name('whatsapp');
    Route::get('/get_product_title', [TestController::class, 'get_product_title'])->name('get_product_title');
    Route::get('/upload-csv', [TestController::class, 'upload_csv'])->name('upload-csv');
    Route::post('/submit-csv', [TestController::class, 'submit_csv'])->name('submit-csv');
    Route::get('/create_khsola_installation', [TestController::class, 'create_khsola_installation'])->name('create_khsola_installation');
});

Route::prefix('callback')->name('test.')->group(function(){
    Route::post('/kga-sales-order', [CallbackController::class, 'kga_sales_order'])->name('kga_sales_order');
});


Route::prefix('cron')->name('test.')->group(function(){    
    Route::get('/test', [CronController::class, 'test'])->name('test');
    Route::get('/get-daily-sales', [CronController::class, 'get_daily_sales'])->name('get-daily-sales');
    Route::get('/installation-request', [CronController::class, 'installation_request'])->name('installation-request');
    Route::get('/get-daily-stock', [CronController::class, 'get_daily_stock'])->name('get-daily-stock');
    Route::get('/send-chimney-greeting', [CronController::class, 'send_chimney_greeting'])->name('send-chimney-greeting');
    Route::get('/send-amc-request', [CronController::class, 'send_amc_request'])->name('send-amc-request');
    Route::get('/update-product-warranty-status', [CronController::class, 'update_product_warranty_status'])->name('update_product_warranty_status');
});



# Feedback
Route::prefix('feedback')->name('feedback.')->group(function(){
    Route::get('/form-installation', [FeedbackController::class, 'form_installation'])->name('form-installation');
    Route::post('/submit-installation',  [FeedbackController::class, 'submit_installation'])->name('submit-installation');
    Route::get('thankyou-installation', [FeedbackController::class, 'thankyou_installation'])->name('thankyou-installation');

    Route::get('/form-repair', [FeedbackController::class, 'form_repair'])->name('form-repair');
    Route::post('/submit-repair',  [FeedbackController::class, 'submit_repair'])->name('submit-repair');
    Route::get('thankyou-repair', [FeedbackController::class, 'thankyou_repair'])->name('thankyou-repair');

    Route::get('/form-maintenance', [FeedbackController::class, 'form_maintenance'])->name('form-maintenance');
    Route::post('/submit-maintenance',  [FeedbackController::class, 'submit_maintenance'])->name('submit-maintenance');
    Route::get('thankyou-maintenance', [FeedbackController::class, 'thankyou_maintenance'])->name('thankyou-maintenance');
});

# AMC
Route::prefix('amc')->name('amc.')->group(function(){
    Route::get('/offers', function(){
        return view('amc.offer');
    })->name('form-installation');
   
});

# CustomerPayment
Route::prefix('customer-payment')->name('customer-payment.')->group(function(){

    ### AMC ###

    Route::get('/amc-offer/{amc_request_id}', [CustomerPaymentController::class, 'amc_offer'])->name('amc-offer');
    Route::get('/amc-preview/{amc_request_id}/{amc_id}', [CustomerPaymentController::class, 'amc_preview'])->name('amc-preview');
    Route::post('/submit-amc', [CustomerPaymentController::class, 'submit_amc'])->name('submit-amc');
    Route::get('/amc-return', [CustomerPaymentController::class, 'amc_return'])->name('amc-return');


    ### DAP Repairing ###

    Route::get('/view-dap/{service_id}', [CustomerPaymentController::class, 'view_dap'])->name('view-dap');
    Route::post('/dap-submit', [CustomerPaymentController::class, 'dap_submit'])->name('dap-submit');
    Route::get('/dap-return', [CustomerPaymentController::class, 'dap_return'])->name('dap-return');
   
});

# Ajax Routes
Route::prefix('ajax')->name('ajax.')->group(function(){
    Route::post('/search-product-for-amc', [AjaxController::class, 'search_product_for_amc'])->name('search-product-for-amc');
    Route::post('/get-chimnney-repairing-repeat-call', [AjaxController::class, 'get_chimnney_repairing_repeat_call'])->name('get-chimnney-repairing-repeat-call');
    Route::post('/amc-product-delete', [AjaxController::class, 'amc_product_delete'])->name('amc-product-delete');
    Route::post('/search-product-by-type', [AjaxController::class, 'search_product_by_type'])->name('search-product-by-type');
    Route::post('/search-product-for-return', [AjaxController::class, 'search_product_for_return'])->name('search-product-for-return');
    Route::post('/po-bulk-scan', [AjaxController::class, 'pobulkscan'])->name('po-bulk-scan');
    Route::post('/po-single-scan', [AjaxController::class, 'posinglescan'])->name('po-single-scan');
    Route::post('/check-po-scanned-boxes', [AjaxController::class, 'checkPOScannedboxes'])->name('check-po-scanned-boxes');
    Route::post('/check-ps-scanned-boxes', [AjaxController::class, 'checkPSScannedboxes'])->name('check-ps-scanned-boxes');
    Route::post('/subcategory-by-category', [AjaxController::class, 'subcategory_by_category'])->name('subcategory-by-category');
    Route::post('/category-by-product-type', [AjaxController::class, 'category_by_product_type'])->name('category-by-product-type');
    Route::post('/get-single-product', [AjaxController::class, 'get_single_product'])->name('get-single-product');
    Route::post('/get-single-product-amc', [AjaxController::class, 'get_single_product_amc'])->name('get-single-product-amc');
    Route::post('/get-product-warranty-status', [AjaxController::class, 'get_product_warranty_status'])->name('get-product-warranty-status');
    Route::post('/get-spare-part', [AjaxController::class, 'get_spare_part'])->name('get-spare-part');
    Route::post('/search-dealer-user', [AjaxController::class, 'searchDealerUser'])->name('search-dealer-user');
    Route::post('/get-service-partner-by-pincode', [AjaxController::class, 'get_service_partner_by_pincode'])->name('get-service-partner-by-pincode');
    Route::post('/get-customer-point-service-partner-by-pincode', [AjaxController::class, 'get_customer_point_service_partner_by_pincode'])->name('get-customer-point-service-partner-by-pincode');
    Route::post('/searchServicePartner', [AjaxController::class, 'searchServicePartner'])->name('searchServicePartner');
    Route::post('/servicepartner-returnable-spares', [AjaxController::class, 'servicepartner_returnable_spares'])->name('servicepartner-returnable-spares');
    Route::post('/return-spare-bulk-scan', [AjaxController::class, 'returnsparebulkscan'])->name('return-spare-bulk-scan');
    Route::post('/get-bank-list', [AjaxController::class, 'getBankList'])->name('get-bank-list');
    Route::post('/search-branches', [AjaxController::class, 'search_branches'])->name('search-branches');
    Route::post('/get-goods-spare', [AjaxController::class, 'get_goods_spare'])->name('get-goods-spare');
    Route::post('/toggle-status', [AjaxController::class, 'toggle_status'])->name('toggle-status');
    Route::post('/dealer-returnable-goods', [AjaxController::class, 'dealer_returnable_goods'])->name('dealer-returnable-goods');
    Route::post('/service-partner-barcodes', [AjaxController::class, 'service_partner_barcodes'])->name('service-partner-barcodes');    
});


Route::post('/masterLogin', [LoginController::class,'masterLogin'])->name('masterLogin');
Route::get('/', [HomeController::class, 'index'])->name('home');
Auth::routes();

# MASTER WEB

Route::middleware(['auth'])->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/myprofile', [HomeController::class, 'myprofile'])->name('myprofile');
    Route::get('/changepassword', [HomeController::class, 'changepassword'])->name('changepassword');
    Route::post('/saveprofile', [HomeController::class, 'saveprofile'])->name('saveprofile');
    Route::post('/savepassword', [HomeController::class, 'savepassword'])->name('savepassword');
    Route::get('/settings', [HomeController::class, 'settings'])->name('settings');
    Route::post('/savesettings', [HomeController::class, 'savesettings'])->name('savesettings');
    Route::get('/kga-daily-stock', [HomeController::class, 'kga_daily_stock'])->name('kga-daily-stock');
    Route::get('/csv-daily-stock', [HomeController::class, 'csv_daily_stock'])->name('csv-daily-stock');
    Route::get('/kga-daily-sales', [HomeController::class, 'kga_daily_sales'])->name('kga-daily-sales');
	Route::post('/update-email', [HomeController::class, 'updateEmail'])->name('update.email');
    Route::get('/csv-daily-sales', [HomeController::class, 'csv_daily_sales'])->name('csv-daily-sales');
    Route::get('/export-csv-changelog', [HomeController::class, 'export_csv_changelog'])->name('export-csv-changelog');

    # AMC 
    Route::prefix('amc')->name('amc.')->group(function(){
		Route::get('/plan-assets', [AmcController::class, 'plan_assets'])->name('plan-assets');
		Route::post('/plan-assets-create', [AmcController::class, 'plan_assets_create'])->name('plan-assets-create');
		Route::get('/plan-assets-edit/{id}', [AmcController::class, 'plan_assets_edit'])->name('plan-assets-edit');
		Route::post('/plan-assets-update/{id}', [AmcController::class, 'plan_assets_update'])->name('plan-assets-update');
		Route::get('/plan-assets-delete/{id}', [AmcController::class, 'plan_assets_delete'])->name('plan-assets-delete');
        Route::get('/plan-type', [AmcController::class, 'plan_type'])->name('plan-type');
        Route::get('/plan-duration/{id}', [AmcController::class, 'plan_duration'])->name('plan-duration');
        Route::post('/plan-duration-create', [AmcController::class, 'plan_duration_create'])->name('plan-duration-create');
        Route::post('/plan-name-create', [AmcController::class, 'plan_name_create'])->name('plan-name-create');
        Route::post('/plan-name-edit', [AmcController::class, 'plan_name_edit'])->name('plan-name-edit');
        Route::post('/plan-name-delete', [AmcController::class, 'plan_name_delete'])->name('plan-name-delete');
        Route::get('/plan-master/{duration_id}', [AmcController::class, 'plan_master'])->name('plan-master');
        Route::get('/incentive-master', [AmcController::class, 'incentive_master'])->name('incentive-master');
        Route::post('/intensive-update', [AmcController::class, 'incentive_update'])->name('intensive-update');
        Route::post('/save-product-amc', [AmcController::class, 'save_product_amc'])->name('save-product-amc');
        Route::get('/search', [AmcController::class, 'search'])->name('search');
        Route::post('/update-amc-product-amount', [AmcController::class, 'update_amc_product_amount'])->name('update-amc-product-amount');
        Route::get('/ho-sale', [AmcController::class, 'ho_sale'])->name('ho-sale');
        Route::get('/amc-by-product/{id}/{product_id}', [AmcController::class, 'amc_by_product'])->name('amc-by-product');
        Route::post('/call-back-date', [AmcController::class, 'call_back_date'])->name('call-back-date');
        Route::post('/call-refuse', [AmcController::class, 'call_refuse'])->name('call-refuse');
        Route::get('/call-history-track/{id}', [AmcController::class, 'call_history_track'])->name('call-history-track');
        Route::get('/prepare-for-purchase-amc-plan/{kga_sale_id}/{amc_id}', [AmcController::class, 'prepare_for_purchase_amc_plan'])->name('prepare-for-purchase-amc-plan');
        Route::post('/send-payment-link', [AmcController::class, 'send_payment_link'])->name('send-payment-link');
        Route::get('/subscription-amc-data', [AmcController::class, 'subscription_amc_data'])->name('subscription-amc-data');
		Route::get('/subscription-amc-data-view/{id}', [AmcController::class, 'subscription_amc_data_view'])->name('subscription-amc-data-view');
		Route::get('/subscription-amc-data-pdf/{id}', [AmcController::class, 'subscription_amc_data_pdf'])->name('subscription-amc-data-pdf');
		
		Route::get('/subscription/csv', [AmcController::class, 'subscription_amc_csv'])->name('subscription.csv');
        Route::get('/pending-request', [AmcController::class, 'pending_request'])->name('pending-request');
		Route::get('/pending-payment', [AmcController::class, 'pending_payment'])->name('pending-payment');
        Route::post('/request-approve', [AmcController::class,'request_approve'])->name('request-approve');
        Route::post('/after-discount-send-payment-link', [AmcController::class, 'after_discount_send_payment_link'])->name('after-discount-send-payment-link');
		//add request for service centre
		Route::get('/add', [AmcController::class, 'add'])->name('add');
        //service ceter module
        // Route::get('/service-centre-sell-amc', [AmcController::class, 'service_centre_sell_amc'])->name('service-centre-sell-amc');
        // Route::get('/view-product-amc-plan/{pid}', [AmcController::class, 'view_product_amc_plan'])->name('view-product-amc-plan');


    });
    # Role 
    Route::prefix('role-management')->name('role-management.')->group(function(){
        Route::get('/list', [RoleManagementController::class, 'index'])->name('list');
        Route::get('/restricted-modules/{id}', [RoleManagementController::class, 'restricted_modules'])->name('restricted-modules');
        Route::post('/save-restricted-modules', [RoleManagementController::class, 'save_restricted_modules'])->name('save-restricted-modules');
    });
    # Managers
    Route::prefix('manager')->name('manager.')->group(function(){
        Route::get('/list', [ManagerController::class, 'index'])->name('list');
        Route::get('/add', [ManagerController::class, 'create'])->name('add');
        Route::post('/store', [ManagerController::class, 'store'])->name('store');
        Route::get('/show/{id}/{getQueryString?}', [ManagerController::class, 'show'])->name('show');
        Route::get('/edit/{id}/{getQueryString?}', [ManagerController::class, 'edit'])->name('edit');
        Route::post('/update/{id}/{getQueryString?}', [ManagerController::class, 'update'])->name('update');
        Route::get('/toggle-status/{id}/{getQueryString?}',[ManagerController::class, 'toggle_status'])->name('toggle-status');
    });
    # Staffs
    Route::prefix('staffs')->name('staffs.')->group(function(){
        Route::get('/list', [StaffController::class, 'index'])->name('list');
        Route::get('/add', [StaffController::class, 'create'])->name('add');
        Route::post('/store', [StaffController::class, 'store'])->name('store');
        Route::get('/show/{id}/{getQueryString?}', [StaffController::class, 'show'])->name('show');
        Route::get('/edit/{id}/{getQueryString?}', [StaffController::class, 'edit'])->name('edit');
        Route::post('/update/{id}/{getQueryString?}', [StaffController::class, 'update'])->name('update');
        Route::get('/toggle-status/{id}/{getQueryString?}',[StaffController::class, 'toggle_status'])->name('toggle-status');
        Route::get('/change-password/{id}/{getQueryString?}',[StaffController::class, 'change_password'])->name('change-password');
        Route::post('/save-password/{id}/{getQueryString?}', [StaffController::class, 'save_password'])->name('save-password'); 
    });
    # Customers
    Route::prefix('customer')->name('customer.')->group(function(){
        Route::get('/list', [CustomerController::class, 'index'])->name('list');
        Route::get('/add', [CustomerController::class, 'create'])->name('add');
        Route::post('/store', [CustomerController::class, 'store'])->name('store');
        Route::get('/show/{id}/{getQueryString?}', [CustomerController::class, 'show'])->name('show');
        Route::get('/edit/{id}/{getQueryString?}', [CustomerController::class, 'edit'])->name('edit');
        Route::post('/update/{id}/{getQueryString?}', [CustomerController::class, 'update'])->name('update');
        Route::get('/toggle-status/{id}/{getQueryString?}',[CustomerController::class, 'toggle_status'])->name('toggle-status');
    });
    # Dealers
    Route::prefix('dealers')->name('dealers.')->group(function(){
        Route::get('/list', [DealerController::class, 'index'])->name('list');
        Route::get('/add', [DealerController::class, 'create'])->name('add');
        Route::post('/store', [DealerController::class, 'store'])->name('store');
        Route::get('/show/{id}/{getQueryString?}', [DealerController::class, 'show'])->name('show');
        Route::get('/edit/{id}/{getQueryString?}', [DealerController::class, 'edit'])->name('edit');
        Route::post('/update/{id}/{getQueryString?}', [DealerController::class, 'update'])->name('update');
        Route::get('/toggle-status/{id}/{getQueryString?}',[DealerController::class, 'toggle_status'])->name('toggle-status');
        Route::get('/employee-toggle-status/{id}/{getQueryString?}',[DealerController::class, 'employee_toggle_status'])->name('employee-toggle-status');
        Route::get('/dealer-branch-list/{id}/{getQueryString?}', [DealerController::class, 'branchList'])->name('branch-list');
        Route::get('/dealer-employee-list/{id}/{getQueryString?}', [DealerController::class, 'dealerEmployee'])->name('dealer-employee-list');
        Route::get('/dealer-employee-add/{id}/{getQueryString?}', [DealerController::class, 'dealerEmployeeAdd'])->name('dealer-employee-add');
        Route::post('/dealer-employee-store/{id}/{getQueryString?}', [DealerController::class, 'dealerEmployeeStore'])->name('dealer-employee-store');
        Route::get('/dealer-employee-edit/{id}/{getQueryString?}', [DealerController::class, 'dealerEmployeeEdit'])->name('dealer-employee-edit');
        Route::post('/dealer-employee-update/{id}/{getQueryString?}', [DealerController::class, 'dealerEmployeeUpdate'])->name('dealer-employee-update');





    });
    # Service Partners
    Route::prefix('service-partner')->name('service-partner.')->group(function(){
        Route::get('/list', [ServicePartnerController::class, 'index'])->name('list');
        Route::get('/add', [ServicePartnerController::class, 'create'])->name('add');
        Route::post('/store', [ServicePartnerController::class, 'store'])->name('store');
        Route::get('/show/{id}/{getQueryString?}', [ServicePartnerController::class, 'show'])->name('show');
        Route::get('/edit/{id}/{getQueryString?}', [ServicePartnerController::class, 'edit'])->name('edit');
        Route::post('/update/{id}/{getQueryString?}', [ServicePartnerController::class, 'update'])->name('update');
        Route::get('/toggle-status/{id}/{getQueryString?}',[ServicePartnerController::class, 'toggle_status'])->name('toggle-status');
        Route::get('/change-password/{id}/{getQueryString?}',[ServicePartnerController::class, 'change_password'])->name('change-password');
        Route::post('/save-password/{id}/{getQueryString?}', [ServicePartnerController::class, 'save_password'])->name('save-password');      
        
        Route::post('/asign-pincodes/{id}',[ServicePartnerController::class, 'asign_pincodes'])->name('asign-pincodes');    
        Route::get('/upload-pincode-csv/{id}',[ServicePartnerController::class, 'upload_pincode_csv'])->name('upload-pincode-csv');
        Route::post('/assign-pincode-csv',[ServicePartnerController::class, 'assign_pincode_csv'])->name('assign-pincode-csv');
        Route::get('/view-duplicate-pincode-assignee', [ServicePartnerController::class, 'view_duplicate_pincode_assignee'])->name('view-duplicate-pincode-assignee');
        Route::post('/remove-duplicate-pincode-assignee', [ServicePartnerController::class, 'remove_duplicate_pincode_assignee'])->name('remove-duplicate-pincode-assignee');
        Route::get('/pincodelist/{id}', [ServicePartnerController::class, 'pincodelist'])->name('pincodelist');
        Route::get('/pincodelistcheckbox/{id}', [ServicePartnerController::class, 'pincodelistcheckbox'])->name('pincodelistcheckbox');
        Route::post('/removepincdoebulk/{id}', [ServicePartnerController::class, 'removepincdoebulk'])->name('removepincdoebulk');    
        Route::get('/call-logs/{id}/{type}/{getQueryString?}', [ServicePartnerController::class, 'call_logs'])->name('call-logs');
        Route::get('/add-charges/{id}/{getQueryString?}', [ServicePartnerController::class, 'add_charges'])->name('add-charges');
        Route::post('/save-charges/{id}/{getQueryString?}', [ServicePartnerController::class, 'save_charges'])->name('save-charges');
        
    });
     # Customer Point Repair
    Route::prefix('customer-point-repair')->group(function(){
        Route::get('service-partner/list', [CustomerpointRepairController::class, 'index'])->name('customer-point-repair.list');
        Route::get('/show/{id}/{getQueryString?}', [CustomerpointRepairController::class, 'show'])->name('customer-point-repair.show');
        Route::get('/upload-pincode-csv/{id}',[CustomerpointRepairController::class, 'upload_pincode_csv'])->name('customer-point-repair.upload-pincode-csv');
        Route::post('/assign-pincode-csv',[CustomerpointRepairController::class, 'assign_pincode_csv'])->name('customer-point-repair.assign-pincode-csv');
        Route::get('/pincodelist/{id}', [CustomerpointRepairController::class, 'pincodelist'])->name('customer-point-repair.pincodelist');
        Route::post('/removepincdoebulk/{id}', [CustomerpointRepairController::class, 'removepincdoebulk'])->name('customer-point-repair.removepincdoebulk');    
        Route::get('/check-product-details', [CustomerpointRepairController::class, 'checkProductDetails'])->name('customer-point-repair.check-product-details');    
        Route::get('/list-booking', [CustomerpointRepairController::class, 'list_booking'])->name('customer-point-repair.list-booking');    
        Route::get('/add-request', [CustomerpointRepairController::class, 'add_call_request'])->name('customer-point-repair.add-call-request');    
        Route::post('/store-request', [CustomerpointRepairController::class, 'store_call_request'])->name('customer-point-repair.store-call-request');   
        Route::get('/crp-barcode/{id}', [CustomerpointRepairController::class, 'barcode'])->name('customer-point-repair.crp-barcode');
        Route::get('/add-spare/{id}', [CustomerpointRepairController::class, 'add_spare'])->name('customer-point-repair.add-spare');
        Route::post('/save-spare', [CustomerpointRepairController::class, 'save_spare'])->name('customer-point-repair.save-spare');
        Route::post('/delete-spare', [CustomerpointRepairController::class, 'delete_spare'])->name('customer-point-repair.delete-spare');
        Route::post('/admin-approval', [CustomerpointRepairController::class, 'admin_approval'])->name('customer-point-repair.admin-approval');
        Route::post('/cancell', [CustomerpointRepairController::class, 'cancell'])->name('customer-point-repair.cancell');
        Route::post('/reassign-engineer', [CustomerpointRepairController::class, 'reassign_engineer'])->name('customer-point-repair.reassign-engineer');
        Route::get('/download-customer-invoice/{id}', [CustomerpointRepairController::class, 'download_customer_invoice'])->name('customer-point-repair.download-customer-invoice');
        Route::get('/send-user-invoice-link/{id}', [CustomerpointRepairController::class, 'send_user_invoice_link'])->name('customer-point-repair.send-user-invoice-link');
        Route::get('/return-dead-spares-barcode/{id}/{getQueryString?}', [CustomerpointRepairController::class, 'DeadBarcodes'])->name('customer-point-repair.return-spares.barcodes');

    });

    # Installation
    Route::prefix('installation')->name('installation.')->group(function(){
        Route::get('/list', [InstallationController::class, 'list'])->name('list');
        Route::get('/add', [InstallationController::class, 'add'])->name('add');
        Route::post('/save', [InstallationController::class, 'save'])->name('save');
        Route::post('/upload-csv', [InstallationController::class, 'upload_csv'])->name('upload-csv');
        Route::post('/submit-call-close', [InstallationController::class, 'submit_call_close'])->name('submit-call-close');
        Route::get('/edit/{id}/{getQueryString?}', [InstallationController::class, 'edit'])->name('edit');
        Route::post('/update/{id}/{getQueryString?}', [InstallationController::class, 'update'])->name('update');
        Route::get('/set-urgent/{id}/{getQueryString?}', [InstallationController::class, 'set_urgent'])->name('set-urgent');
        Route::post('/save-remark', [InstallationController::class, 'save_remark'])->name('save-remark');
        Route::get('/cancel/{id}/{getQueryString?}', [InstallationController::class, 'cancel'])->name('cancel');
    });
    # Incomplete Installation
    Route::prefix('incomplete-installation')->name('incomplete-installation.')->group(function(){
        Route::get('/list', [IncompleteInstallationController::class, 'index'])->name('list');
        Route::get('/clear-form', [IncompleteInstallationController::class, 'clear_form'])->name('clear-form');
        Route::post('save-incomplete-installation', [IncompleteInstallationController::class, 'save_incomplete_installation'])->name('save-incomplete-installation');
    });
    # Repair
    Route::prefix('repair')->name('repair.')->group(function(){
        Route::get('/list', [RepairController::class, 'list'])->name('list');
        Route::get('/add', [RepairController::class, 'add'])->name('add');
        Route::post('/save', [RepairController::class, 'save'])->name('save');
        Route::post('/submit-call-close', [RepairController::class, 'submit_call_close'])->name('submit-call-close');
        Route::get('/edit/{id}/{getQueryString?}', [RepairController::class, 'edit'])->name('edit');
        Route::post('/update/{id}/{getQueryString?}', [RepairController::class, 'update'])->name('update');
        Route::post('/save-remark', [RepairController::class, 'save_remark'])->name('save-remark');
        Route::get('/cancel/{id}/{getQueryString?}', [RepairController::class, 'cancel'])->name('cancel');
        Route::get('/add-spares/{id}/{getQueryString?}', [RepairController::class, 'add_spares'])->name('add-spares');
        Route::post('/save-spares/{id}/{getQueryString?}', [RepairController::class, 'save_spares'])->name('save-spares');
        Route::get('/remove-spares/{id}/{getQueryString?}', [RepairController::class, 'remove_spares'])->name('remove-spares');
    });

    # Suppliers
    Route::prefix('supplier')->name('supplier.')->group(function(){
        Route::get('/list', [SupplierController::class, 'index'])->name('list');
        Route::get('/add', [SupplierController::class, 'create'])->name('add');
        Route::post('/store', [SupplierController::class, 'store'])->name('store');
        Route::get('/show/{id}/{getQueryString?}', [SupplierController::class, 'show'])->name('show');
        Route::get('/edit/{id}/{getQueryString?}', [SupplierController::class, 'edit'])->name('edit');
        Route::post('/update/{id}/{getQueryString?}', [SupplierController::class, 'update'])->name('update');
        Route::get('/toggle-status/{id}/{getQueryString?}',[SupplierController::class, 'toggle_status'])->name('toggle-status');
    });
    # Categories
    Route::prefix('category')->name('category.')->group(function(){
        Route::get('/list', [CategoryController::class, 'index'])->name('list');
        Route::get('/add', [CategoryController::class, 'create'])->name('add');
        Route::post('/store', [CategoryController::class, 'store'])->name('store');
        Route::get('/show/{id}/{getQueryString?}', [CategoryController::class, 'show'])->name('show');
        Route::get('/edit/{id}/{getQueryString?}', [CategoryController::class, 'edit'])->name('edit');
        Route::post('/update/{id}/{getQueryString?}', [CategoryController::class, 'update'])->name('update');
        Route::get('/toggle-status/{id}/{getQueryString?}',[CategoryController::class, 'toggle_status'])->name('toggle-status');
    });
    # Products
    Route::prefix('product')->name('product.')->group(function(){
        Route::get('/list', [ProductController::class, 'index'])->name('list');
        Route::get('/add', [ProductController::class, 'create'])->name('add');
        Route::post('/store', [ProductController::class, 'store'])->name('store');
        Route::get('/show/{id}/{getQueryString?}', [ProductController::class, 'show'])->name('show');
        Route::get('/edit/{id}/{getQueryString?}', [ProductController::class, 'edit'])->name('edit');
        Route::post('/update/{id}/{getQueryString?}', [ProductController::class, 'update'])->name('update');
        Route::get('/toggle-status/{id}/{getQueryString?}',[ProductController::class, 'toggle_status'])->name('toggle-status');
        Route::get('/copy/{id}/{getQueryString?}', [ProductController::class, 'copy'])->name('copy');
        Route::get('/csv-upload', [ProductController::class, 'csv_upload'])->name('csv-upload');
        Route::post('/submit-csv', [ProductController::class, 'submit_csv'])->name('submit-csv');
        Route::post('/change-status-ajax', [ProductController::class, 'change_status_ajax'])->name('change-status-ajax');
        Route::get('/assign-spare-goods/{id}/{getQueryString?}', [ProductController::class, 'assign_spare_goods'])->name('assign-spare-goods');
        Route::post('/save-spare-goods/{id}/{getQueryString?}', [ProductController::class, 'save_spare_goods'])->name('save-spare-goods');
        Route::get('/view-amc/{id}/{getQueryString?}', [ProductController::class, 'view_amc'])->name('view-amc');
        Route::post('/save-amc/{id}/{getQueryString?}', [ProductController::class, 'save_amc'])->name('save-amc');
        Route::get('/remove-amc-offers/{id}/{getQueryString?}', [ProductController::class, 'remove_amc_offers'])->name('remove-amc-offers');
        Route::get('/csv-export', [ProductController::class, 'csv_export'])->name('csv-export');
        Route::get('/add-goods-warranty/{id}/{getQueryString?}', [ProductController::class, 'add_goods_warranty'])->name('add-goods-warranty');
        Route::get('/list-goods-warranty/{id}/{getQueryString?}', [ProductController::class, 'list_goods_warranty'])->name('list-goods-warranty');
        Route::get('/remove-goods-warranty/{id}', [ProductController::class, 'remove_goods_warranty'])->name('remove-goods-warranty');
        Route::post('/save-goods-warranty/{id}/{getQueryString?}', [ProductController::class, 'save_goods_warranty'])->name('save-goods-warranty');
        Route::post('/duplicate-warranty', [ProductController::class, 'duplicate_warranty'])->name('duplicate-warranty');
    });
    # PurchaseOrderController
    Route::prefix('purchase-order')->name('purchase-order.')->group(function(){
        Route::get("/list", [PurchaseOrderController::class, 'index'])->name('list');
        Route::get("/add", [PurchaseOrderController::class, 'create'])->name('add');
        Route::post('/store', [PurchaseOrderController::class, 'store'])->name('store');
        Route::get("/edit/{id}/{getQueryString?}", [PurchaseOrderController::class, 'edit'])->name('edit');
        Route::post('/update/{id}/{getQueryString?}', [PurchaseOrderController::class, 'update'])->name('update');
        
        Route::get("/cancel/{id}/{getQueryString?}", [PurchaseOrderController::class, 'cancel'])->name('cancel');
        Route::get("/make-grn/{id}/{getQueryString?}", [PurchaseOrderController::class, 'make_grn'])->name('make-grn');
        Route::get('/viewgrn/{id}/{getQueryString?}', [PurchaseOrderController::class, 'viewgrn'])->name('viewgrn');
        Route::post('/generate-grn', [PurchaseOrderController::class, 'generategrn'])->name('generate-grn');
        Route::get('/remove-item/{id}/{product_id}', [PurchaseOrderController::class, 'remove_item'])->name('remove-item');
        Route::get('/barcodes/{id}', [PurchaseOrderController::class, 'barcodes'])->name('barcodes');
        Route::get('/barcode-csv/{id}', [PurchaseOrderController::class, 'barcode_csv'])->name('barcode-csv');
        Route::get("/show/{id}/{getQueryString?}", [PurchaseOrderController::class, 'show'])->name('show');
        Route::get("/archive/{type}/{id}/{product_id}/{barcode_no}/{goods_in_type?}/{getQueryString?}", [PurchaseOrderController::class, 'archive'])->name('archive');
        Route::get('/archived/{id}', [PurchaseOrderController::class, 'archived'])->name('archived');
        Route::post('/bulk-archive', [PurchaseOrderController::class, 'bulk_archive'])->name('bulk_archive');
        Route::get('/barcode-number-pdf/{id}', [PurchaseOrderController::class, 'barcode_number_pdf'])->name('barcode-number-pdf');

    });
    # GRN
    Route::prefix('grn')->name('grn.')->group(function(){
        Route::get('/list', [GRNController::class, 'index'])->name('index');
        Route::get('/show/{id}', [GRNController::class, 'show'])->name('show');
        Route::get('/barcodes/{id}', [GRNController::class, 'barcodes'])->name('barcodes');
        Route::get('/barcode-csv/{id}', [GRNController::class, 'barcode_csv'])->name('barcode-csv');
    });
    # Stock
    Route::prefix('stock')->name('stock.')->group(function(){
        Route::get('/list', [StockController::class, 'index'])->name('list');
        Route::get('/logs/{id}/{getQueryString?}', [StockController::class, 'logs'])->name('logs');
        Route::get('/barcodes/{id}/{getQueryString?}', [StockController::class, 'barcodes'])->name('barcodes');
        Route::get('/barcodes/damage-check/{id}/{status}', [StockController::class, 'barcodeDamageCheck'])->name('barcode-damage-check');
        Route::get('/product-by-barcode', [StockController::class, 'product_by_barcode'])->name('product-by-barcode');
        Route::get('/stock-list-csv', [StockController::class, 'stock_list_csv'])->name('stock-list-csv');
        Route::get('/all-damage-stock-barcodes', [StockController::class, 'all_damage_stock_barcodes'])->name('all-damage-stock-barcodes');
    });
    # SalesOrderController
    Route::prefix('sales-order')->name('sales-order.')->group(function(){
        Route::get("/list", [SalesOrderController::class, 'index'])->name('list');
        Route::get("/add", [SalesOrderController::class, 'create'])->name('add');
        Route::post('/store', [SalesOrderController::class, 'store'])->name('store');
        Route::get("/cancel/{id}/{getQueryString?}", [SalesOrderController::class, 'cancel'])->name('cancel');
        Route::get("/show/{id}/{getQueryString?}", [SalesOrderController::class, 'show'])->name('show');
        Route::get("/generate-packing-slip/{id}/{getQueryString?}", [SalesOrderController::class, 'generate_packing_slip'])->name('generate-packing-slip');
        Route::post('/save-packing-slip/{id}/{getQueryString?}', [SalesOrderController::class, 'save_packing_slip'])->name('save-packing-slip');
    });
    # PackingslipController
    Route::prefix('packingslip')->name('packingslip.')->group(function(){
        Route::get('/list', [PackingslipController::class, 'index'])->name('list');
        Route::get('/show/{id}/{getQueryString?}', [PackingslipController::class, 'show'])->name('show');
        Route::get('/download/{id}', [PackingslipController::class, 'download'])->name('download');
        Route::get('/raise-invoice/{id}', [PackingslipController::class, 'raise_invoice'])->name('raise-invoice');
        Route::post('/save-invoice', [PackingslipController::class, 'save_invoice'])->name('save-invoice');
        Route::get('/goods-scan-out/{id}/{getQueryString?}', [PackingslipController::class, 'goods_scan_out'])->name('goods-scan-out');
        Route::post('/save-scan-out/{id}', [PackingslipController::class, 'save_scan_out'])->name('save-scan-out');
        Route::get('/barcodes/{id}', [PackingslipController::class, 'barcodes'])->name('barcodes');
        Route::get('/barcode-csv/{id}', [PackingslipController::class, 'barcode_csv'])->name('barcode-csv');
    });
    # InvoiceController
    Route::prefix('invoice')->name('invoice.')->group(function(){
        Route::get('/list', [InvoiceController::class, 'index'])->name('list');
        Route::get('/download/{id}', [InvoiceController::class, 'download'])->name('download');
    });
    # ReportController
    Route::prefix('report')->name('report.')->group(function(){
        Route::get('/ledger-user', [ReportController::class, 'ledger_user'])->name('ledger-user');
        Route::get('/ledger-user-csv', [ReportController::class, 'ledger_user_csv'])->name('ledger-user-csv');
    });
    # ReturnSpareController
    Route::prefix('return-spares')->name('return-spares.')->group(function(){
        Route::get('/list', [ReturnSpareController::class, 'index'])->name('list');
        Route::get('/crp/spare', [ReturnSpareController::class, 'store_crp_spare'])->name('store_crp_spare');
      
        Route::get('/crp/spare/without-warranty/check', [ReturnSpareController::class, 'crp_spare_without_warranty_check'])->name('crp_spare_without_warranty_check');
        Route::get('/crp/spare/not_required/{id}/{status}', [ReturnSpareController::class, 'spare_not_required'])->name('spare_not_required');
        Route::get('/crp/old-spare', [ReturnSpareController::class, 'return_old_spare'])->name('return_old_spare');
        Route::get('/add', [ReturnSpareController::class, 'add'])->name('add');
        Route::post('/save', [ReturnSpareController::class, 'save'])->name('save');
        Route::get('/show/{id}/{getQueryString?}', [ReturnSpareController::class, 'show'])->name('show');
        Route::get('/barcodes/{id}/{getQueryString?}', [ReturnSpareController::class, 'barcodes'])->name('barcodes');
        Route::get('/barcode-csv/{id}', [ReturnSpareController::class, 'barcode_csv'])->name('barcode-csv');
        Route::get("/make-grn/{id}/{getQueryString?}", [ReturnSpareController::class, 'make_grn'])->name('make-grn');
        Route::post('/generate-grn', [ReturnSpareController::class, 'generategrn'])->name('generate-grn');
        Route::get('/cancel/{id}/{getQueryString?}', [ReturnSpareController::class, 'cancel'])->name('cancel');
    });
    # AccountingController
    Route::prefix('accounting')->name('accounting.')->group(function(){
        Route::get('/payment-list', [AccountingController::class, 'payment_list'])->name('payment-list');
        Route::get('/payment-add', [AccountingController::class, 'payment_add'])->name('payment-add');
        Route::post('/payment-save', [AccountingController::class, 'payment_save'])->name('payment-save');
        Route::get('/list-credit-note', [AccountingController::class, 'list_credit_note'])->name('list-credit-note');
        Route::get('/add-credit-note', [AccountingController::class, 'add_credit_note'])->name('add-credit-note');
        Route::post('/save-credit-note', [AccountingController::class, 'save_credit_note'])->name('save-credit-note');
    });

    # MaintenanceController
    Route::prefix('maintenance')->name('maintenance.')->group(function(){
        Route::get('/list', [MaintenanceController::class, 'list'])->name('list');
        Route::get('/add', [MaintenanceController::class, 'add'])->name('add');
        Route::get('/add-request', [MaintenanceController::class, 'add_call_request'])->name('add-call-request');    
		Route::post('/service-provider-update', [MaintenanceController::class, 'ServiceProvderUpdate'])->name('service-provider-update');
        Route::get('/checkitemstatus', [MaintenanceController::class, 'checkitemstatus'])->name('checkitemstatus');
        
        // Route::get('/add/{service_type?}', [MaintenanceController::class, 'add'])->name('add');
        Route::post('/save', [MaintenanceController::class, 'save'])->name('save');
        // Route::post('/save/{service_type?}', [MaintenanceController::class, 'save'])->name('save');
        Route::post('/save_remark', [MaintenanceController::class, 'save_remark'])->name('save_remark');
        Route::post('/submit-call-close', [MaintenanceController::class, 'submit_call_close'])->name('submit-call-close');
        Route::get('/cancel/{id}/{getQueryString?}', [MaintenanceController::class, 'cancel'])->name('cancel');
    });

    # ServiceCentreController
    Route::prefix('service-centre')->name('service-centre.')->group(function(){
        Route::get('/list', [ServiceCentreController::class, 'index'])->name('list');
        Route::get('/add', [ServiceCentreController::class, 'create'])->name('add');
        Route::post('/store', [ServiceCentreController::class, 'store'])->name('store');
        Route::get('/show/{id}/{getQueryString?}', [ServiceCentreController::class, 'show'])->name('show');
        Route::get('/edit/{id}/{getQueryString?}', [ServiceCentreController::class, 'edit'])->name('edit');
        Route::post('/update/{id}/{getQueryString?}', [ServiceCentreController::class, 'update'])->name('update');
        Route::get('/toggle-status/{id}/{getQueryString?}',[ServiceCentreController::class, 'toggle_status'])->name('toggle-status');
        Route::get('/change-password/{id}/{getQueryString?}',[ServiceCentreController::class, 'change_password'])->name('change-password');
        Route::post('/save-password/{id}/{getQueryString?}', [ServiceCentreController::class, 'save_password'])->name('save-password');
        
    });

    # DAPServiceController
    Route::prefix('dap-services')->name('dap-services.')->group(function(){
        Route::get('/list', [DAPServiceController::class, 'index'])->name('list');
        Route::get('/payment-history', [DAPServiceController::class, 'payment_history'])->name('payment-history');
        Route::get('/checkdapitemstatus', [DAPServiceController::class, 'checkdapitemstatus'])->name('checkdapitemstatus');
        Route::get('/add', [DAPServiceController::class, 'create'])->name('add');
        Route::post('/store', [DAPServiceController::class, 'store'])->name('store');
        Route::get('/generate-road-challan', [DAPServiceController::class, 'generate_road_challan'])->name('generate-road-challan');
        Route::post('/save-road-challan', [DAPServiceController::class, 'save_road_challan'])->name('save-road-challan');
        Route::get('/centre-reached-items', [DAPServiceController::class, 'centre_reached_items'])->name('centre-reached-items');
        Route::get('/centre-returned-items', [DAPServiceController::class, 'centre_returned_items'])->name('centre-returned-items');
        Route::get('/make-close/{id}/{getQueryString?}', [DAPServiceController::class, 'make_close'])->name('make-close');
        Route::post('/make-paid/{getQueryString?}', [DAPServiceController::class, 'make_paid'])->name('make-paid');
        Route::get('/dap-barcode/{id}', [DAPServiceController::class, 'barcode'])->name('dap-barcode');
        Route::get('/send-service-centre', [DAPServiceController::class, 'send_service_centre'])->name('send-service-centre');
        Route::post('/generate-road-challan', [DAPServiceController::class, 'generate_road_challan_new'])->name('generate-road-challan-new');
        Route::get('/generate-road-challan/{barcode}', [DAPServiceController::class, 'download_road_challan_new'])->name('download-road-challan-new');
        Route::post('/reassign-engineer', [DAPServiceController::class, 'reassign_engineer'])->name('reassign-engineer');
        Route::get('/dap-quotation/{id}', [DAPServiceController::class, 'dap_quotation'])->name('dap-quotation');
        Route::get('/dap-track/{id}', [DAPServiceController::class, 'dap_track'])->name('dap-track');
        Route::post('/dap-discount-amount-request-approved', [DAPServiceController::class, 'dap_discount_amount_request_approved'])->name('dap-discount-amount-request-approved');
        Route::get('/dap-invoice/{id}', [DAPServiceController::class, 'dap_invoice'])->name('dap-invoice');

    });

    # DealerPurchaseOrderController
    Route::prefix('dealer-purchase-order')->name('dealer-purchase-order.')->group(function(){
        Route::get('/list', [DealerPurchaseOrderController::class, 'index'])->name('list');
        Route::get('/add', [DealerPurchaseOrderController::class, 'create'])->name('add');
        Route::post('/store', [DealerPurchaseOrderController::class, 'store'])->name('store');
        Route::get("/cancel/{id}/{getQueryString?}", [DealerPurchaseOrderController::class, 'cancel'])->name('cancel');
        Route::get("/show/{id}/{getQueryString?}", [DealerPurchaseOrderController::class, 'show'])->name('show');
        Route::get("/generate-grn/{id}/{getQueryString?}", [DealerPurchaseOrderController::class, 'generate_grn'])->name('generate-grn');
        Route::post("/save-grn", [DealerPurchaseOrderController::class, 'save_grn'])->name('save-grn');
        Route::get('/barcodes/{id}', [DealerPurchaseOrderController::class, 'barcodes'])->name('barcodes');
        Route::get('/barcode-csv/{id}', [DealerPurchaseOrderController::class, 'barcode_csv'])->name('barcode-csv');
    });

    # SpareReturnController
    Route::prefix('spare-return')->name('spare-return.')->group(function(){
        Route::get('/list', [SpareReturnController::class, 'index'])->name('list');
        Route::get('/return-items/{id}/{getQueryString?}', [SpareReturnController::class, 'return_items'])->name('return-items');
        Route::get('/generate-new-barcode/{id}/{getQueryString?}', [SpareReturnController::class, 'generate_new_barcode'])->name('generate-new-barcode');
    });

    # SpareInventoryController
    Route::prefix('spare-inventory')->name('spare-inventory.')->group(function(){
        Route::get('/list', [SpareInventoryController::class, 'index'])->name('list');
        Route::get('/list-return', [SpareInventoryController::class, 'list_return'])->name('list-return');
        Route::post('/save-return', [SpareInventoryController::class, 'save_return'])->name('save-return');
        Route::get('/supplier-return-list', [SpareInventoryController::class, 'supplier_return_list'])->name('supplier-return-list');
        Route::get('/supplier-return-csv', [SpareInventoryController::class, 'supplier_return_csv'])->name('supplier-return-csv');
    });

});

Route::post('/logout',function(Request $request){
    $redirect_url = isset($request->redirect_url)?$request->redirect_url:'';
    // dd($redirect_url);
    Auth::logout();
    if(!empty($redirect_url)){
        Session::flash('message', 'Session timed out !!! ');
        Session::flash('redirect_url', $redirect_url);
    }
    
    return redirect()->route('login');
})->name('logout');
Route::get('/cashfree/payment/success', [CronController::class, 'Dap_payment_success'])->name('dap_payment_success');
Route::get('/cashfree/crp/payment/success', [CronController::class, 'Crp_payment_success'])->name('crp_payment_success');
Route::get('/cashfree/amc/payment/success', [CronController::class, 'Amc_payment_success'])->name('amc_payment_success');
Route::get('/verify', [CronController::class, 'Dap_payment_link'])->name('dap_payment_link');
Route::get('c/verify', [CronController::class, 'CRP_payment_link'])->name('CRP_payment_link');
Route::get('a/verify', [CronController::class, 'AMC_payment_link'])->name('AMC_payment_link');
Route::get('/in', [CronController::class, 'dap_invoice'])->name('invoice');
Route::get('/cin', [CronController::class, 'crp_invoice'])->name('c_invoice');
Route::get('/amc/invoice/{id}', [CronController::class, 'whatsapp_amc_invoice'])->name('whatsapp_amc_invoice');
Route::get('/website/send-whatsapp', [CronController::class, 'send_whatsapp'])->name('send_whatsapp');
Route::get('/website/ami_amit', [CronController::class, 'Ami_Amit'])->name('ami_amit');
Route::get('/test-invoice', [CronController::class, 'testGenerateInvoice']);

// New Route
// https://kgaerp.in/retailer/amc/payment/{amc_id}
// https://kgaerp.in/retailer/amc/bill/{amc_id}
// https://kgaerp.in/test-retailer/amc/payment/{amc_id}
// https://kgaerp.in/test-retailer/amc/bill/{amc_id}
