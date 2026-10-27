<?php

namespace App\Http\Controllers;

use App\Models\activitylog;
use App\Models\Leadproduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller {
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */

    // public function index() {
    //     //
    // }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */

    public function create() {
        //
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */

    public function store( Request $request ) {
        //
    }

    /**
    * Display the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

    public function show( $id ) {
        //
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

    public function edit( $id ) {
        //
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

    public function update( Request $request, $id ) {
        //
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  int  $id
    * @return \Illuminate\Http\Response
    */

    public function destroy( $id ) {
        //
    }

    public function login( Request $request ) {
        $request->validate( [
            'username' => 'required',
            'password' => 'required'
        ] );

        $username = $request->input( 'username' );
        $password = $request->input( 'password' );

        $usercheck = DB::table( 'users' )
        ->where( 'mobilenumber', $request->input( 'username' ) )
        ->first();

        if ( $usercheck ) {
            if ( $usercheck->userstatus == 'Active' ) {
                $credentials = [
                    'mobilenumber' => $request[ 'username' ],
                    'password' => $request[ 'password' ],
                ];

                $test = Auth::attempt( $credentials );

                if ( $test ) {
                    $user = User::where( 'mobilenumber', $username )->first();
                    Auth::login( $user );
                    $userid = Auth::user()->userid;

                    // return redirect( '/dashboard' )->with( 'message', 'User Login Successfully...' );
                    return response()->json( [ 'status'=>'success', 'User Login Successfully', 200 ] );
                } else {
                    // return back()->with( 'error', 'Username or Password Incorrect....' );
                     return response()->json( [ 'status'=>'error', 'Invalid Credentials', 500 ] );
                }
            } else {
                // return back()->with( 'success', 'User Not Activated...' );
                return response()->json( [ 'status'=>'error', 'User Not Activated', 500 ] );
            }
        } else {
            // return back()->with( 'success', 'User Not Available...' );
            return response()->json( [ 'status'=>'error', 'User Not Available', 500 ] );
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        return redirect('/')->with('success', 'Logged Out..!');
    }
}
