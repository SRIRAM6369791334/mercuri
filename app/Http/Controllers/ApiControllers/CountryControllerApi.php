<?php


namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CountryControllerApi extends Controller
{
    public function countryData(){
        $data = DB::table('countries')->where('dial_code','=','+91')->get(); 
        return ResponseHelper::successResponseData('Success', 'successfully fetch data', $data);
 
    }
    public function stateData(){
        $data = DB::table('state_list')->get(); 
        return ResponseHelper::successResponseData('Success', 'successfully fetch data', $data);
 
    }
    public function cityData($stateId){
        $data = DB::table('city_list')->where('state_code','=',$stateId)->get(); 
        return ResponseHelper::successResponseData('Success', 'successfully fetch data', $data);
 
    }
    public function salesManagerData(){
        $data = DB::table('users')->get(); 
        return ResponseHelper::successResponseData('Success', 'successfully fetch data', $data);
 
    }
    public function proceedData(){
        $data = DB::table('dropdowndatas')->where('formid','=','35')->get(); 
        return ResponseHelper::successResponseData('Success', 'successfully fetch data', $data);
 
    }
    public function leadStatusData(){
        $data = DB::table('dropdowndatas')->where('formid','=','6')->get(); 
        return ResponseHelper::successResponseData('Success', 'successfully fetch data', $data);
 
    }
    public function categoriesData(){
        $data = DB::table('product_categories_tb')->get(); 
        return ResponseHelper::successResponseData('Success', 'successfully fetch data', $data);
 
    }
    // public function productData($categories){
    //     $data = DB::table('products')->where('Productcategory','=',$categories)->get(); 
    //     return ResponseHelper::successResponseData('Success', 'successfully fetch data', $data);
 
    // }
    
    public function productData($categories = null) {
        $query = DB::table('products');
    
        if (!empty($categories)) {
            $query->where('Productcategory', '=', $categories);
        }
    
        $data = $query->get();
    
        return ResponseHelper::successResponseData('Success', 'Successfully fetched data', $data);
    }
}