<?php

namespace App\Http\Controllers;

use App\Models\UsersPhoneNumber;
use Illuminate\Http\Request;
use Twilio\Rest\Client;

class SmsController extends Controller
{
    
	/**
     * Show the forms with users phone number details.
     *
     * @return Response
     */
    public function show()
    {
        $users = UsersPhoneNumber::all();
        return view('numbers', compact("users"));
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
        $user_phone_number_model->save();
        $this->sendMessage("User registration successful!", $request->phone_number);
        return back()->with(['success' => "{$request->phone_number} registered"]);
    }
    /**
     * Send message to a selected users
     */
    public function sendCustomMessage(Request $request)
    {
        $validatedData = $request->validate([
            'users' => 'required|array',
            'body' => 'required',
        ]);
        $recipients = $validatedData["users"];
        // iterate over the array of recipients and send a twilio request for each
        foreach ($recipients as $recipient) {
            $this->sendMessage($validatedData["body"], $recipient);
        }
        return back()->with(['success' => "Messages on their way!"]);
    }
    /**
     * Sends sms to user using Twilio's programmable sms client
     * @param String $message Body of sms
     * @param Number $recipients Number of recipient
     */
    private function sendMessage($message, $recipients)
    {
        $account_sid = 'AC38f74fc8c8a5b4c61af190947c6ce627';
        $auth_token = '87f62047149b7e5ae3702e1e89a297ac';
        $twilio_number = '+16627361070';
        $client = new Client($account_sid, $auth_token);
        $client->messages->create($recipients, ['from' => $twilio_number, 'body' => "User registration successful!"]);
    }

}
