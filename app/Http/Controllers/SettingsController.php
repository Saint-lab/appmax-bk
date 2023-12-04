<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use DB;

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
        'message' => $validator->errors(),
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
        'user_pic' => url('public/images/profile').'/'.$user->user_pic,
      ];
       return redirect()->back();
  } 
}

public function settings(){
 $userId = auth()->user()->id;
    $setting = Settings::where('user_id', $userId)->first();
    $response = [
        'status' => 'success',
        'settings' => $setting,
      ];
    return response($response, 200); 
}  

// public function updateSetting(Request $request){
//  $userId = auth()->user()->id;
//   $setting = Setting::where('user_id', $userId)->first();
//   if($request->newsletter){
//     $setting->newsletter = $request->newsletter;
//   }

//   if($request->txt_sms){
//     $setting->txt_sms = $request->txt_sms;
//   }

//   if($request->preference){
//     $setting->preference = $request->preference;
//   } 

//   if($request->secure1){
//     $setting->secure1 = $request->secure1;
//   } 

//   if($request->secure2){
//     $setting->secure2 = $request->secure2;
//   }

//   $setting->save();
//   $response = [
//         'status' => 'success',
//         'settings' => 'Updated',
//       ];
//     return response($response, 200);    
// }

public function getTwilio(){
  $userId = auth()->user()->id;
        $twilio = Settings::where('user_id', $userId)->first();
        if($twilio){
        $response = [
        'status' => 'success',
        'twilio_number' => $twilio->twilio_number,
        'twilio_sid' => $twilio->twilio_sid,
        'twilio_token' => $twilio->twilio_token,
      ];
    return response($response, 200);
  }else{
    $response = [
        'status' => 'error',
        'message' => 'User Settings not found',
      ];
    return response($response, 401);
    }
  }

  public function postTwilioSetting(Request $request){
    $validator = Validator::make($request->all(), [
               'twilio_number' => 'required',
               'twilio_sid' => 'required',
               'twilio_token' => 'required',
                  ]);

                if ($validator->fails()) {
                  $response = [
        'status' => 'error',
        'message' => $validator->errors(),
      ];
    return response($response, 401);
     }
    $userId = auth()->user()->id;
    $setting = Settings::where('user_id', $userId)->first();
     $setting->twilio_number = $request->twilio_number;
     $setting->twilio_sid = $request->twilio_sid;
     $setting->twilio_token = $request->twilio_token;
     $setting->save();
     $response = [
        'status' => 'success',
        'message' => 'save successfully',
      ];
    return response($response, 200);
  }


public function addOptinEmail($account){ 
        if ($account === "mailchimp") {
         return   $this->loadMailchimp();
        } elseif($account === "aweber") {
        return  $this->loadAweber();
         }
        elseif($account === "getresponse") {
        return $this->loadGetresponse();
        }
        
    }

public function loadMailchimp(){ 
      //User-Specific configuration
      
      $userId = auth()->user()->id;
      $mailchimpOauth = DB::table('responder_table')->where('type', 'mailchimp')->first();
     $redirect_uri = action([SettingsController::class, 'authorizeMailch']);
    
      //Mailchimp standard OAuth2 configuration
      $authorize_uri = "https://login.mailchimp.com/oauth2/authorize";
    
      return redirect()->away("$authorize_uri?response_type=code&client_id={$mailchimpOauth->clicnt_id}&redirect_uri=$redirect_uri");
    }

    public function loadAweber(){ 
     $userId = auth()->user()->id;
      //User-Specific configuration
      $aweberOauth = DB::table('responder_table')->where('type', 'aweber')->first();
      $redirect_uri = action([SettingsController::class, 'authorizeApp']);
    //aweber standard OAuth2 configuration
      $OAUTH_URL = "https://auth.aweber.com/oauth2/authorize";
      $scope = array('account.read', 'list.read', 'list.write', 'subscriber.read', 'subscriber.write', 'email.read', 'email.write');
        
       return redirect("$OAUTH_URL?response_type=code&client_id={$aweberOauth->clicnt_id}&redirect_uri={$redirect_uri}&scope=account.read+list.read+list.write+subscriber.read+subscriber.write+email.read+email.write");
    }

public function authorizeApp(){
        $code = $_GET['code'];
        return $this->aweber_oauth2_token($code);
     }
     public function authorizeMailch(){
        $code = $_GET['code'];
       return $this->authorizeMailchimp($code);
     }

       public function authorizeMailchimp($code){
       $userId = auth()->user()->id;
      $mailchimpOauth = DB::table('responder_table')->where('type', 'mailchimp')->first();
    
      $access_token_uri = "https://login.mailchimp.com/oauth2/token";
      $base_uri = "https://login.mailchimp.com/oauth2/";
      $metadata_uri = "https://login.mailchimp.com/oauth2/metadata";
    
      $postData = array(
        "grant_type" => "authorization_code",
        "client_id" => $mailchimpOauth->api_client_id,
        "client_secret" => $mailchimpOauth->api_client_key,
        "code" => $code,
        "redirect_uri" => action([SettingsController::class, 'authorizeMailch']),
      );
    
      $response = $this->makeRequest($access_token_uri,$postData,1);
      if ($response) {
        $metadata = $this->makeRequest($metadata_uri,"",0,$response->access_token);
        if ($metadata) {
           DB::table('optin_email_accounts')->insert([
              'user_id'=>  $userId,
               'type' => "mailchimp",
               'name' => $metadata->accountname,
               'access_token' => $response->access_token,
               'api_endpoint' => $metadata->api_endpoint,
              ]);
           $response = [
        'status' => 'success',
        'message' => 'Mailchimp Account Added',
         ];
        return response($response, 200);
       
        }
      }
    }

     public function aweber_oauth2_token($code) {
      $userId = auth()->user()->id;
      $aweberOauth = DB::table('responder_table')->where('type', 'aweber')->first();
       $client_id = $aweberOauth->api_client_id;
       $client_secret = $aweberOauth->api_client_key;
       $redirect_uri = action([SettingsController::class, 'authorizeApp']);
      $meta = "https://api.aweber.com/1.0/accounts";
      $oauth2token_url = "https://auth.aweber.com/oauth2/token";
      $clienttoken_post = array(
      "code" => $code,
      "client_id" => $client_id,
      "client_secret" => $client_secret,
      "redirect_uri" => $redirect_uri,
      "grant_type" => "authorization_code"
      );
      
     $response = $this->getRequest($oauth2token_url,$clienttoken_post,1);
        if($response){
            $token = $response['access_token'];
        $refreshToken = $response['refresh_token'];
        $ex = $response['expires_in'];
         $clienttoken_refresh = array(
      "refresh_token" => $refreshToken,
      "client_id" => $client_id,
      "client_secret" => $client_secret,
      "grant_type" => "refresh_token"
      );
        $r = $this->getRequest($oauth2token_url,$clienttoken_refresh,1);
        if($r){
         $met = $this->getRequest($meta, "", 0, $r['access_token']);
        if($met){
        $endpoint = $met['entries'][0]['lists_collection_link'];
         DB::table('optin_email_accounts')->insert([
              'user_id'=>  $userId,
               'type' => "aweber",
               'name' => "User",
               'access_token' => $response['access_token'],
               'api_endpoint' => $endpoint,
               'refresh_token' => $refreshToken,
               'expire_time'   => $ex,
              ]);
       $response = [
        'status' => 'success',
        'message' => 'Aweber Account Added',
         ];
        return response($response, 200);
       
    
            }
          }
        }
      }

public function getresponseApiKey(Request $request)
{
  $validator = Validator::make($request->all(), [
            'api_key' => 'required|string',
         ]);

        if ($validator->fails()) {
                  $response = [
        'status' => 'error',
        'message' => $validator->errors(),
      ];
    return response($response, 401);
                }

     $key = $request->api_key;
     $userId = auth()->user()->id;
     $type = "getresponse";
     DB::table('optin_email_accounts')->insert([
              'user_id'=>  $userId,
               'type' => $type,
               'name' => "User",
               'access_token' => $key,
              ]);
     
         $response = [
        'status' => 'success',
        'message' => 'Getresponse Account Added',
      ];
    return response($response, 200);
  }




      public function optInAccountLists($id){
        
      $api = "/3.0/lists";
      $userId = auth()->user()->id;
      $count = DB::table('optin_email_accounts')->where('type', $id)->where('user_id', $userId)->count();
      if ($count > 0) {
        $account = DB::table('optin_email_accounts')->where('type', $id)->where('user_id', $userId)->first();
        if($account->type == "mailchimp"){
        $access_token = $account->access_token;
        $api_endpoint = $account->api_endpoint;
        $url = $api_endpoint.$api;
         $response = [
        'status' => 'success',
        'type' => $id,
        'mailchimp' => $this->makeRequest($url,"",0,$access_token),
         ];
        return response($response, 200);
        }
        elseif($account->type == "aweber"){
        $aweberOauth = DB::table('responder_table')->where('type', 'aweber')->first();
       $client_id = $aweberOauth->api_client_id;
       $client_secret = $aweberOauth->api_client_key;
       $redirect_uri = action([SettingsController::class, 'authorizeApp']);
        $oauth2token_url = "https://auth.aweber.com/oauth2/token";
         $access_token = $account->access_token;
        $api_endpoint = $account->api_endpoint; 
         $clienttoken_refresh = array(
      "refresh_token" => $account->refresh_token,
      "client_id" => $client_id,
      "client_secret" => $client_secret,
      "redirect_uri" => $redirect_uri,
      "grant_type" => "refresh_token"
      );
      $r = $this->getRequest($oauth2token_url,$clienttoken_refresh,1);
        if($r){
        $response = [
        'status' => 'success',
        'type' => $id,
        'aweber' => $this->getRequest($api_endpoint, "", 0, $r['access_token']),
         ];
        return response($response, 200);
       
        }
        }
        elseif($account->type == "getresponse"){
         $key = $account->access_token;
         $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.getresponse.com/v3/campaigns",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false, //<--- Added this.
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_HTTPHEADER => array(
        "X-Auth-Token: api-key $key" //<---Change type
        )
      )); 
      $response = curl_exec($curl);
      $err = curl_error($curl);
      curl_close($curl);
      if ($err) {
        echo "cURL Error #:" . $err;
      } else {
      $res = json_decode($response, true);
      $response = [
        'status' => 'success',
        'type' => $id,
        'getresponse' => $res,
         ];
        return response($response, 200);
    
     
      }   
        }
      }
    }

// public function deleteOptinEmail($account,$type){

//         if ($account === "mailchimp") { 
//          return $this->deleteMailchimp($type);
//         } elseif($account === "aweber") {
//          return $this->deleteAweber($type);
//         }
//         elseif($account === "getresponse") {
//          return $this->deleteGetresponse($type);
//         }
//         elseif($account === "convertkit") {
//          return $this->deleteConvertKit($type);
//         }
//     }

public function deleteOptinEmail($type){
      $userId = auth()->user()->id;
      $find = DB::table('optin_email_accounts')->where('type', $type)->where('user_id', $userId)->first();
      if($find){
        $find->delete();
        $response = [
        'status' => 'success',
        ];
        return response($response, 200);
      }else{
        $response = [
        'status' => 'error',
        'message' => 'Account do not exist',
        ];
        return response($response, 401);
      }
      }
    
    









      public function makeRequest($url,$data,$isPost="",$isHeader=""){
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url);
      if ($isPost === 1){
        curl_setopt($ch, CURLOPT_POST, 1);
      }
      if (!empty($data)){
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
      }
      if (!empty($isHeader)){
        $header = array(
         "Accept: application/json",
         "Authorization: OAuth $isHeader"
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
      }
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
      $response = json_decode(curl_exec($ch));
      curl_close ($ch);
      return $response;
    }
    
    public function getRequest($url, $data, $header, $token=""){
      if($header == 0){
          $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false, //<--- Added this.
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
       CURLOPT_HTTPHEADER =>  array(
          "Content-Type: application/json",
          "Authorization: Bearer $token"
         )
      ));
      }
      else{
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false, //<--- Added this.
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",  //<---You may also try "CURLOPT_POST => 1" instead.
        CURLOPT_POSTFIELDS => http_build_query($data), //<--Makes your array into application/x-www-form-urlencoded format.
        CURLOPT_HTTPHEADER => array(
          "application/x-www-form-urlencoded" //<---Change type
        )
      ));
    }
    
      $response = curl_exec($curl);
      $err = curl_error($curl);
      curl_close($curl);
      if ($err) {
        echo "cURL Error #:" . $err;
      } else {
      return  json_decode($response, true);
      }
    }



}
