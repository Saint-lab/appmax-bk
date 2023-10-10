<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingsController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

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



Route::get('/sample', function () {
    return view('welcome'); 
});

Route::post('/sample', function (Request $request) {
	$validator = Validator::make($request->all(), [
               'user_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                  ]);

                if ($validator->fails()) {
                  
                    Session::flash('error',"Unsuccessful");
                     return redirect()->back();
                }
               
	
   $userId = 4;
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
});
