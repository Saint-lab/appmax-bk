<?php

namespace App\Http\Controllers;

use App\Models\UsersPhoneNumber;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;

class SendSmsMessageController extends Controller
{
    
	/**
     * Show the forms with users phone number details.
     *
     * @return Response
     */
    public function show()
    {
    	$userId = auth()->user()->id;
        $numbers = UsersPhoneNumber::where('user_id', $userId)->get();
        $response = [
        'status' => 'success',
        'numbers' => $numbers,
      ];
    return response($response, 200);
        //return view('numbers', compact("users"));
    }
    /**
     * Store a new user phone number.
     *
     * @param  Request  $request
     * @return Response
     */
    public function storePhoneNumber(Request $request)
    {
        //run validation on data sent in
        
         $validator = Validator::make($request->all(), [
               'phone_number' => 'required|numeric',
                  ]);
		if ($validator->fails()) {
                  $response = [
        'status' => 'error',
        'message' => $validator->errors(),
      ];
    return response($response, 401);
    }

        $user_phone_number_model = new UsersPhoneNumber(
         [
         	'user_id' => auth()->user()->id,
         	'phone_number' => $request->phone_number,
         	'status' => 1,
         ]
        );
        $user_phone_number_model->save();
        
        $response = [
        'status' => 'success',
        'message' => 'Save successful',
      ];
    return response($response, 200);
    }
    /**
     * Send message to a selected users
     */
    public function sendCustomMessage(Request $request)
    {
    	$validator = Validator::make($request->all(), [
               'numbers' => 'required|array',
               'body' => 'required',
                  ]);
		if ($validator->fails()) {
                  $response = [
        'status' => 'error',
        'message' => $validator->errors(),
      ];
    return response($response, 401);
    }
    
        $recipients = $request->numbers;
        // iterate over the array of recipients and send a twilio request for each
        foreach ($recipients as $recipient) {
            $this->sendMessage($request->body, $recipient);
        }
        $response = [
        'status' => 'success',
        'message' => 'Message sent successfully',
      ];
    return response($response, 200);
       
    }
    /**
     * Sends sms to user using Twilio's programmable sms client
     * @param String $message Body of sms
     * @param Number $recipients Number of recipient
     */
    private function sendMessage($message, $recipients)
    {	
    	$userId = auth()->user()->id;
    	$twilioSettings = Settings::where('user_id', $userId)->first();
        $account_sid = $twilioSettings->twilio_sid;
        $auth_token = $twilioSettings->twilio_token;
        $twilio_number = $twilioSettings->twilio_number;
        $client = new Client($account_sid, $auth_token);
        $client->messages->create($recipients, ['from' => $twilio_number, 'body' => $message]);
    }

}
