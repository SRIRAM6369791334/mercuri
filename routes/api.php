<?php

use App\Http\Controllers\ApiControllers\CountryControllerApi;
use App\Http\Controllers\ApiControllers\DashboardControllerApi;
use App\Http\Controllers\ApiControllers\DesignControllerApi;
use App\Http\Controllers\ApiControllers\GoogleAuthController;
use App\Http\Controllers\ApiControllers\LeadControllerApi;
use App\Http\Controllers\ApiControllers\LoginControllerApi;
use App\Http\Controllers\ApiControllers\OrfControllerApi;
use App\Http\Controllers\ApiControllers\QuotationControllerApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
//MARK:auth
Route::post('stafflogin', [LoginControllerApi::class, "stafflogin"]);


//MARK:leadController
Route::get("leadData/{userId}", [LeadControllerApi::class, "leadData"]);
Route::get("sourceEnquiryDropdown", [LeadControllerApi::class, "sourceEnquiryDropdown"]);
Route::get("customersList/{cusId?}", [LeadControllerApi::class, "customersList"]);


//MARK: customersList($cusId)
Route::post("saveleads", [LeadControllerApi::class, "saveleads"]);
Route::post("updateStatuslead", [LeadControllerApi::class, "updateStatuslead"]);

//MARK:Design Controller
Route::post("storedesign", [DesignControllerApi::class, "storedesign"]);
Route::post("designapproval", [DesignControllerApi::class, "designapproval"]);
Route::get("designleadData/{userId}", [DesignControllerApi::class, "designleadData"]);


//MARK:country controller

Route::get("countryData", [CountryControllerApi::class, "countryData"]);
Route::get("stateData", [CountryControllerApi::class, "stateData"]);
Route::get("salesManagerData", [CountryControllerApi::class, "salesManagerData"]);
Route::get("proceedData", [CountryControllerApi::class, "proceedData"]);
Route::get("cityData/{stateId}", [CountryControllerApi::class, "cityData"]);
Route::get("leadStatusData", [CountryControllerApi::class, "leadStatusData"]);
Route::get("categoriesData", [CountryControllerApi::class, "categoriesData"]);
Route::get("productData/{categories?}", [CountryControllerApi::class, "productData"]);

//MARK: quotation controller
Route::get("quotationLeadData/{userId}", [QuotationControllerApi::class, "quotationLeadData"]);
Route::post('/quotation-filter-api', [QuotationControllerApi::class, 'filterQuotationFunction']);

// quotationLeadData

//MARK:ORF Controller
Route::get("getOrfData", [OrfControllerApi::class, "getOrfData"]);
Route::get("getOrfApprovalPendingData", [OrfControllerApi::class, "getOrfApprovalPendingData"]);
Route::get('/test-fcm', [GoogleAuthController::class, 'sendNotification']);

//MARK: Dashboard controller
Route::get('getDashboardData/{period?}/{fromdate?}/{todate?}/{assignedto?}', [DashboardControllerApi::class, 'getDashboardData']);
Route::get('getLeadSummaryCount', [DashboardControllerApi::class, 'getLeadSummaryCount']);
Route::get('getUserData/{id?}', [DashboardControllerApi::class, 'getUserData']);
Route::get('getProductCategoriesData/{id?}', [DashboardControllerApi::class, 'getProductCategoriesData']);
Route::get('getProductData/{id?}', [DashboardControllerApi::class, 'getProductData']);
Route::get('getCustomerData/{id?}', [DashboardControllerApi::class, 'getCustomerData']);
Route::get('getTermData/{id?}', [DashboardControllerApi::class, 'getTermData']);
Route::get('getRoles', [DashboardControllerApi::class, 'getRoles']);
Route::post('updateUser/{id}', [DashboardControllerApi::class, 'updateUser']);
Route::post('updateProductCategory', [DashboardControllerApi::class, 'updateProductCategory']);
Route::post('updateProductData', [DashboardControllerApi::class, 'updateProductData']);
Route::post('updateCustomerData', [DashboardControllerApi::class, 'updateCustomerData']);
Route::post('updateTermData', [DashboardControllerApi::class, 'updateTermData']);
Route::post('updateTermApprovalStatus', [DashboardControllerApi::class, 'updateTermApprovalStatus']);
Route::post('deleteTerm', [DashboardControllerApi::class, 'deleteTerm']);
Route::post('deleteCustomer', [DashboardControllerApi::class, 'deleteCustomer']);
Route::post('deleteProducts', [DashboardControllerApi::class, 'deleteProducts']);
Route::post('deleteProductsCategories', [DashboardControllerApi::class, 'deleteProducts14Categories']);

// Route::get('Overduelist/{userId?}/{companyId?}',[HomeController::class,"Overduelist"]);