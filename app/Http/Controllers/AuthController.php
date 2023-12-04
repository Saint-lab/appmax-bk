<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Settings;
use App\Models\UserVerify;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Mail;
use Session; 
use DB;

class AuthController extends Controller
{
    //

    public function register(Request $request){ 
    	$fields = $request->validate([
    		'name' => 'required|string',
    		'email' => 'required|string|unique:users,email',
            //'username' => 'required|string|unique:users,username',
    		'password' => 'required|string|confirmed'
    	]); 

    	$user = User::create([
    		'name' => $fields['name'],
    		'email' => $fields['email'],
            //'username' => $fields['username'],
    		'password' => bcrypt($fields['password'])
    	]);

      Settings::create([
        'user_id' => $user->id,
       ]);
       
      // $code = Str::random(64);
      //   UserVerify::create([
      //       'user_id' => $user->id, 
      //       'token' => $code,
      //   ]);

      //   Mail::send('emails.emailVerificationEmail', ['token' => $code], function($message) use($request){
      //       $message->to($request->email);
      //       $message->subject('Email Verification Mail');
      //   });

    	$token = $user->CreateToken('myapptoken')->plainTextToken;

    	$response = [
        'status' => 'success',
    		// 'user' => $user,
    		// 'token' => $token
    	];

    	return response($response, 200);
    }

     public function login(Request $request){
    	$fields = $request->validate([
    		'email' => 'required|string',
    		'password' => 'required|string'
    	]);

    	//check user email exist
    	$user = User::where('email', $fields['email'])->first();

    	if(!$user || !Hash::check($fields['password'], $user->password)){

    		return response([
          'status' => 'error',
				'message' => 'Bad credential',
    		], 401);
    	}

    	$token = $user->CreateToken('myapptoken')->plainTextToken;

    	$response = [
        'status' => 'success',
    		'user' => $user,
    		'token' => $token
    	];

    	return response($response, 200);
    }

    public function verifyAccount($token){
        $verifyUser = UserVerify::where('token', $token)->first();
        if(!is_null($verifyUser)){
            $user = $verifyUser->user;
            if(!$user->is_email_verified){
                $verifyUser->user->is_email_verified = 1;
                $verifyUser->user->save();
                $response = [
            'message' => 'Email Verified, You can now login.',
         ];

        return response($response, 200);
            }else{
             $response = [
            'message' => 'Email Alrady Verified.',
         ]; 
         return response($response, 200);  
            }
        }
    }

public function reset(Request $request){
   $fields = $request->validate([
            'email' => 'required|string|email',
        ]);
$count = DB::table('users')->where('email', $fields['email'])->count();
if($count != 0){
    $token = Str::random(64);
$reset = DB::table('password_resets')->where('email', $fields['email'])->count();
 if($reset == 0){
    DB::table('password_resets')->insert([
              'email'=> $fields['email'],
               'token' => $token,
               ]);
}else{
  DB::table('password_resets')->where('email', $fields['email'])->update([
              'token' => $token,
              'status' => 0,
               ]); 
}
   Mail::send('emails.emailResetEmail', ['token' => $token], function($message) use($request){
            $message->to($request->email);
            $message->subject('Reset Password Email');
        });
  $response = [
            'status' => 'success',
            'message' => 'Reset Password Link sent to your email',
        ];

        return response($response, 200);       
}else{
   return response([
                'status' => 'error',
                'message' => 'User Not Found',
            ], 401); 
 }
      
}

public function resetAccount($token)
{
   $reset = DB::table('password_resets')->where('token', $token)->where('status', 0)->first();
   if(!$reset){
     return response([
                'status' => 'error',
                'message' => 'Token Not Found',
            ], 401); 
 }     
   else{
   $response = [
            'status' => 'success',
            'reset' => $reset,
        ];

        return response($response, 200);
    }
}

 public function saveResetAccount(Request $request){ 
   $this->validate($request, [
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|confirmed|min:4',
        ]);
$reset = DB::table('password_resets')->where('token', $request->token)->orderBy('created_at', 'desc')->first();
      
        if ($reset->status == 1) {
             return response([
                'status' => 'error',
                'message' => 'Invalid Token',
            ], 401); 
        }

        $password = bcrypt($request->password);
        DB::table('users')->where('email', $reset->email)->update([
              'password'=> $password,
             ]);
       DB::table('password_resets')->where('token', $request->token)->update([
              'status'=> 1,
             ]);
$response = [
            'status' => 'success',
            'message' => 'Password Reset Successfully',
        ];

        return response($response, 200);

 }



    public function logout(Request $request){
    	auth()->user()->tokens()->delete();

    	$response = [
            'status' => 'success',
            'message' => 'User Logout',
        ];

        return response($response, 200);
    }
}
