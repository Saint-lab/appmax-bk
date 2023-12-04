<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\User;
use Kutia\Larafirebase\Facades\Larafirebase;
use App\Notifications\SendPushNotification;
use Stripe;
use Notification;
use DB;



class PaymentController extends Controller
{
 public function __construct()
    {
        header("Access-Control-Allow-Origin: *");
        
    }

 public function getStripeKeys(){
    $userId = auth()->user()->id;
    $stripe = DB::table('payment_gateway')->where('user_id', $userId)->where('type', 'stripe')->first();
    if($stripe){
        $response = [
        'status' => 'success',
        'stripe' => $stripe,
         ];
        return response($response, 200);
    }else{
        $response = [
        'status' => 'error',
        'message' => 'Stripe Account not found',
         ];
        return response($response, 401);
    }
 }

 public function postStripeKeys(Request $request)
 {
    $validator = Validator::make($request->all(), [
            'public_key' => 'required|string',
            'secret_key' => 'required|string',
         ]);
    if ($validator->fails()) {
        $response = [
        'status' => 'error',
        'message' => $validator->errors(),
      ];
    return response($response, 401);
                }

  $userId = auth()->user()->id;              
$stripe = DB::table('payment_gateway')->insert([
              'user_id'=>  $userId,
               'type' => 'stripe',
               'public_key' => $request->public_key,
               'secret_key' => $request->secret_key,
              ]);
     
         $response = [
        'status' => 'success',
        'message' => 'Stripe Account Added',
        'stripe' => $stripe,
      ];
    return response($response, 200);


 }



    public function stripe()
    {
        return view('stripe');
    }

    public function stripePaymentPost(Request $request)
    {      
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'stripeToken' => 'required|string',
         ]);
    if ($validator->fails()) {
        $response = [
        'status' => 'error',
        'message' => $validator->errors(),
      ];
    return response($response, 401);
  }
        $amount = $request->amount ?? 1;
        $cur = $request->cur ?? 'USD';
        $des = $request->description ?? 'Stripe Payment';
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        Stripe\Charge::create ([
                "amount" => $amount*100,
                "currency" => $cur,
                "source" => $request->stripeToken,
                "description" => $des,
        ]);
         
          $pay = DB::table('payment_history')->insert([
              'user_id'=>  auth()->user()->id,
               'type' => 'stripe',
               'trans_id' => Str::random(16),
               'amount' => $request->amount,
               'description' => $request->description,
               'created_on' => date(),
               'status' => 1,
              ]);
          if($pay){
            $response = [
        'status' => 'success',
        'transection' => $pay,
      ];
    return response($response, 200);
          }
        //Session::flash('success', 'Payment Successful !');
        //return back();
    }

public function getPaypayEmail(){
     $userId = auth()->user()->id;
    $paypal = DB::table('payment_gateway')->where('user_id', $userId)->where('type', 'paypal')->first();
    if($paypal){
        $response = [
        'status' => 'success',
        'paypal_email' => $paypal->email,
         ];
        return response($response, 200);
    }else{
        $response = [
        'status' => 'error',
        'message' => 'Paypal Account not found',
         ];
        return response($response, 401);
    }
}

public function postPaypalEmail(Request $request){
     $validator = Validator::make($request->all(), [
            'paypal_email' => 'required|string',
         ]);
    if ($validator->fails()) {
        $response = [
        'status' => 'error',
        'message' => $validator->errors(),
      ];
    return response($response, 401);
  }

$userId = auth()->user()->id;              
 $pay = DB::table('payment_gateway')->insert([
              'user_id'=>  $userId,
               'type' => 'paypal',
               'email' => $request->paypal_email,
              ]);
     
         $response = [
        'status' => 'success',
        'message' => 'Stripe Account Added',
        'paypal_email' => $pay->email,
      ];
    return response($response, 200);
}

public function payWithPaypal(Request $request)
{
    $pay = DB::table('payment_history')->insert([
              'user_id'=>  auth()->user()->id,
               'type' => 'paypal',
               'trans_id' => Str::random(16),
               'amount' => $request->amount,
               'description' => $request->description,
              ]);
          if($pay){
            $response = [
        'status' => 'success',
        'transection' => $pay,
      ];
    return response($response, 200);
          }
}



public function stripePost(Request $request)
    {      
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'stripeToken' => 'required|string',
         ]);
    if ($validator->fails()) {
       Session::flash('error', $validator->errors());
  }
        $amount = $request->amount ?? 1;
        $cur = $request->cur ?? 'USD';
        $des = $request->description ?? 'Stripe Payment';
        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        Stripe\Charge::create ([
                "amount" => $amount*100,
                "currency" => $cur,
                "source" => $request->stripeToken,
                "description" => $des,
        ]);
         
          $pay = DB::table('payment_history')->insert([
              'user_id'=>  4,
               'type' => 'stripe',
               'trans_id' => Str::random(16),
               'amount' => $request->amount,
               'description' => $request->description,
               'created_on' => date(),
               'status' => 1,
              ]);
          if($pay){
           Session::flash('success', 'Payment Successful !');
        return back(); 
          }
        
    }

    public function updateToken(Request $request){
    try{
        $usr = User::where('id', auth()->user()->id)->first();
        $usr->fcm_token = $request->pin;
        $usr->save();
        // $request->user()->update(['fcm_token'=>$request->token]);
        $response = [
        'status' => 'success',
        'message' => 'Notification Enable',
      ];
      return response($response, 200);
    }catch(\Exception $e){
        report($e);
       $response = [
        'status' => 'error',
        'message' => $e,
      ];
      return response($response, 401);
    }
}

public function indexN(){
    return view('home');
}

public function sendNotification(Request $request)
    {
       try{

        $fcmTokens = User::where('id', auth()->user()->id)->whereNotNull('fcm_token')->pluck('fcm_token')->toArray();

     // Notification::send(null,new SendPushNotification($request->title,$request->message,$fcmTokens));

        // Larafirebase::withTitle($request->title)
        //     ->withBody($request->message)
        //     ->sendMessage($fcmTokens);
       
        //return redirect()->back()->with('success','Notification Sent Successfully!!');

    $SERVER_API_KEY = 'AAAAIlZc8sU:APA91bGtRAgp8FwZSa3cQewcmi7XkRIbvs2z5p9xcasNC8BNuBgo7ZpHkxm2oyX8AfE1L86QkUCzgZZt0vd0N-PJCTnLl63bGTjkrEXYvJ8kr5dKLWowbMeF9XTqhupBBIpyixlmGY5l'; 
  
        $data = [
            "registration_ids" => $fcmTokens,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,  
            ]
        ];
        $dataString = json_encode($data);
    
        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json',
        ];
    
        $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);
               
        $response = curl_exec($ch);
  
        $response = [
        'status' => 'success',
        'message' => 'Notification Sent',
      ];
      return response($response, 200);

        }catch(\Exception $e){
        report($e);
       $response = [
        'status' => 'error',
        'message' => $e,
      ];
      return response($response, 401);
    }

    }
public function pushNotificationSettings(Request $request)
{
        $validator = Validator::make($request->all(), [
            'apiKey' => 'required',
            'authDomain' => 'required|string',
            'databaseURL' => 'required|string',
            'projectId' => 'required',
            'storageBucket' => 'required|string',
            'messagingSenderId' => 'required',
            'appId' => 'required',
            'measurementId' => 'required',
            'server_key' => 'required|string',
         ]);
    if ($validator->fails()) {
        $response = [
        'status' => 'error',
        'message' => $validator->errors(),
      ];
    return response($response, 401);
  }

   DB::table('push_notification')->insert([
              'user_id'=>  auth()->user()->id,
               'apiKey' => $request->apiKey,
               'authDomain' => $request->authDomain,
               'databaseURL' => $request->databaseURL,
               'projectId' => $request->projectId,
               'storageBucket' => $request->storageBucket,
               'messagingSenderId' => $request->messagingSenderId,
               'appId' => $request->appId,
               'measurementId' => $request->measurementId,
               'server_key' => $request->server_key,
              ]);

   $response = [
        'status' => 'success',
        'message' => 'saved',
      ];
      return response($response, 200);

}

public function getPushNotificationSettings(){
  $fb = DB::table('push_notification')->where('user_id', auth()->user()->id)->first();
  if($fb){
    $response = [
        'status' => 'success',
        'firebase' => $fb,
      ];
      return response($response, 200);

  }else{
    $response = [
        'status' => 'error',
        'message' => 'Settings not found',
      ];
      return response($response, 401);
  }
}


}