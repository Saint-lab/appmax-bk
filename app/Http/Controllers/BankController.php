<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BankController extends Controller
{
    public function bank()
    {
    	$userId = auth()->user()->id;
   	$banks = Bank::where('user_id', $userId)->get();
   	$response = [
    		'status' => 'success',
    		'banks' => $banks,
    	];
		return response($response, 200);
    }

 public function addBank(Request $request)
 {
 	$this->validate($request, [
            'bank' => 'required|string',
            'account_number' => 'required|number|max:10',
            'account_name' => 'required|string',
        ]);
 	$userId = auth()->user()->id;
   $bank = Bank::create([
        'user_id' => $userId,
        'bank' => $request->bank,
        'acc_num' => $request->account_number,
        'acc_name' => $request->account_name
       ]);
   $response = [
    		'status' => 'success',
    		'bank' => $bank,
    	];
		return response($response, 200);
 }

 public function editBank($id)
    {
   	$bank = Bank::where('id', $id)->first();
   	if($bank){
   	$response = [
    		'status' => 'success',
    		'banks' => $bank,
    	];
		return response($response, 200);
	}else{
		$response = [
    		'status' => 'error',
    		'message' => 'Bank not Found',
    	];
		return response($response, 200);
	 }
	}
  
  public function updateBank(Request $request, $id)
 {
 	$this->validate($request, [
            'bank' => 'required|string',
            'account_number' => 'required|number|max:10',
            'account_name' => 'required|string',
        ]);
 	$bank = Bank::where('id', $id)->first();
      if($bank){
        $bank->bank = $request->bank,
        $bank->acc_num = $request->account_number,
        $bank->acc_name = $request->account_name
        $bank->save();
$response = [
    		'status' => 'success',
    		'message' => 'Updated',
    	];
		return response($response, 200);
	}
	else{
		$response = [
    		'status' => 'error',
    		'message' => 'Bank not Found',
    	];
		return response($response, 200);
	}
 }  

public function deleteBank($id)
{
  Bank::where('id', $id)->delete();
  $response = [
    		'status' => 'success',
    		'message' => 'Deleted',
    	];
		return response($response, 200);

}






}
