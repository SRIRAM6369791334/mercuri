<?php

namespace App\Http\Controllers\ApiControllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;


class LoginControllerApi extends Controller
{
   public function stafflogin(Request $request){
       
       
  
    // $validator = Validator::make($request->all(),[
    //     'mobilenum' => 'required',
    //     'password' =>'required',
    // ]);
    // if( $validator -> fails()){
    //     return $validator ->errors();
    // }else{
    //     $mobilenum = $request->mobilenum;
    //     $password = $request->password;

    //     $role = DB::table('users')
    //     ->where('mobilenum',$mobilenum)
    //     ->where('userstatus','Active')
    //     ->first();

    //     $hashedPassword = $role->password;
    //     if(Hash::check($password,$hashedPassword)){
    //     return response()->json([
    //     'status' => '200',
    //     'message' => 'Login Successfully',
    //     'id'=>$role->id,
    //     'phone'=>$role->mobilenum,
    //     'name'=>$role->name,
    //     'role'=>$role->role,
    //     'userstatus'=>$role->userstatus
        
    //     ]);
    //     }
    //     else{
    //                 return response()->json([
    //                     'status' => '201',
    //                     'message' => 'Invalid Credentials'
    //                     ]);
    //             }
    //  }
    
    
     $user = DB::table('users')
                    ->where('mobilenumber', $request->mobilenum)
                    ->first();
                if (!$user) {
                    return response()->json([
                        "status"=>"201",
                        "message" => "Phone number does not exists"
                        ]);
                }
    
   $validator = Validator::make($request->all(), [
        'mobilenum' => 
            'required',

        'password' => 'required',
    ]);

    if ($validator->fails()) {
        return $validator->errors();
    } else {
        $mobilenum = $request->mobilenum;
        $password = $request->password;

        $role = DB::table('users')
            ->where('mobilenumber', $mobilenum)
            ->first();
            
            // dd($role);

        if ($role) {
            if ($role->userstatus == 'Active' && Hash::check($password, $role->password)) {
                return response()->json([
                    'status' => '200',
                    'message' => 'Login Successfully',
                    'id' => $role->id,
                    'phone' => $role->mobilenumber,
                    'name' => $role->name,
                    'role' => $role->role,
                    'userstatus' => $role->userstatus
                ]);
            } else {
                return response()->json([
                    'status' => '201',
                    'message' => ($role->userstatus == 'Inactive') ? 'User not active' : 'Invalid  Credentials'
                ]);
            }
        } else {
            return response()->json([
                'status' => '201',
                'message' => 'Invalid Credentials'
            ]);
        }
    }
   }
}