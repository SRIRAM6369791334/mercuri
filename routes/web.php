<?php

use App\Http\Controllers\AddassetController;
use App\Http\Controllers\AddEmployeeController;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\AssignSettingController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductSubCategoryController;
use App\Http\Controllers\PurchaseEntryController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseStockController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FieldController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExpoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DateOfValidityController;
use App\Http\Controllers\LeadDesignController;
use App\Http\Controllers\OrfController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TermController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignSpecificationMasterController;
use App\Http\Controllers\EmployeeAdvanceController;
use App\Http\Controllers\MailsettingController;
use App\Http\Controllers\ProductSpecificationController;
use App\Http\Controllers\QuotationProductController;
use App\Http\Controllers\AccessoryController;
use App\Models\Design;
use App\Http\Controllers\LeadImportController;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', function () {
    return view('welcome');
})->middleware('guest')->name('login');

Route::POST('/login', [LoginController::class, 'login']);

Route::get('/logout', [LoginController::class, 'logout']);


Route::group(['middleware' => 'auth'], function () {

    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/get-leads', [DashboardController::class, 'getLeads'])->name('getLeads');
    Route::post('/dashboard-data', [DashboardController::class, 'getDashboardData']);


    // fieldroute
    Route::get('/setup/fields', [FieldController::class, 'FieldIndex']);
    Route::POST('/savedetails', [FieldController::class, 'savedetails']);
    Route::get('/setup/viewform', [FieldController::class, 'viewForm']);
    Route::POST('/updatestatus', [FieldController::class, 'updatestatus']);
    Route::get('/editform/{id}', [FieldController::class, 'editForm']);
    Route::POST('/updateformdetails', [FieldController::class, 'updateformdetails']);
    Route::GET('/deletecolumn/{id}/{categoryid}/{subcatid}/{formtext}', [FieldController::class, 'deletecolumn']);

    // lead designation and department

    Route::get('/master/designation-department', [MasterController::class, 'viewde'])->name('master.dd');
Route::post('/master/store', [MasterController::class, 'store'])->name('master.store');
Route::get('/master/edit/{id}', [MasterController::class, 'edit'])->name('master.edit');
Route::post('/master/update/{id}', [MasterController::class, 'update'])->name('master.update');
Route::delete('/master/delete/{id}', [MasterController::class, 'destroy'])->name('master.delete');
Route::post('/master/quotation-field/store', [MasterController::class, 'storeQuotationField'])->name('master.quotation-field.store');
Route::get('/master/quotation-field/{type}/{id}/edit', [MasterController::class, 'editQuotationField'])->name('master.quotation-field.edit');
Route::post('/master/quotation-field/{type}/{id}/update', [MasterController::class, 'updateQuotationField'])->name('master.quotation-field.update');
Route::delete('/master/quotation-field/{type}/{id}', [MasterController::class, 'destroyQuotationField'])->name('master.quotation-field.delete');
Route::get('/get-designations', [MasterController::class, 'getDesignations']);
Route::get('/get-departments', [MasterController::class, 'getDepartments']);


    // userrout
    Route::view('/users', 'pages.user')->middleware('can:users-display');
    Route::post('/adduser', [UserController::class, 'store'])->name('users.store')->middleware('can:users-create');
    Route::post('/updateuser', [UserController::class, 'update'])->name('users.update')->middleware('can:users-edit');
    Route::post('/editpassword', [UserController::class, 'updatePassword'])->middleware('can:users-edit');
    Route::get('/get-users-by-role', [UserController::class, 'getUsersByRole'])->name('users.getByRole');
    Route::get('/delete-user/{id}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('can:users-delete');

    // productrout
    Route::get('/products', [ProductController::class, 'products'])->name('products')->middleware('can:products-display');

    // product category
    Route::get('/product-categories', [ProductController::class, 'productCategoryList'])->name('product-categories')->middleware('can:product-categories-display');
    Route::post('/add-category-action', [ProductCategoryController::class, 'addCategoryFunction'])->middleware('can:product-categories-create');
    Route::post('/edit-category-action', [ProductCategoryController::class, 'editCategoryFunction'])->middleware('can:product-categories-edit');
    Route::get('/delete-category/{catId}', [ProductCategoryController::class, 'deleteCategoryFunction'])->middleware('can:product-categories-delete');
    // web.php
Route::post('/ajax/add-segment', [ProductCategoryController::class, 'ajaxStore'])
    ->name('ajax.segment.store')->middleware('can:product-categories-create');


    // product sub categories
    Route::get('/product-sub-categories', [ProductSubCategoryController::class, 'productSubCategoryList'])->middleware('can:product-categories-display');
    Route::get('/get-products-by-keyword', [ProductSubCategoryController::class, 'getProductsByKeyword']);
    Route::post('/add-sub-category-action', [ProductSubCategoryController::class, 'addSubCategoryFunction'])->middleware('can:product-categories-create');
    Route::post('/edit-sub-category-action', [ProductSubCategoryController::class, 'editSubCategoryFunction'])->middleware('can:product-categories-edit');
    Route::get('/delete-sub-category/{Id}', [ProductSubCategoryController::class, 'deleteSubCategoryFunction'])->middleware('can:product-categories-delete');
    // web.php
    Route::post('/ajax/add-sub-category',
    [ProductSubCategoryController::class, 'ajaxStore']
)->name('ajax.subcategory.store')->middleware('can:product-categories-create');

    Route::get('/quotation-groups', [QuotationProductController::class, 'manageImages'])
        ->name('quotation-group-images.index');
    Route::post('/quotation-group-images/{productGroup}', [QuotationProductController::class, 'updateImage'])
        ->name('quotation-group-images.update');
    Route::delete('/quotation-group-images/{productGroup}', [QuotationProductController::class, 'deleteImage'])
        ->name('quotation-group-images.delete');


    // products routes

    Route::view('/addproduct', 'pages.productadd')->middleware('can:products-create');
    Route::post('/addproduct', [ProductController::class, 'store'])->name('products.store')->middleware('can:products-create');
    Route::get('/updateProductData/{productId}', [ProductController::class, 'updateProductData'])->middleware('can:products-edit');
    Route::get('/productDelete/{productId}', [ProductController::class, 'productDelete'])->middleware('can:products-delete');
    Route::post('/updateProduct', [ProductController::class, 'updateProduct'])->name('products.update')->middleware('can:products-edit');
    Route::get('/get-products/{catId}', [ProductController::class, 'getProductData']);

    // products specifictaions routes

   Route::get('/product-specifications',            [ProductSpecificationController::class, 'specifications'])->name('product_specifications.index')->middleware('can:products-display');
Route::get('/get-product-specifications/{id}',   [ProductSpecificationController::class, 'getSpecifications'])->name('product_specifications.get');
Route::post('/product-specifications/store',     [ProductSpecificationController::class, 'store'])->name('product_specifications.store')->middleware('can:products-create');

Route::get(
    '/bulkupload_spec',
    [ProductSpecificationController::class, 'bulkUploadForm']
)->name('bulkupload_spec_form')->middleware('can:products-create');

Route::post(
    '/bulkupload_spec',
    [ProductSpecificationController::class, 'bulkUpload']
)->name('bulkupload_spec')->middleware('can:products-create');

    // custmorrout
    Route::get('/customer', [CustomerController::class, 'customer'])->name('pages.customer')->middleware('can:customer-display');
    Route::view('/addcustomer', 'pages.customeradd')->middleware('can:customer-create');
    Route::post('/savecustomer', [CustomerController::class, 'savecustomer'])->middleware('can:customer-create');
    Route::post('/check-customer-duplicate', [CustomerController::class, 'checkDuplicate'])->name('customer.check-duplicate');
    Route::post('/getfandfrepor', [Controller::class, 'getfandfrepor']);
    Route::get('/customer/sample-template', [CustomerController::class, 'downloadSampleTemplate'])->name('customer.sample-template');
    Route::get('/customer/{id}/edit', [CustomerController::class, 'edit'])->name('customer.edit')->middleware('can:customer-edit');
    Route::put('/customer/{id}', [CustomerController::class, 'update'])->name('customer.update')->middleware('can:customer-edit');
    Route::post('/customer/delete/{id}', [CustomerController::class, 'softDelete'])->name('customer.delete')->middleware('can:customer-delete');
    
    // Expo routes
    Route::get('/expo', [ExpoController::class, 'expo'])->name('pages.expo');
    Route::get('/addexpo', [ExpoController::class, 'create'])->name('pages.expo.add');
    Route::post('/saveexpo', [ExpoController::class, 'store'])->name('pages.expo.store');
    Route::get('/expo/{id}/edit', [ExpoController::class, 'edit'])->name('pages.expo.edit');
    Route::put('/expo/{id}', [ExpoController::class, 'update'])->name('pages.expo.update');
    Route::post('/expo/delete/{id}', [ExpoController::class, 'softDelete'])->name('pages.expo.delete');
    Route::get('/expo/sample-template', function (\Illuminate\Http\Request $request) {
        return app(LeadImportController::class)->downloadTemplate($request, 'expo');
    })->name('expo.sample-template');
    Route::post('/expo/bulk-upload', function (\Illuminate\Http\Request $request) {
        return app(LeadImportController::class)->import($request, 'expo');
    })->name('expo.bulk-upload');

    // leadroute
    Route::get('/viewleads', [Controller::class, 'index'])->name('pages.leads')->middleware('can:leads-display');
    Route::view('/addleads', 'pages.leadaddform')->middleware('can:leads-create');
    Route::get('/leads/sample-template', function (\Illuminate\Http\Request $request) {
        return app(LeadImportController::class)->downloadTemplate($request, 'general');
    })->name('leads.sample-template')->middleware('can:leads-create');
    Route::post('/leads/bulk-upload', function (\Illuminate\Http\Request $request) {
        return app(LeadImportController::class)->import($request, 'general');
    })->name('leads.bulk-upload')->middleware('can:leads-create');
    Route::post('/getfandfreports', [Controller::class, 'getfandfreports']);
    Route::post('/getfandfrep', [Controller::class, 'getfandfrep']);
    // ajax
    Route::post('/getProductGroup', [Controller::class, 'getProductGroupFunction']);
    Route::post('/saveleads', [Controller::class, 'saveleads'])->middleware('can:leads-create');
    Route::get('/get-customer-data/{id}', [CustomerController::class, 'getCustomerData']);
    Route::get('/get-customer-by-gst/{gst}', [CustomerController::class, 'getCustomerByGst']);
    Route::post('/lead/leupdate-Statusdf', [Controller::class, 'leupdateStatusdf'])->name('lead.leupdateStatusdf');
    Route::post('/lead/update-Statusdf', [Controller::class, 'updateStatusdf'])->name('lead.updateStatusdf');
    Route::post('/upload-document', [Controller::class, 'designapproval'])->name('document.upload');
    Route::get('/get-cities/{state_id}', function ($state_id) {
        return App\Models\Citylist::where('state_code', $state_id)->get();
    });
    Route::post('/leads/soft-delete/{id}', [Controller::class, 'softDelete'])->name('leads.softDelete')->middleware('can:leads-delete');

    Route::get('/lostleads', [Controller::class, 'viewlost'])->name('pages.losts');
    // sinlelead
    Route::get('/singlelead/{leadid}', [Controller::class, 'singleLead'])->name('single.lead')->middleware('can:leads-display');
    Route::POST('/saveleadsproduct', [Controller::class, 'saveleadsproduct'])->middleware('can:leads-create');
    Route::get('/lead-edit/{id}', [Controller::class, 'leadedit'])->name('lead.edit')->middleware('can:leads-edit');
    Route::POST('/updatelead', [Controller::class, 'updatelead'])->middleware('can:leads-edit');
    Route::post('/statusupdate', [Controller::class, 'updateStatus'])->name('status.update');


    // desih
    Route::get('/leaddesigns/{id}/edit', [LeadDesignController::class, 'edit'])->name('leaddesigns.edit')->middleware('can:design-edit');
    // Route::put('/leaddesigns/{id}', [LeadDesignController::class, 'update'])->name('leaddesigns.update');
    Route::post('/leaddesigns/update/{id}', [LeadDesignController::class, 'update'])->name('leaddesigns.update')->middleware('can:design-edit');
    Route::get('/deletedesigns/{id}', [LeadDesignController::class, 'softDeletedesign'])->middleware('can:design-delete');
    Route::get('/settings/assign', [AssignSettingController::class, 'edit'])
    ->name('assign.settings');

Route::post('/settings/assign', [AssignSettingController::class, 'update'])
    ->name('assign.settings.update');
Route::get('/get-design-specs/{category}/{type}', [Controller::class, 'getSpecs']);


Route::get('/design-spec-master', [DesignSpecificationMasterController::class, 'create'])
    ->name('design.spec.master.create');

Route::post('/design-spec-master/store', [DesignSpecificationMasterController::class, 'store'])
    ->name('design.spec.master.store');
    Route::get('/design-spec-master/{id}/edit',
    [DesignSpecificationMasterController::class, 'edit'])
    ->name('design.spec.master.edit');

Route::put('/design-spec-master/{id}/update',
    [DesignSpecificationMasterController::class, 'update'])
    ->name('design.spec.master.update');

Route::delete('/design-spec-master/{id}',
    [DesignSpecificationMasterController::class, 'destroy'])
    ->name('design.spec.master.delete');
    Route::get(
    '/design-specs/{category}/{type}',
    [DesignSpecificationMasterController::class, 'getSpecs']
)->name('design.specs.fetch');

Route::post('/design/assign-designer', [Controller::class, 'assignDesigner'])
    ->name('design.assign');
Route::get(
    '/design/specifications/{designId}',
    [Controller::class, 'viewSpecifications']
)->name('design.specifications.view');

Route::post('/design/upload-files', [Controller::class, 'uploadFiles'])
    ->name('design.upload.files');
Route::get('/designs/{design}/quotation', [Controller::class, 'viewQuotationd']);
Route::get('/designs/{design}/drawing', [Controller::class, 'viewDrawing']);

Route::post('/designs/move-to-quotation', [Controller::class, 'moveToQuotation']);
Route::post('/design/return', [Controller::class, 'returnDesign'])
    ->name('design.return');

Route::get('/design/edit-data/{designId}', [Controller::class, 'getEditData']);

Route::post('/design/update-after-return', [Controller::class, 'updateAfterReturn'])
    ->name('design.update.after.return');

Route::get('/design-engineer/designs', [Controller::class, 'designEngineerList'])
    ->middleware('auth')
    ->name('design.engineer.list');
Route::get('/design/{id}/edit', [Controller::class, 'edit'])
    ->name('design.edit');
    Route::post('/design/{id}/update-files', [Controller::class, 'updateFiles'])
    ->name('design.update.files');
Route::get('/design/drawings/{design}',
    [Controller::class, 'getuploadDesignDrawings']
)->name('design.drawings');

Route::post('/design/upload-drawings',
    [Controller::class, 'uploadDrawings']
)->name('design.upload.drawings');

Route::get(
    '/design/drawing-approval/{design}',
    [Controller::class, 'getDrawingApproval']
)->name('design.drawing.approval');

// 🔹 Save approval / rejection (SALES ONLY)
Route::post(
    '/design/drawing-approval',
    [Controller::class, 'saveDrawingApproval']
)->name('design.drawing.approval.save');
Route::get(
    '/design/rejected-drawings/{design}',
    [Controller::class, 'getRejectedDrawings']
);

Route::post(
    '/design/rejected-drawings/update',
    [Controller::class, 'updateRejectedDrawings']
);
Route::post('/design/approved-drawings', [Controller::class, 'approvedDrawings'])
    ->name('design.approved.drawings');

Route::post(
    '/design/send-drawing-mail',
    [Controller::class, 'sendSingleDrawingMail']
)->name('design.send.single.drawing.mail');

/* SEND ALL DRAWINGS MAIL */
Route::post(
    '/design/send-all-drawings-mail',
    [Controller::class, 'sendAllDrawingsMail']
)->name('design.send.all.drawings.mail');

    // quoto
    Route::get('/singlequota/{leadid}', [Controller::class, 'singlequota'])->name('single.quota')->middleware('can:quotation-display');
    Route::get('/createquota/{leadid}', [Controller::class, 'createquota'])->name('create.quota')->middleware('can:quotation-create');
    Route::get('/editquota/{quotaId}', [Controller::class, 'editquota'])->name('editquota.quota')->middleware('can:quotation-edit');
    Route::post('/leaddesigns/storedesign', [Controller::class, 'storedesign'])->name('leaddesigns.storedesign')->middleware('can:design-create');
    Route::post('/createestimate', [Controller::class, 'createestimate'])->name('createestimate')->middleware('can:quotation-create');
    Route::get('/quotation-pdf/{id}', [Controller::class, 'generatePDF'])->name('quotation.pdf')->middleware('can:quotation-display');
    Route::get('/quotation/view/{id}', [Controller::class, 'viewQuotation'])->name('quotation.view')->middleware('can:quotation-display');
    Route::view('/quopdf2', 'quotations.pdf2');
    Route::post('/shifandfreports', [Controller::class, 'shifandfreports']);
    Route::get('/quotation/download/{id}', [Controller::class, 'downloadQuotation'])->name('quotation.download')->middleware('can:quotation-display');

    Route::post('/approve-lead/{id}', [Controller::class, 'approvemovequto']);
    Route::get('/viewmovequto', [Controller::class, 'moveQuotationApproval']);
Route::post('/leads/update-closure-date', [Controller::class, 'updateClosureDate'])
    ->name('leads.update-closure-date');
    Route::post('/quotation/send-mail/{id}', [Controller::class, 'sendQuotationMail']);
    Route::post('/quotation/mark-sent/{id}', [Controller::class, 'markQuotationSent'])->name('quotation.mark-sent');
Route::get('/settings/mail', [MailsettingController::class, 'edit'])->name('mail.settings');
Route::post('/settings/mail', [MailsettingController::class, 'update'])->name('mail.settings.update');

Route::get('/quotation-charge-approvals', [App\Http\Controllers\QuotationChargeApprovalController::class, 'pending'])->name('quotation.charges.index');
Route::post('/quotation-charge-approvals/update/{quotation}', [App\Http\Controllers\QuotationChargeApprovalController::class, 'decide'])->name('quotation.charge.update');
Route::post('/quotation-charge-approvals/decide/{quotation}', [App\Http\Controllers\QuotationChargeApprovalController::class, 'decide'])->name('quotation.charges.decide');

Route::get('/quotation-term-approvals', [App\Http\Controllers\QuotationChargeApprovalController::class, 'pendingTerms'])->name('quotation.terms.index');
Route::post('/quotation-term-approvals/update/{id}', [App\Http\Controllers\QuotationChargeApprovalController::class, 'decideTerms'])->name('quotation.term.update');
Route::post('/quotation-term-approvals/decide/{id}', [App\Http\Controllers\QuotationChargeApprovalController::class, 'decideTerms'])->name('quotation.terms.decide');

    Route::post('/approve-quto/{id}', [Controller::class, 'approvequto'])->middleware('can:quotation-approval');
    Route::post('/approve-disc/{id}', [Controller::class, 'approvedisc'])->middleware('can:discount-approval');
    Route::get('/viewqutoapp', [Controller::class, 'viewQuotationApproval'])->middleware('can:quotation-approvals-view');
    Route::post('/leads/update-qutostatus', [Controller::class, 'updateQutoStatus'])->name('leads.update-qutostatus');
    Route::post('/approve-fre/{id}', [Controller::class, 'approvefre']);
    Route::post('/approve-terms-ajax', [Controller::class, 'approveTermsFunctions'])->middleware('can:terms-approval');

    Route::get('/lead/design-drawings/{leadId}', [Controller::class, 'getDesignDrawings']);


    // orf
    Route::get('/orf/{id}', [Controller::class, 'createorf'])->name('createorf')->middleware('can:orf-create');
    Route::post('/store-orf', [OrfController::class, 'store'])->name('orf.store')->middleware('can:orf-create');
    Route::get('/vieworf', [OrfController::class, 'vieworf'])->name('pages.vieworf')->middleware('can:orf-approval');
    Route::get('/vieworfapp', [OrfController::class, 'vieworfapp'])->name('pages.vieworfapp')->middleware('can:orf-display');
    Route::post('/orf/insert-statusorf', [OrfController::class, 'insertStatusorf'])->name('orf.insertStatusorf')->middleware('can:orf-approval');
    Route::post('/orf/update-csstatus', [OrfController::class, 'updatecsStatusorf'])->name('orf.updatecsStatusorf')->middleware('can:orf-approval');
    Route::view('/invoice', 'quotations.invoice');
    Route::get('/orfdetail/view/{id}', [OrfController::class, 'vieworfinvoice'])->name('orfdetail.view')->middleware('can:orf-display');
    Route::get('/orf/edit/{id}', [OrfController::class, 'edit'])->name('orf.edit')->middleware('can:orf-edit');
    Route::put('/orf/update/{id}', [OrfController::class, 'update'])->name('orf.update')->middleware('can:orf-edit');

    Route::get('/orf/{orf}/attachments/{type}', [OrfController::class, 'getAttachments']);

Route::delete('/orf-attachment/{id}', [OrfController::class, 'deleteAttachment']);


    // activities
    Route::view('/activities', 'pages.createactivities')->name('activities');

    // quotatu
    Route::get('/viewconverted', [Controller::class, 'viewconvert'])->name('pages.viewconverted')->middleware('can:design-display');
    Route::get('/viewquota', [Controller::class, 'viewquota'])->name('pages.viewquotation')->middleware('can:quotation-display');




    // reports

    // Route::view('/viewreport', 'pages.reports');
    Route::get('/viewreport', [ReportController::class, 'showReports'])->middleware('can:reports-display');
    Route::get('/equipfilter', [ReportController::class, 'equipfilter'])->name('equipfilter');
    Route::get('/sparserfilter', [ReportController::class, 'sparserfilter'])->name('sparserfilter');
    Route::get('/sparserbillfilter', [ReportController::class, 'sparserbillfilter'])->name('sparserbillfilter');
    Route::get('/equipbillfilter', [ReportController::class, 'equipbillfilter'])->name('equipbillfilter');
    Route::get('/openorderfilter', [ReportController::class, 'openorderfilter'])->name('openorderfilter');
    Route::get('/monbillfilter', [ReportController::class, 'monbillfilter'])->name('monbillfilter');
    Route::get('/monbookfilter', [ReportController::class, 'monbookfilter'])->name('monbookfilter');



    // vendor routes
    Route::get('/vendors', [VendorController::class, 'viewVendors'])->name('pages.vendor.vendorList')->middleware('can:vendors-display');
    Route::view('/add-vendors', 'pages.vendor.addVendors')->name('pages.vendor.addVendors')->middleware('can:vendors-create');
    Route::get('/edit-vendors/{vendorId}', [VendorController::class, 'vendorEditDetails'])->middleware('can:vendors-edit');
    Route::get('/delete-vendors/{vendorId}', [VendorController::class, 'deleteVendor'])->middleware('can:vendors-delete');
    Route::post('/saveVendors', [VendorController::class, 'saveVendors'])->name('add-vendors')->middleware('can:vendors-create');
    Route::post('/editVendors', [VendorController::class, 'editVendors'])->name('edit-vendors')->middleware('can:vendors-edit');
    Route::get('/get-vendors/{productId}', [VendorController::class, 'getVendorDetails']);
    Route::get('/get-vendors-ajax/{type}', [VendorController::class, 'getVendorListAjax']);

    // purchase order routes
    Route::get('/purchase-order-details', [PurchaseOrderController::class, 'viewPurchaseOrder'])->name('purchase-order-details-page')->middleware('can:purchase-order-display');
    Route::get('/create-po', [PurchaseOrderController::class, 'createPoView'])->name('pages.vendor.create-po')->middleware('can:purchase-order-create');
    Route::get('/po-delete/{poId}', [PurchaseOrderController::class, 'deletePO'])->middleware('can:purchase-order-delete');
    Route::get('/po-invoice/{invoiceId}', [PurchaseOrderController::class, 'viewPOInvoice'])->where('invoiceId', '.*');
    Route::post('/save-po', [PurchaseOrderController::class, 'savePo'])->middleware('can:purchase-order-create');
    Route::get('/edit-po-data/{poNo}', [PurchaseOrderController::class, 'poEditDetails'])->where('poNo', '.*')->middleware('can:purchase-order-edit');
    Route::post('/edit-po', [PurchaseOrderController::class, 'editPo'])->middleware('can:purchase-order-edit');
    Route::get('/order-po-tracking', [PurchaseOrderController::class, 'orderPoTracking'])->name('order-po-tracking')->middleware('can:purchase-order-display');
    Route::post('/vendor/get-po-details', [PurchaseOrderController::class, 'getPoDetails']);
    Route::post('/vendor/get-po-prodetails', [PurchaseOrderController::class, 'getPoProDetails']);
    Route::post('/customer/get-by-gst', [PurchaseOrderController::class, 'getCustomerByGst']);
    Route::post('/vendor/get-branches', [PurchaseOrderController::class, 'getVendorBranches']);
    Route::get('/po-download-merged/{poNo}', [PurchaseOrderController::class, 'downloadMergedPo'])->where('poNo', '.*');


    // purchase entry routes
    Route::view('/purchase-entry', "pages.vendor.purchase_entry.purchaseEntry")->name('purchase-entry-details')->middleware('can:purchase-entry-display');
    Route::view('/add-purchase-entry', 'pages.vendor.purchase_entry.addPurchaseEntry')->name('pages.vendor.add-purchase-entry')->middleware('can:purchase-entry-create');
    Route::get('/pe-delete/{peId}/{oldQty}/{proId}', [PurchaseEntryController::class, 'deletePE'])->middleware('can:purchase-entry-delete');
    Route::post('/save-pe', [PurchaseEntryController::class, 'savePE'])->middleware('can:purchase-entry-create');
    Route::post('/edit-pe', [PurchaseEntryController::class, 'editPE'])->middleware('can:purchase-entry-edit');
    Route::get('/edit-pe-data/{peId}', [PurchaseEntryController::class, 'editPEDetails'])->middleware('can:purchase-entry-edit');
    Route::post('/vendor/get-pe-pro-details', [PurchaseEntryController::class, 'getPEProductDetails']);

    // product stock routes

    Route::view('/product-stocks', "pages.vendor.productStock")->middleware('can:product-stock-display');
    Route::get('/get-pro-stock-details/{productId}', [PurchaseStockController::class, 'getStockDetails']);
    Route::post('/product-stock-edit', [PurchaseStockController::class, 'productStockUpdation'])->middleware('can:product-stock-edit');
    Route::post('/product-stock-alert-update', [PurchaseStockController::class, 'productStockAlertEdit']);
    Route::get('/get-product-stocks-count', [PurchaseStockController::class, 'getProductStocksCount']);

    // terms&conditions
    Route::get('/terms', [TermController::class, 'viewterms'])->name('terms.view')->middleware('can:terms-and-condition-display');
    Route::view('/addterms', 'pages.addterms')->name('pages.addterms')->middleware('can:terms-and-condition-create');
    Route::post('/saveterms', [TermController::class, 'store'])->name('terms.store')->middleware('can:terms-and-condition-create');
    Route::get('/terms/edit/{id}', [TermController::class, 'edit'])->name('terms.edit')->middleware('can:terms-and-condition-edit');
    Route::put('/terms/update/{id}', [TermController::class, 'update'])->name('terms.update')->middleware('can:terms-and-condition-edit');
    Route::delete('/terms/delete/{id}', [TermController::class, 'destroy'])->name('terms.destroy')->middleware('can:terms-and-condition-delete');
    Route::post('/terms/approve/{id}', [TermController::class, 'approve'])->name('terms.approve');
    Route::get('/products/by-segment/{id}', [TermController::class, 'getProductsBySegment']);
    Route::get('/subcategories/by-segment/{id}', [TermController::class, 'getSubCategoriesBySegment']);


    // roles routes
    Route::view('/roles', 'pages.users.roles')->name('roles')->middleware('can:roles-view');
    Route::view('/addrole', 'pages.roleadd')->middleware('can:roles-create');
    Route::post('/addrole', [UserController::class, 'storerole'])->name('roles.storerole')->middleware('can:roles-create');
    Route::post('/updateRole', [UserController::class, 'updateRole'])->name('roles.updateRole')->middleware('can:roles-edit');
    Route::get('/deleteRole/{roleId}', [UserController::class, 'deleteRole'])->name('roles.deleteRole')->middleware('can:roles-delete');

    // permission routes
    Route::view('/permissions', 'pages.users.permissions')->name('permissions')->middleware('can:permissions-view');
    Route::post('/createPermission', [PermissionController::class, 'createPermission'])->middleware('can:permissions-create');
    Route::post('/editPermission', [PermissionController::class, 'editPermission'])->middleware('can:permissions-edit');
    Route::get('/add-permissions/{roleId}', [PermissionController::class, 'addPermissionView'])->middleware('can:permissions-edit');
    Route::post('/allocate-permission', [PermissionController::class, 'allocatePermission'])->middleware('can:permissions-edit');

    // ajax routes
    Route::post('/check-role-name', [PermissionController::class, 'checkRolefFunction']);
    Route::get('/lead-filter/{statusId}', [Controller::class, 'leadFilterFunction']);

    Route::get('/get-products-by-category/{categoryId}/{subCategoryId}', [ProductController::class, 'getByCategory']);
    Route::get('/get-sub-category/{categoryId}', [ProductSubCategoryController::class, 'getSubCategory']);
    Route::get('/quotation-filter', [Controller::class, 'filterQuotation']);
    Route::get('/get-settings-details', [AdminSettingController::class, 'getSettingsDetails']);

    Route::get('/design-filter', [Controller::class, 'filterdesign']);

    // bulk upload]
    Route::post('/customer-bulk-upload', [CustomerController::class, 'bulkUploadCustomers'])->name('customer.bulk-upload');
    Route::post('/product-bulk-upload', [ProductController::class, 'bulkUploadProduct']);
    Route::get('/bulk-upload-master',  [ProductController::class, 'bulkUploadMasterView'])->name('bulk-upload.master.view');
Route::post('/bulk-upload-master', [ProductController::class, 'bulkUploadMaster'])->name('bulk-upload.master');

    // setting page route
    Route::view('/settings', 'pages.admin_settings');
    Route::post('/settings-update-form', [AdminSettingController::class, 'settingFunction']);


    // hrmodule

    // Department
   Route::get('/department', [DepartmentController::class,'department']);
   Route::post('/designationinser', [DepartmentController::class, 'store'])->name('department.store');
   Route::post('designationupdate', [DepartmentController::class, 'designationupdate'])->name('department.update');
   Route::delete('/department/{id}', [DepartmentController::class, 'destroy'])->name('department.destroy');



    // addemployee
     Route::get('/employeelist', [AddEmployeeController::class, 'employeelist'])->name('employee.list');
     Route::view('/addemployee', 'pages.hr.addemployee');
     Route::post('/Addemployeeinsert', [AddEmployeeController::class, 'Addemployeeinsert'])->name('employee.insert');
     Route::delete('/employee/{id}', [AddEmployeeController::class, 'destroy'])->name('employee.destroy');
     Route::get('/editemployee/{id}', [AddEmployeeController::class, 'edit'])->name('employee.edit');
     Route::post('/editemployeeupdate', [AddEmployeeController::class, 'editemployeeupdate'])->name('employee.update');
     Route::get('/employee-report/{id}', [AddEmployeeController::class, 'showEmployeeReport'])->name('employee.report');

     // addasset
     Route::view('/addassets', 'pages.hr.addassets');
     Route::get('/employeeassetlist', [AddassetController::class,'assetdetaillist']);
     Route::post('addnewassetinsert',[AddassetController::class,'addnewassetinsert']);
     Route::get('gettsupprod',[AddassetController::class,'gettsupprod']);
     Route::post('assetdetailinsert',[AddassetController::class,'assetdetailinsert']);
     Route::get('/employeeassetreport/{id}', [AddassetController::class,'employeeassetreport']);
     Route::get('/employee-assets/edit/{id}', [AddassetController::class, 'edit']);
     Route::post('/employee-assets/return', [AddassetController::class, 'return'])->name('employee-assets.return');

// addadvance
Route::get('/employee-advance/all', [EmployeeAdvanceController::class, 'listshow'])->name('employee.advance.index');
Route::get('/employee-advance/create', [EmployeeAdvanceController::class, 'create'])->name('employee.advance.create');
Route::post('/employee-advance/store', [EmployeeAdvanceController::class, 'store'])->name('employee.advance.store');
Route::get('/employee-advance/employee/{id}', [EmployeeAdvanceController::class, 'show'])->name('employee.advance.show');

// attendance
Route::get('/attendancelist', [AttendanceController::class, 'attendancelist'])->name('attendance.list');
Route::post('/attendance/punchin', [AttendanceController::class, 'punchIn'])->name('attendance.punchin');


Route::get('/addholiday', [AttendanceController::class,'addholiday']);
Route::post('/Addholidayinsert', [AttendanceController::class, 'Addholidayinsert'])->name('Addholidayinsert');
Route::post('editholidayinsert', [AttendanceController::class,'editholidayinsert']);
Route::delete('deleteholiday/{id}', [AttendanceController::class, 'deleteholiday']);

Route::get('/get-products-by-category/{categoryId}', [ProductController::class, 'getProductsByCategory']);

Route::get('/date-of-validity', [DateOfValidityController::class, 'dovvv']);
Route::post('/date-of-validity/store', [DateOfValidityController::class, 'store'])->name('dov.store');
Route::get('/date-of-validity/edit/{id}', [DateOfValidityController::class, 'edit']);
Route::post('/date-of-validity/update/{id}', [DateOfValidityController::class, 'update']);
Route::delete('/date-of-validity/delete/{id}', [DateOfValidityController::class, 'delete']);




Route::middleware(['auth'])->prefix('master')->group(function () {
    Route::post('accessories/bulk-upload', [AccessoryController::class, 'bulkUpload'])->name('accessories.bulk_upload');
    Route::resource('accessories', AccessoryController::class);
});

// AJAX routes for accessories (called directly from root URL in JS)
Route::get('/get-accproducts/{category}', [AccessoryController::class, 'getProducts']);
Route::match(['get', 'post'], '/fetchTableData', [AttendanceController::class, 'attendancelist']);
Route::get('/api/get-accessories-by-product/{id}', [AccessoryController::class, 'fetchTableData']);

Route::get('/quotation-charge-approvals', [App\Http\Controllers\QuotationChargeApprovalController::class, 'pending'])->name('quotation.charges.index');
Route::post('/quotation-charge-approvals/update/{quotation}', [App\Http\Controllers\QuotationChargeApprovalController::class, 'decide'])->name('quotation.charge.update');
Route::post('/quotation-charge-approvals/decide/{quotation}', [App\Http\Controllers\QuotationChargeApprovalController::class, 'decide'])->name('quotation.charges.decide');

Route::get('/session-keep-alive', function () {
    return response()->json([
        'status' => 'alive',
        'csrf' => csrf_token(),
    ]);
})->name('session.keepalive');

});