<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SettingsController;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UsersPhoneNumber;
use Illuminate\Support\Facades\Validator;
 use Twilio\Rest\Client;



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

// Route::get('/addnumber', 'SmsController@show');
 //Route::post('/savenumber', 'SmsController@storePhoneNumber');
// Route::post('/custom-number', 'SmsController@sendCustomMessage');
Route::get('/addnumber', function () {

   $users = UsersPhoneNumber::all();
        return view('numbers', compact("users"));
});

Route::post('/savenumber', function (Request $request) {
      $validatedData = $request->validate([
            'phone_number' => 'required|numeric',
        ]);
        $user_phone_number_model = new UsersPhoneNumber(
         [
          'user_id' => 4,
          'phone_number' => $request->phone_number,
          'status' => 1,
         ]
        );
        $message = "User registration successful!";
        $user_phone_number_model->save();
        $account_sid = 'AC38f74fc8c8a5b4c61af190947c6ce627';
        $auth_token = '87f62047149b7e5ae3702e1e89a297ac';
        $twilio_number = '+16627361070';
        $client = new Client($account_sid, $auth_token);
        $client->messages->create($request->phone_number, ['from' => $twilio_number, 'body' => $message]);
        
        return back()->with(['success' => "{$request->phone_number} registered"]);
  });



Route::get('stripe', [PaymentController::class, 'stripe']);
Route::post('stripe', [PaymentController::class, 'stripePost'])->name('stripe.post');

Route::get('/home', [PaymentController::class, 'indexN'])->name('home');
Route::post('/fcm-token', [PaymentController::class, 'updateToken'])->name('fcmToken');
Route::post('/send-notification',[PaymentController::class,'sendNotification'])->name('send.notification');

Route::get('/sample', function () {
    return view('welcome'); 
});

Route::post('/sample', function (Request $request) {
	$validator = Validator::make($request->all(), [
               'user_image' => 'required',
               'user_image.*' => 'image|mimes:jpeg,png,jpg,gif|max:20048',
                  ]);

                if ($validator->fails()) {
                  dd($validator->errors());
                     return redirect()->back();
                }
               
	
   $userId = 4;
    $user = User::where('id', $userId)->first();

    if($request->hasFile('user_image')){
       $arr = array();
      
      $images = $request->user_image;
      foreach ($images as $img) {
        $file = $img;
      $filename = time().'.'.$file->getClientOriginalExtension();
      $file->move(public_path('images/product/4/'), $filename);
       $arr[] = $filename;
      }
      
      $user->user_pic = json_encode($arr);
      $user->save();
       $response = [
        'status' => 'success',
        'message' => 'Profile Picture Updated',
      ];
       return redirect()->back();
  }
});
