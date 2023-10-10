<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SettingsController extends Controller
{
   public function profile(){  
   	$userId = auth()->user()->id;
   	$user = User::where('id', $userId)->first();
   	$response = [
    		'status' => 'success',
    		'user' => $user,
    	];
		return response($response, 200);
   }

   public function updateProfile(Request $request){
      $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email',
        ]);
     $userId = auth()->user()->id;
     $profile = User::where('id', $userId)->first(); 
     if($profile){
     	$profile->name = $request->name;
     	$profile->email = $request->email;
     	// $profile->username = $request->username;
     	$user = $profile->save();
     	$response = [
    		'status' => 'success',
    		'message' => 'Profile Updated',
    		
    	];
		return response($response, 200);
     }else{
     	$response = [
    		'status' => 'error',
    		'message' => 'Profile Not Found',
    	];
		return response($response, 200);
     }
   }

   public function changePassword(Request $request){ 
   	$this->validate($request, [
            'current_password' => 'required',
            'password' => 'required|string|confirmed'
        ]);
   	        $user = auth()->user();
        if (Hash::check($request->current_password, $user->password)) {
            $password = Hash::make($request->password);
            $user->password = $password;
            $user->save();
            $response = [
    		'status' => 'success',
    		'message' => 'Password changes successfully',
    	];
		return response($response, 200);
        }else{
        	$response = [
    		'status' => 'error',
    		'message' => 'Password doesn\'t match the old password we have with us!',
    	];
		return response($response, 200);
        }

   }

public function currency(){
  $userId = auth()->user()->id;
    $setting = Setting::where('user_id', $userId)->first();
    $response = [
        'status' => 'success',
        'currency' => $setting->currency,
      ];
    return response($response, 200);
}

public function updateCurrency(Request $request){
  $userId = auth()->user()->id;
  $setting = Setting::where('user_id', $userId)->first();
  $setting->currency = $request->currency;
  $setting->save();
  $response = [
        'status' => 'success',
        'message' => 'Updated',
      ];
    return response($response, 200);
}

public function lang(){
  $userId = auth()->user()->id;
    $setting = Setting::where('user_id', $userId)->first();
    $response = [
        'status' => 'success',
        'lang' => $setting->lang,
      ];
    return response($response, 200);
}

public function updateLang(Request $request){
  $userId = auth()->user()->id;
  $setting = Setting::where('user_id', $userId)->first();
  $setting->lang = $request->lang;
  $setting->save();
  $response = [
        'status' => 'success',
        'message' => 'Updated',
      ];
    return response($response, 200);
}

public function users(Request $request){
$user = User::query();
if ($request->filled('search')) {
            $user->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $user = $user->paginate('15');
   $response = [
        'status' => 'success',
        'users' => $user,
      ];
    return response($response, 200);

}

public function EditUser($id){
      $user = User::where('id',$id)->first();
      $response = [
        'status' => 'success',
        'user' => $user,
      ];
    return response($response, 200);
    }

 public function updatedUser(Request $request, $id){
   $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email',
            'username' => 'required|min:5',
        ]);
     
     $profile = User::where('id', $id)->first(); 
     if($profile){
      if($request->password){
       $profile->password = $request->password; 
      }
      $profile->name = $request->name;
      $profile->email = $request->email;
      $profile->username = $request->username;
      $user = $profile->save();
      $response = [
        'status' => 'success',
        'message' => 'User Data Updated',
        
      ];
    return response($response, 200);
     }else{
      $response = [
        'status' => 'error',
        'message' => 'User Not Found',
      ];
    return response($response, 200);
     }
 }

 public function deleteUser($id)
{
  User::where('id', $id)->delete();
  $response = [
        'status' => 'success',
        'message' => 'User Deleted',
      ];
    return response($response, 200);

} 

public function getProfilePic(){
$userId = auth()->user()->id;
$user = User::where('id', $userId)->first();
  $response = [
        'status' => 'success',
        'user_pic' => url('public/images/profile').'/'.$user->user_pic,
      ];
    return response($response, 200);
}

public function profileImage(Request $request){
 $validator = Validator::make($request->all(), [
               'user_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                  ]);

                if ($validator->fails()) {
                  $response = [
        'status' => 'error',
        'message' => 'Image parameter are not meant',
      ];
    return response($response, 401);
                }

    $userId = auth()->user()->id;
    $user = User::where('id', $userId)->first();

    if($request->hasFile('user_image')){
      $file = $request->user_image;
      $filename = time().'.'.$file->getClientOriginalExtension();
      $file->move(public_path('images/profile'), $filename);
      $user->user_pic = $filename;
      $user->save();
       $response = [
        'status' => 'success',
        'message' => 'Profile Picture Updated',
      ];
       return redirect()->back();
  } 
}

public function settings(){
 $userId = auth()->user()->id;
    $setting = Setting::where('user_id', $userId)->first();
    $response = [
        'status' => 'success',
        'settings' => $setting,
      ];
    return response($response, 200); 
}  

public function updateSetting(Request $request){
 $userId = auth()->user()->id;
  $setting = Setting::where('user_id', $userId)->first();
  if($request->newsletter){
    $setting->newsletter = $request->newsletter;
  }

  if($request->txt_sms){
    $setting->txt_sms = $request->txt_sms;
  }

  if($request->preference){
    $setting->preference = $request->preference;
  } 

  if($request->secure1){
    $setting->secure1 = $request->secure1;
  } 

  if($request->secure2){
    $setting->secure2 = $request->secure2;
  }

  $setting->save();
  $response = [
        'status' => 'success',
        'settings' => 'Updated',
      ];
    return response($response, 200);    
}

}
